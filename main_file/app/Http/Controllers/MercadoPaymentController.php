<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Utility;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use LivePixel\MercadoPago\MP;

class MercadoPaymentController extends Controller
{
    public $secret_key;
    public $app_id;
    public $is_enabled;
    public $currancy;

    public function __construct()
    {
        $this->middleware(
            [
                'auth',
                'XSS',
            ]
        );

        if(isset($_REQUEST['from']) && $_REQUEST['from'] == 'invoice')
        {
            $payment_setting = Utility::getPaymentSetting($_REQUEST['invoice_creator']);
            $invoice         = Invoice::find($_REQUEST['invoice_creator']);
            $this->currancy  = (isset($invoice->project)) ? $invoice->project->currency_code : 'USD';
        }
        else
        {
            $payment_setting = Utility::getPaymentSetting();
            $this->currancy  = env('CURRENCY_CODE');
        }
        $this->secret_key = isset($payment_setting['mercado_secret_key']) ? $payment_setting['mercado_secret_key'] : '';
        $this->app_id     = isset($payment_setting['mercado_app_id']) ? $payment_setting['mercado_app_id'] : '';
        $this->is_enabled = isset($payment_setting['is_mercado_enabled']) ? $payment_setting['is_mercado_enabled'] : 'off';
    }

    public function planPayWithMercado(Request $request)
    {
        $authuser  = Auth::user();
        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan      = Plan::find($planID);
        $coupon_id = '';

        if($plan)
        {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  = $plan->{$request->mercado_payment_frequency . '_price'};

            if(isset($request->coupon) && !empty($request->coupon))
            {
                $request->coupon = trim($request->coupon);
                $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if(!empty($coupons))
                {
                    $usedCoupun             = $coupons->used_coupon();
                    $discount_value         = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;

                    if($usedCoupun >= $coupons->limit)
                    {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }

                    $price     = $price - $discount_value;
                    $coupon_id = $coupons->id;
                }
                else
                {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if($price <= 0)
            {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id, $request->mercado_payment_frequency);

                if($assignPlan['is_success'] == true && !empty($plan))
                {
                    if(!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '')
                    {
                        try
                        {
                            $authuser->cancel_subscription($authuser->id);
                        }
                        catch(\Exception $exception)
                        {
                            \Log::debug($exception->getMessage());
                        }
                    }

                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price == null ? 0 : $price,
                            'price_currency' => !empty(env('CURRENCY_CODE')) ? env('CURRENCY_CODE') : 'usd',
                            'txn_id' => '',
                            'payment_type' => __('Zero Price'),
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $res['msg']  = __("Plan successfully upgraded.");
                    $res['flag'] = 2;

                    return $res;
                }
                else
                {
                    return response()->json(
                        [
                            'message' => __('Plan fail to upgrade.'),
                        ], 401
                    );
                }
            }

            $preference_data = array(
                "items" => array(
                    array(
                        "title" => "Plan : " . $plan->name,
                        "quantity" => 1,
                        "currency_id" => $this->currancy,
                        "unit_price" => (float)$price,
                    ),
                ),
            );

            try
            {
                $mp         = new MP($this->app_id, $this->secret_key);
                $preference = $mp->create_preference($preference_data);

                return redirect($preference['response']['init_point']);
            }
            catch(Exception $e)
            {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function getPaymentStatus(Request $request)
    {
        Log::info(json_encode($request->all()));
    }

    public function invoicePayWithMercado(Request $request)
    {
        $validatorArray = [
            'amount' => 'required',
            'invoice_id' => 'required',
        ];
        $validator      = Validator::make(
            $request->all(), $validatorArray
        )->setAttributeNames(
            ['invoice_id' => 'Invoice']
        );

        if($validator->fails())
        {
            return redirect()->back()->with('error', __($validator->errors()->first()));
        }
        $invoice = Invoice::find($request->invoice_id);
       
        $payment_setting = Utility::getCompanyPaymentSettingWithOutAuth($invoice->created_by);
        
        $this->token = isset($payment_setting['mercado_access_token'])?$payment_setting['mercado_access_token']:'';
        $this->mode = isset($payment_setting['mercado_mode'])?$payment_setting['mercado_mode']:'';
        $this->is_enabled = isset($payment_setting['is_mercado_enabled'])?$payment_setting['is_mercado_enabled']:'off';
        $settings = Utility::settingsById($invoice->created_by);
        
        
        if($invoice->getDue() < $request->amount)
        {
            return redirect()->route('invoices.show', $invoice->id)->with('error', __('Invalid amount.'));

        }

        $preference_data = array(
            "items" => array(
                array(
                    "title" => "Invoice : " . $request->invoice_id,
                    "quantity" => 1,
                    "currency_id" => $this->currancy,
                    "unit_price" => (float)$request->amount,
                ),
            ),
        );

        \MercadoPago\SDK::setAccessToken($this->token);
        // try {
               
            // Create a preference object
            $preference = new \MercadoPago\Preference();
            // Create an item in the preference
            $item = new \MercadoPago\Item();
            $item->title = "Invoice : " . $request->invoice_id;
            $item->quantity = 1;
            $item->unit_price = (float)$request->amount;
            $preference->items = array($item);

            $success_url = route('invoice.mercado.callback',['amount'=>(float)$request->amount,'flag'=>'success',encrypt($invoice->id)]);
            $failure_url = route('invoice.mercado.callback',[encrypt($invoice->id),'flag'=>'failure']);
            $pending_url = route('invoice.mercado.callback',[encrypt($invoice->id),'flag'=>'pending']);
            $preference->back_urls = array(
                "success" => $success_url,
                "failure" => $failure_url,
                "pending" => $pending_url
            );
            $preference->auto_return = "approved";
            $preference->save();

            // Create a customer object
            $payer = new \MercadoPago\Payer();
            // Create payer information
            $payer->name = \Auth::user()->name;
            $payer->email = \Auth::user()->email;
            $payer->address = array(
                "street_name" => ''
            );
            
            if($this->mode =='live'){
                $redirectUrl = $preference->init_point;
            }else{
                $redirectUrl = $preference->sandbox_init_point;
            }
            return redirect($redirectUrl);
        // } catch (Exception $e) {
        //     return redirect()->back()->with('error', $e->getMessage());
        // }

    }

    public function getInvoicePaymentStatus(Request $request,$invoice_id)
    {
        if(!empty($invoice_id))
        {
            
            $invoice_id = decrypt($invoice_id);
            $invoice    = Invoice::find($invoice_id);
            $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
            if($invoice && $request->has('status'))
            {
                // try
                // {
                  
                    if($request->status == 'approved' && $request->flag =='success')
                    {
                        $new                 = new InvoicePayment();
                        $new->invoice_id     = $invoice_id;
                        $new->transaction_id = $orderID;
                        $new->date           = Date('Y-m-d');
                        $new->amount         = $request->has('amount')?$request->amount:0;
                        $new->client_id      = Auth::user()->id;
                        $new->payment_type = 'Mercado Pago';
                        $new->payment_id     = 0;
                        
                        $new->save();

                        if(($invoice->getDue() - $new->amount) == 0)
                        {
                            Invoice::change_status($invoice->id, 3);
                        }
                        else
                        {
                            Invoice::change_status($invoice->id, 2);
                        }
                        $invoice->save();

                        $settings  = Utility::settingsById(Auth::user()->id);
                   
                        if(isset($settings['invoice_status_notificaation']) && $settings['invoice_status_notificaation'] == 1){
                            $msg = __('Invoice ').\App\Models\Utility::invoiceNumberFormat($invoice->invoice_id) . ' status changed '. (\App\Models\Invoice::$status[$invoice->status]) ;
                            Utility::send_slack_msg($msg);    
                        }

                        if(isset($settings['telegram_invoice_status_notificaation']) && $settings['telegram_invoice_status_notificaation'] == 1){
                            $resp =__('Invoice ').\App\Models\Utility::invoiceNumberFormat($invoice->invoice_id) . ' status changed '. (\App\Models\Invoice::$status[$invoice->status]) ;
                            Utility::send_telegram_msg($resp);    
                        }
                                
                        return redirect()->route('invoices.show', $invoice_id)->with('success', __('Invoice paid Successfully!'));
                    }else{
                        return redirect()->route('invoices.show',$invoice_id)->with('error', __('Transaction fail'));
                    }
                // }
                // catch(\Exception $e)
                // {
                //     return redirect()->route('invoices.index')->with('error', __('Plan not found!'));
                // }
            }else{
                return redirect()->route('invoices.show',$invoice_id)->with('error', __('Invoice not found.'));
            }
        }else{
            return redirect()->route('invoices.index')->with('error', __('Invoice not found.'));
        }
    }
}
