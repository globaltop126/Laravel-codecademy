<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoiceProduct;
use App\Mail\CustomInvoiceSend;
use App\Mail\InvoiceSend;
use App\Mail\PaymentReminder;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectUser;
use App\Models\Tax;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Stripe;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoiceExport;
use App\Imports\InvoiceImport;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usr             = Auth::user();
        $user_projects   = $usr->projects()->pluck('project_id')->toArray();
        $send_invoice    = Invoice::whereIn('project_id', $user_projects)->where('created_by', '=', $usr->id)->get();
        $receive_invoice = Invoice::whereIn('project_id', $user_projects)->where('client_id', '=', $usr->id)->get();

        return view('invoices.index', compact('send_invoice', 'receive_invoice'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $projects = Auth::user()->projects()->where('permission', 'owner')->get();

        return view('invoices.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'project_id' => 'required',
                               'client_id' => 'required',
                               'due_date' => 'required',
                               'tax_id' => 'required',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->route('invoices.index')->with('error', $messages->first());
        }

        $invoice             = new Invoice();
        $invoice->invoice_id = $this->invoiceNumber();
        $invoice->project_id = $request->project_id;
        $invoice->client_id  = $request->client_id;
        $invoice->due_date   = $request->due_date;
        $invoice->tax_id     = $request->tax_id;
        $invoice->created_by = Auth::user()->id;
        $invoice->save();

        $settings  = Utility::settingsById(Auth::user()->id);
    
        if(isset($settings['invoice_notificaation']) && $settings['invoice_notificaation'] == 1){
            $msg = "New Invoice created by the ".\Auth::user()->name.'.';
           
            Utility::send_slack_msg($msg);    
        }

        if(isset($settings['telegram_invoice_notificaation']) && $settings['telegram_invoice_notificaation'] == 1){
                    $resp = "New Invoice created by the ".\Auth::user()->name.'.';
                    Utility::send_telegram_msg($resp);    
                }

        return redirect()->route('invoices.index')->with('success', __('Invoice successfully created!'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        $usr            = Auth::user();
        $left_address   = $usr->decodeDetails();
       
        $creator_detail = $usr->decodeDetails($invoice->created_by);
        $paymentSetting = Utility::getPaymentSetting($invoice->created_by);

        if($invoice->client_id == $usr->id)
        {
            $right_address = $usr->decodeDetails($invoice->created_by);
        }
        else
        {
            $right_address = $usr->decodeDetails($invoice->client_id);
        }

        return view('invoices.show', compact('invoice', 'left_address', 'right_address', 'creator_detail', 'paymentSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        if(Auth::user()->id == $invoice->created_by)
        {
            $projects = Auth::user()->projects()->where('permission', 'owner')->get();

            return view('invoices.edit', compact('invoice', 'projects'));
        }
        else
        {
            return redirect()->route('invoices.index')->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        if(Auth::user()->id == $invoice->created_by)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'project_id' => 'required',
                                   'client_id' => 'required',
                                   'due_date' => 'required',
                                   'tax_id' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('invoices.index')->with('error', $messages->first());
            }

            $invoice->project_id = $request->project_id;
            $invoice->client_id  = $request->client_id;
            $invoice->due_date   = $request->due_date;
            $invoice->tax_id     = $request->tax_id;
            $invoice->save();

            return redirect()->back()->with('success', __('Invoice successfully updated!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        if($invoice->created_by == Auth::user()->id)
        {
            InvoicePayment::where('invoice_id', '=', $invoice->id)->delete();
            InvoiceProduct::where('invoice_id', '=', $invoice->id)->delete();
            $invoice->delete();

            return redirect()->route('invoices.index')->with('success', __('Invoice successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // get invoice number
    function invoiceNumber()
    {
        $latest = Invoice::where('created_by', '=', Auth::user()->id)->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->invoice_id + 1;
    }

    // project wise load client
    function jsonClient(Request $request)
    {
        $client_user = ProjectUser::where('project_id', '=', $request->project_id)->where('permission', 'client')->pluck('user_id')->toArray();
        $clients     = User::whereIn('id', array_unique($client_user))->get()->pluck('name', 'id');

        return response()->json($clients, 200);
    }

    public function productAdd($id)
    {
        $usr     = Auth::user();
        $invoice = Invoice::find($id);

        if($invoice->created_by == $usr->id)
        {
            $tasks = ProjectTask::where('project_id', '=', $invoice->project_id)->pluck('name')->toArray();

            return view('invoices.item', compact('invoice', 'tasks'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function productStore($id, Request $request)
    {
        $usr     = Auth::user();
        $invoice = Invoice::find($id);
        if($invoice->created_by == $usr->id)
        {
            $validate = [];
            if($invoice->getTotal() == 0.0)
            {
                Invoice::change_status($invoice->id, 1);
            }

            if($request->from == 'tasks-tab')
            {
                $validate = [
                    'task' => 'required',
                ];

                $item = $request->task;
            }
            else
            {
                $validate = [
                    'title' => 'required',
                ];

                $item = $request->title;
            }

            $validate['price'] = 'required|numeric|min:0';

            $validator = Validator::make(
                $request->all(), $validate
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            InvoiceProduct::create(
                [
                    'invoice_id' => $invoice->id,
                    'item' => $item,
                    'price' => $request->price,
                    'type' => str_replace('-tab', '', $request->from),
                ]
            );

            return redirect()->back()->with('success', __('Item successfully added.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function productDelete($id, $product_id)
    {
        $usr     = Auth::user();
        $invoice = Invoice::find($id);

        if($invoice->created_by == $usr->id)
        {
            $invoiceProduct = InvoiceProduct::find($product_id);
            $invoiceProduct->delete();

            if($invoice->getDue() <= 0.0)
            {
                Invoice::change_status($invoice->id, 3);
            }

            return redirect()->back()->with('success', __('Item successfully deleted.'));
        }
    }

    public function paymentAdd($id)
    {
        $usr     = Auth::user();
        $invoice = Invoice::find($id);

        if($invoice->created_by == $usr->id)
        {
            return view('invoices.payment', compact('invoice'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function paymentStore($id, Request $request)
    {
        $usr     = Auth::user();
        $invoice = Invoice::find($id);

        if($invoice->created_by == $usr->id)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'amount' => 'required|numeric|min:1',
                                   'date' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            InvoicePayment::create(
                [
                    'transaction_id' => $this->transactionNumber(),
                    'invoice_id' => $invoice->id,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'payment_id' => 0,
                    'payment_type' => __('MANUAL'),
                    'notes' => $request->notes,
                ]
            );
            if($invoice->getDue() == 0.0)
            {
                Invoice::change_status($invoice->id, 3);
            }
            else
            {
                Invoice::change_status($invoice->id, 2);
            }

            return redirect()->back()->with('success', __('Payment successfully added.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function transactionNumber()
    {
        $usr = Auth::user();
        //        $creator_id = $usr->getCreatedBy();
        //        $latest = InvoicePayment::select('invoice_payments.*')->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->where('invoices.created_by', '=', $creator_id)->latest()->first();

        $latest = InvoicePayment::select('invoice_payments.*')->join('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->latest()->first();

        if($latest)
        {
            return $latest->transaction_id + 1;
        }

        return 1;
    }

    //Client Invoice Payment
    public function addPayment($id, Request $request)
    {
        $objUser        = Auth::user();
        $invoice        = Invoice::find($id);
        $paymentSetting = Utility::getPaymentSetting($invoice->created_by);

        if($paymentSetting['enable_stripe'] == 'on')
        {
            $project = Project::find($invoice->project_id);

            // validate amount it must be at least 1
            $validator = Validator::make(
                $request->all(), ['amount' => 'required|numeric|min:1']
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            if($invoice)
            {
                if($request->amount > $invoice->getDue())
                {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                }
                else
                {
                    try
                    {
                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                        $price   = $request->amount;
                        Stripe\Stripe::setApiKey($paymentSetting['stripe_secret']);
                        $data = Stripe\Charge::create(
                            [
                                "amount" => 100 * $price,
                                "currency" => $project->currency_code,
                                "source" => $request->stripeToken,
                                "description" => $objUser->name . " - " . Utility::invoiceNumberFormat($invoice->invoice_id),
                                "metadata" => ["order_id" => $orderID],
                            ]
                        );

                        if($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1)
                        {
                            InvoicePayment::create(
                                [
                                    'transaction_id' => $this->transactionNumber(),
                                    'invoice_id' => $invoice->id,
                                    'amount' => $price,
                                    'date' => date('Y-m-d'),
                                    'payment_id' => 0,
                                    'payment_type' => 'STRIPE',
                                    'client_id' => $objUser->id,
                                    'notes' => '',
                                ]
                            );

                            if(($invoice->getDue() - $request->amount) == 0)
                            {
                                Invoice::change_status($invoice->id, 3);
                                $settings  = Utility::settingsById(Auth::user()->id);
                            
                                if(isset($settings['invoice_status_notificaation']) && $settings['invoice_status_notificaation'] == 1){
                                    $msg = \App\Models\Invoice::change_status($invoice->status, 2). ' '.__(" To ").' '. $invoice->change_status() . '.';
                                    
                                    Utility::send_slack_msg($msg);    
                                }


                                 if(isset($settings['telegram_invoice_status_notificaation']) && $settings['telegram_invoice_status_notificaation'] == 1){
                                    $resp =  \App\Models\Invoice::change_status($invoice->status, 2). ' '.__(" To ").' '. $invoice->change_status() . '.';
                                    Utility::send_telegram_msg($resp);    
                                }
                            }
                            else
                            {
                                Invoice::change_status($invoice->id, 2);
                                $settings  = Utility::settings(Auth::user()->id);
                            
                                if(isset($settings['invoice_status_notificaation']) && $settings['invoice_status_notificaation'] == 1){
                                    $msg = \App\Models\Invoice::change_status($invoice->status, 2). ' '.__(" To ").' '. $invoice->change_status() . '.';
                                    
                                    Utility::send_slack_msg($msg);    
                                }

                                if(isset($settings['telegram_invoice_status_notificaation']) && $settings['telegram_invoice_status_notificaation'] == 1){
                                    $resp =  \App\Models\Invoice::change_status($invoice->status, 2). ' '.__(" To ").' '. $invoice->change_status() . '.';
                                    Utility::send_telegram_msg($resp);    
                                }
                            }

                            return redirect()->back()->with('success', __(' Payment added Successfully'));
                        }
                        else
                        {
                            return redirect()->back()->with('error', __('Transaction has been failed!'));
                        }

                    }
                    catch(\Exception $e)
                    {
                        return redirect()->route('invoices.show', $invoice->id)->with('error', __($e->getMessage()));
                    }
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // end client invoice payment

    // send email from invoice creator side
    public function sent($id)
    {
        $usr     = Auth::user();
        $invoice = Invoice::find($id);

        if($invoice->created_by == $usr->id)
        {
            $client           = !empty($invoice->project) ? $invoice->client : '';
            $invoice->name    = !empty($client) ? $client->name : 'Dear';
            $email            = !empty($client) ? $client->email : '';
            $invoice->invoice = Utility::invoiceNumberFormat($invoice->invoice_id);
            $invoiceId        = Crypt::encrypt($invoice->invoice_id);
            $invoice->url     = route('get.invoice', $invoiceId);

            try
            {
                Mail::to($email)->send(new InvoiceSend($invoice));
            }
            catch(\Exception $e)
            {
                // $smtp_error = $e->getMessage();
                $smtp_error = __('E-Mail has been not sent due to some issue');
            }

            return redirect()->back()->with('success', __('Invoice successfully sent.') . (isset($smtp_error) && !empty($smtp_error) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function paymentReminder($invoice_id)
    {
        $invoice          = Invoice::find($invoice_id);
        $client           = !empty($invoice->project) ? $invoice->client : '';
        $invoice->getDue  = Utility::projectCurrencyFormat($invoice->project_id, $invoice->getDue(), true);
        $invoice->name    = !empty($client) ? $client->name : 'Dear';
        $email            = !empty($client) ? $client->email : '';
        $invoice->date    = Utility::getDateFormated($invoice->due_date);
        $invoice->invoice = Utility::invoiceNumberFormat($invoice->invoice_id);
        $invoiceId        = Crypt::encrypt($invoice->invoice_id);
        $invoice->url     = route('get.invoice', $invoiceId);

        try
        {
            Mail::to($email)->send(new PaymentReminder($invoice));
        }
        catch(\Exception $e)
        {
            // $smtp_error = $e->getMessage();
            $smtp_error = __('E-Mail has been not sent due to to some issue');
        }

        return redirect()->back()->with('success', __('Payment reminder successfully send.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }
    // end

    // Invoice Template Setting
    public function templateSetting()
    {
        $usr     = Auth::user();
        $decoded = $usr->decodeDetails();
        
        return view('invoices.template_setting', compact('decoded'));
    }

    public function saveTemplateSettings(Request $request)
    {
        //dd($request->all());
        $user = Auth::user();
        $post = $request->all();

        unset($post['_token']);

        if(isset($post['invoice_template']) && (!isset($post['invoice_color']) || empty($post['invoice_color'])))
        {
            $post['invoice_color'] = "ffffff";
        }
        if($request->invoice_logo)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'invoice_logo' => 'image|mimes:png|max:2048',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoice_logo = $user->id . '_invoice_logo.png';
            $request->file('invoice_logo')->storeAs('invoice_logo', $invoice_logo);
            $post['invoice_logo'] = 'invoice_logo/' . $invoice_logo;
        }

        $details = $user->decodeDetails();

        foreach($post as $key => $data)
        {
            $details[$key] = $data;
        }

        $user->details = json_encode($details);
        $user->save();

        if(isset($post['invoice_template']))
        {
            return redirect()->back()->with('success', __('Invoice Setting updated successfully'));
        }
    }

    public function printInvoice($id)
    {
        $invoiceId = Crypt::decrypt($id);
        $invoice   = Invoice::find($invoiceId);

        if($invoice)
        {
            $invoice_usr     = User::find($invoice->created_by);
            $invoice_creator = $invoice_usr->decodeDetails($invoice->created_by);
            $left_address    = $invoice_usr->decodeDetails();

            if($invoice->client_id == $invoice_usr->id)
            {
                $right_address = $invoice_creator;
            }
            else
            {
                $right_address = $invoice_usr->decodeDetails($invoice->client_id);
            }

            $color      = '#' . $invoice_creator['invoice_color'];
            $font_color = Utility::getFontColor($color);

            //Set your logo
            $img = asset(\Storage::url($invoice_creator['invoice_logo']));

            // Set Footer information
            $footer['invoice_footer_title'] = $invoice_usr->decodeDetails()['invoice_footer_title'];
            $footer['invoice_footer_note']  = $invoice_usr->decodeDetails()['invoice_footer_note'];

            return view('invoices.templates.' . $invoice_creator['invoice_template'], compact('invoice', 'color', 'img', 'font_color', 'left_address', 'right_address', 'footer', 'invoice_usr'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewInvoice($template, $color)
    {
        $invoice_usr = Auth::user();

        $left_address  = [
            'light_logo' => asset(\Storage::url('logo/logo.png')),
            'dark_logo' => asset(\Storage::url('logo/logo.png')),
            'address' => '793  Sherbrooke Ouest',
            'city' => 'Montreal',
            'state' => 'Quebec',
            'zipcode' => 'H4A 1H3',
            'country' => 'Canada',
            'telephone' => '5142405577',
            'invoice_template' => 'template1',
            'invoice_color' => 'ffffff',
            'invoice_logo' => asset(\Storage::url('logo/logo.png')),
        ];
        $right_address = [
            'light_logo' => asset(\Storage::url('logo/logo.png')),
            'dark_logo' => asset(\Storage::url('logo/logo.png')),
            'address' => '820  Papineau Avenue',
            'city' => 'Montreal',
            'state' => 'Quebec',
            'zipcode' => 'H2K 4J5',
            'country' => 'Canada',
            'telephone' => '9876543210',
            'invoice_template' => 'template1',
            'invoice_color' => 'ffffff',
            'invoice_logo' => asset(\Storage::url('logo/logo.png')),
        ];

        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $tax             = new Tax();
        $tax->id         = 1;
        $tax->name       = 'GST';
        $tax->rate       = 10;
        $tax->created_by = 1;

        $project                    = new Project();
        $project->id                = 0;
        $project->name              = 'Test Project';
        $project->status            = 'complete';
        $project->budget            = '15000';
        $project->start_date        = date("d M Y");
        $project->end_date          = date("d M Y");
        $project->currency          = '$';
        $project->currency_code     = 'USD';
        $project->currency_position = 'pre';

        $items = [];
        for($i = 1; $i <= 3; $i++)
        {
            $item             = new InvoiceProduct();
            $item->invoice_id = 0;
            $item->item       = 'Product ' . $i;
            $item->price      = 100;
            $item->type       = 'other';
            $items[]          = $item;
        }

        $user       = new User();
        $user->name = 'Hello world';

        $client       = new User();
        $client->name = 'Client';

        $invoice             = new Invoice();
        $invoice->invoice_id = 0;
        $invoice->project_id = 0;
        $invoice->client_id  = 0;
        $invoice->project    = $project;
        $invoice->due_date   = date("d M Y");
        $invoice->user       = $user;
        $invoice->client     = $client;
        $invoice->items      = $items;
        $invoice->tax        = $tax;

        //Set your logo
        $img = asset(\Storage::url($invoice_usr->decodeDetails()['invoice_logo']));

        $footer['invoice_footer_title'] = $invoice_usr->decodeDetails()['invoice_footer_title'];
        $footer['invoice_footer_note']  = $invoice_usr->decodeDetails()['invoice_footer_note'];

        return view('invoices.templates.' . $template, compact('invoice', 'preview', 'color', 'img', 'font_color', 'left_address', 'right_address', 'footer', 'invoice_usr'));
    }

    // Client Side Invoice Send
    public function customMail($invoice_id)
    {
        $usr     = Auth::user();
        $invoice = Invoice::find($invoice_id);

        if($invoice->client_id == $usr->id)
        {
            return view('invoices.invoice_send', compact('invoice_id'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customMailSend(Request $request, $invoice_id)
    {
        $usr     = Auth::user();
        $invoice = Invoice::find($invoice_id);

        if($invoice->client_id == $usr->id)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'email' => 'required|email',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $email            = $request->email;
            $invoice->name    = $usr->name;
            $invoice->invoice = Utility::invoiceNumberFormat($invoice->invoice_id);
            $invoiceId        = Crypt::encrypt($invoice->invoice_id);
            $invoice->url     = route('get.invoice', $invoiceId);

            try
            {
                Mail::to($email)->send(new CustomInvoiceSend($invoice));
            }
            catch(\Exception $e)
            {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->back()->with('success', __('Invoice successfully sent.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function pay($invoice_id){

        $id=\Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
        $invoice =  Invoice::find($id);

        if(\Auth::check())
        {
            $user=\Auth::user();
        }
        else
        {
            $user=User::where('id',$invoice->created_by)->first();
        }   
        
            if($user->type == 'owner' || $invoice->created_by == $user->getCreatedBy())
            {
            
                $settings = Utility::settings();
                $payment_setting = Utility::getAdminPaymentSetting();
                $client   = $invoice->project->client;
                if($client != 0)
                {
                    $user = User::where('id', $client)->first();
                }
                else
                {
                    $user = '';
                }
            
                foreach($invoice->items as $item)
                {
                    $taxes         = $item->tax();
                    
                }
                $company_setting = Utility::settings();
                return view('invoices.invoicepay', compact('invoice','company_setting', 'settings', 'user', 'payment_setting','item'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
    }
    
   
    public function pdffrominvoice($id)
    {
        $invoiceId = Crypt::decrypt($id);
        $invoice   = Invoice::find($invoiceId);

        if($invoice)
        {
            $invoice_usr     = User::find($invoice->created_by);
            $invoice_creator = $invoice_usr->decodeDetails($invoice->created_by);
            $left_address    = $invoice_usr->decodeDetails();

            if($invoice->client_id == $invoice_usr->id)
            {
                $right_address = $invoice_creator;
            }
            else
            {
                $right_address = $invoice_usr->decodeDetails($invoice->client_id);
            }

            $color      = '#' . $invoice_creator['invoice_color'];
            $font_color = Utility::getFontColor($color);

            //Set your logo
            $img = asset(\Storage::url($invoice_creator['invoice_logo']));

            // Set Footer information
            $footer['invoice_footer_title'] = $invoice_usr->decodeDetails()['invoice_footer_title'];
            $footer['invoice_footer_note']  = $invoice_usr->decodeDetails()['invoice_footer_note'];

            return view('invoices.templates.' . $invoice_creator['invoice_template'], compact('invoice', 'color', 'img', 'font_color', 'left_address', 'right_address', 'footer', 'invoice_usr'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function importFile()
    {
        return view('invoices.import');
    }


    public function export()
    {
        $name = 'Members' . date('Y-m-d i:h:s');
        $data = Excel::download(new InvoiceExport(), $name . '.xlsx'); ob_end_clean();

        return $data;
    }

    public function import(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'file' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
     

        $projects = (new InvoiceImport())->toArray(request()->file('file'))[0];
        //dd($projects);
        $totalitem = count($projects) - 1;
       
        $errorArray    = [];
        for($i = 1; $i <= count($projects) - 1; $i++)
        {
            $members = $projects[$i];
            $projectByEmail = Invoice::where('id', $members[1])->first();
            
            if(!empty($projectByEmail))
            {
                $userData = $projectByEmail;
            }
            else
            {
                $userData = new Invoice();
            }
            
            $userData->invoice_id                = $members[0];
            $userData->project_id                = $members[1];
            $userData->client_id                 = $members[2];
            $userData->tax_id	                 = $members[3];
            $userData->due_date                  = $members[4];
            $userData->status                    = '1';
            $userData->created_by                = $user->id;
           // dd($userData);
            if(empty($userData))
            {
                $errorArray[]      = $userData;
            }
            else
            {
                $userData->save();
            }
        }

        $errorRecord = [];
        if(empty($errorArray))
        {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        }
        else
        {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalitem . ' ' . 'record');


            foreach($errorArray as $errorData)
            {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

}
