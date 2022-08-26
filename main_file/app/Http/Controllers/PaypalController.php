<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\Project;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PaypalController extends Controller
{
    private $_api_context;

    public function setApiContext()
    {
        $paypal_conf = config('paypal');

        if(isset($_REQUEST['from']) && $_REQUEST['from'] == 'invoice')
        {
            $paymentSetting                  = Utility::getPaymentSetting($_REQUEST['invoice_creator']);
            $paypal_conf['settings']['mode'] = $paymentSetting['paypal_mode'];
            $paypal_conf['client_id']        = $paymentSetting['paypal_client_id'];
            $paypal_conf['secret_key']       = $paymentSetting['paypal_secret_key'];
        }
        else
        {
            $paymentSetting                  = Utility::getPaymentSetting();
            $paypal_conf['settings']['mode'] = $paymentSetting['paypal_mode'];
            $paypal_conf['client_id']        = $paymentSetting['paypal_client_id'];
            $paypal_conf['secret_key']       = $paymentSetting['paypal_secret_key'];
        }

        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $paypal_conf['client_id'], $paypal_conf['secret_key']
            )
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function clientPayWithPaypal(Request $request, $invoice_id)
    {
       
        $id= $invoice_id;
        
        $invoice =  Invoice::find($id);
        if(\Auth::check())
        {
            $user=\Auth::user();
        }
        else
        {
            $user=User::where('id',$invoice->created_by)->first();
        }   
       
        // $invoice        = Invoice::find($invoice_id);
        $paymentSetting = Utility::getPaymentSetting($invoice->created_by);
        if($paymentSetting['enable_paypal'] == 'on')
        {
            $get_amount = $request->amount;

            // validate amount it must be at least 1
            $validator = Validator::make(
                $request->all(), ['amount' => 'required|numeric|min:1']
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $project = Project::find($invoice->project_id);

            if($invoice)
            {
                if($get_amount > $invoice->getDue())
                {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                }
                else
                {
                    $this->setApiContext();

                    $name = $user->name . " - " . Utility::invoiceNumberFormat($invoice->invoice_id);

                    $payer = new Payer();
                    $payer->setPaymentMethod('paypal');

                    $item_1 = new Item();
                    $item_1->setName($name)->setCurrency($project->currency_code)->setQuantity(1)->setPrice($get_amount);

                    $item_list = new ItemList();
                    $item_list->setItems([$item_1]);

                    $amount = new Amount();
                    $amount->setCurrency($project->currency_code)->setTotal($get_amount);

                    $transaction = new Transaction();
                    $transaction->setAmount($amount)->setItemList($item_list)->setDescription($request->notes);

                    $redirect_urls = new RedirectUrls();
                    $redirect_urls->setReturnUrl(route('client.get.payment.status', $invoice->id))->setCancelUrl(route('client.get.payment.status', $invoice->id));

                    $payment = new Payment();
                    $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions([$transaction]);

                    try
                    {
                        $payment->create($this->_api_context);
                    }
                    catch(\PayPal\Exception\PayPalConnectionException $ex) //PPConnectionException
                    {
                        if(\Config::get('app.debug'))
                        {
                            return redirect()->route('invoices.show', $invoice_id)->with('error', __('Connection timeout'));
                        }
                        else
                        {
                            return redirect()->route('invoices.show', $invoice_id)->with('error', __('Some error occur, sorry for inconvenient'));
                        }
                    }

                    foreach($payment->getLinks() as $link)
                    {
                        if($link->getRel() == 'approval_url')
                        {
                            $redirect_url = $link->getHref();
                            break;
                        }
                    }

                    Session::put('paypal_payment_id', $payment->getId());

                    if(isset($redirect_url))
                    {
                        return Redirect::away($redirect_url);
                    }

                    return redirect()->route('invoices.show', $invoice_id)->with('error', __('Unknown error occurred'));
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

    public function clientGetPaymentStatus(Request $request, $invoice_id)
    {
        $user    = Auth::user();
        $invoice = Invoice::find($invoice_id);
        $paymentSetting   = Utility::getPaymentSetting();
        
        if($invoice)
        {   
            $this->setApiContext($paymentSetting);
           
            $payment_id = Session::get('paypal_payment_id');

            if(empty($request->PayerID || empty($request->token)))
            {
                return redirect()->route('invoices.show', $invoice_id)->with('error', __('Payment failed'));
            }
            $payment = Payment::get($payment_id, $this->_api_context);
           
            $execution = new PaymentExecution();
            $execution->setPayerId($request->PayerID);
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            // try
            // {
                $result = $payment->execute($execution, $this->_api_context)->toArray();

                $status = ucwords(str_replace('_', ' ', $result['state']));

                if($result['state'] == 'approved')
                {
                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->transaction_id = $orderID;
                    $invoice_payment->invoice_id     = $invoice->id;
                    $invoice_payment->amount         = $result['transactions'][0]['amount']['total'];
                    $invoice_payment->date           = date('Y-m-d');
                    $invoice_payment->payment_id     = 0;
                    $invoice_payment->payment_type   = 'PAYPAL';
                    $invoice_payment->client_id      = $user->id;
                    $invoice_payment->notes          = '';
                    $invoice_payment->save();

                    if(($invoice->getDue() - $invoice_payment->amount) == 0)
                    {
                        Invoice::change_status($invoice->id, 3);
                    }
                    else
                    {
                        Invoice::change_status($invoice->id, 2);
                    }

                    $settings  = Utility::settingsById(Auth::user()->id);
                   
                    if(isset($settings['invoice_status_notificaation']) && $settings['invoice_status_notificaation'] == 1){
                        $msg = __('Invoice ').\App\Models\Utility::invoiceNumberFormat($invoice->invoice_id) . ' status changed '. (\App\Models\Invoice::$status[$invoice->status]) ;
                        Utility::send_slack_msg($msg);    
                    }

                    if(isset($settings['telegram_invoice_status_notificaation']) && $settings['telegram_invoice_status_notificaation'] == 1){
                            $resp =__('Invoice ').\App\Models\Utility::invoiceNumberFormat($invoice->invoice_id) . ' status changed '. (\App\Models\Invoice::$status[$invoice->status]) ;
                            Utility::send_telegram_msg($resp);    
                        }
                        
                    
                    return redirect()->route('invoices.show', $invoice_id)->with('success', __('Payment added Successfully'));
                }
                else
                {
                    return redirect()->route('invoices.show', $invoice_id)->with('error', __('Transaction has been ' . $status));
                }

            // }
            // catch(\Exception $e)
            // {
            //     return redirect()->route('invoices.show', $invoice_id)->with('error', __('Transaction has been failed!'));
            // }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function planPayWithPaypal(Request $request)
    {
        $authuser = Auth::user();
        $planID   = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan     = Plan::find($planID);

        if($plan)
        {
            try
            {
                $coupon_id = null;
                $price     = (float)$plan->{$request->paypal_payment_frequency . '_price'};

                if(!empty($request->coupon))
                {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if(!empty($coupons))
                    {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value = ($price / 100) * $coupons->discount;
                        $price          = $price - $discount_value;

                        if($coupons->limit == $usedCoupun)
                        {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
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

                    $assignPlan = $authuser->assignPlan($plan->id);

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
                                'price' => $price,
                                'price_currency' => !empty(env('CURRENCY_CODE')) ? env('CURRENCY_CODE') : 'usd',
                                'txn_id' => '',
                                'payment_type' => __('Zero Price'),
                                'payment_status' => 'succeeded',
                                'receipt' => null,
                                'user_id' => $authuser->id,
                            ]
                        );

                        return redirect()->route('home')->with('success', __('Plan successfully upgraded.'));
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Plan fail to upgrade.'));
                    }
                }

                $this->setApiContext();

                $name = $plan->name;

                $payer = new Payer();
                $payer->setPaymentMethod('paypal');

                $item_1 = new Item();
                $item_1->setName($name)->setCurrency(env('CURRENCY_CODE'))->setQuantity(1)->setPrice($price);

                $item_list = new ItemList();
                $item_list->setItems([$item_1]);

                $amount = new Amount();
                $amount->setCurrency(env('CURRENCY_CODE'))->setTotal($price);

                $transaction = new Transaction();
                $transaction->setAmount($amount)->setItemList($item_list)->setDescription($name);

                $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(
                    route(
                        'plan.get.payment.status', [
                                                     $plan->id,
                                                     'coupon_id' => $coupon_id,
                                                     'frequency' => $request->paypal_payment_frequency,
                                                 ]
                    )
                )->setCancelUrl(
                    route(
                        'plan.get.payment.status', [
                                                     $plan->id,
                                                     'coupon_id' => $coupon_id,
                                                     'frequency' => $request->paypal_payment_frequency,
                                                 ]
                    )
                );

                $payment = new Payment();
                $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions([$transaction]);

                try
                {
                    $payment->create($this->_api_context);
                }
                catch(\PayPal\Exception\PayPalConnectionException $ex) //PPConnectionException
                {
                    if(config('app.debug'))
                    {
                        return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Connection timeout'));
                    }
                    else
                    {
                        return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Some error occur, sorry for inconvenient'));
                    }
                }

                foreach($payment->getLinks() as $link)
                {
                    if($link->getRel() == 'approval_url')
                    {
                        $redirect_url = $link->getHref();
                        break;
                    }
                }

                // Session::put('plan_paypal_payment_id', $payment->getId());

                if(isset($redirect_url))
                {
                    return Redirect::away($redirect_url);
                }

                return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Unknown error occurred'));
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetPaymentStatus(Request $request, $plan_id)
    {
        $user = Auth::user();
        $plan = Plan::find($plan_id);

        if($plan)
        {
            $this->setApiContext();

            // $payment_idt = Session::get('plan_paypal_payment_id');
            $payment_id = $request->paymentId;
            // dd($payment_idt,$payment_id);

            if(empty($request->PayerID || empty($request->token)))
            {
                return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Payment failed'));
            }

            $payment   = Payment::get($payment_id, $this->_api_context);
            $execution = new PaymentExecution();
            $execution->setPayerId($request->PayerID);

            try
            {
                $result = $payment->execute($execution, $this->_api_context)->toArray();

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $status = ucwords(str_replace('_', ' ', $result['state']));

                //                Session::forget('plan_paypal_payment_id');

                if($result['state'] == 'approved')
                {
                    if(!empty($user->payment_subscription_id) && $user->payment_subscription_id != '')
                    {
                        try
                        {
                            $user->cancel_subscription($user->id);
                        }
                        catch(\Exception $exception)
                        {
                            \Log::debug($exception->getMessage());
                        }
                    }

                    if($request->has('coupon_id') && $request->coupon_id != '')
                    {
                        $coupons = Coupon::find($request->coupon_id);
                        if(!empty($coupons))
                        {
                            $userCoupon            = new UserCoupon();
                            $userCoupon->user_id   = $user->id;
                            $userCoupon->coupon_id = $coupons->id;
                            $userCoupon->order_id  = $orderID;
                            $userCoupon->save();

                            $usedCoupun = $coupons->used_coupon();
                            if($coupons->limit <= $usedCoupun)
                            {
                                $coupons->is_active = 0;
                                $coupons->save();
                            }
                        }
                    }

                    $order                 = new Order();
                    $order->order_id       = $orderID;
                    $order->name           = $user->name;
                    $order->card_number    = '';
                    $order->card_exp_month = '';
                    $order->card_exp_year  = '';
                    $order->plan_name      = $plan->name;
                    $order->plan_id        = $plan->id;
                    $order->price          = $result['transactions'][0]['amount']['total'];
                    $order->price_currency = env('CURRENCY_CODE');
                    $order->txn_id         = $payment_id;
                    $order->payment_type   = 'PAYPAL';
                    $order->payment_status = $result['state'];
                    $order->receipt        = '';
                    $order->user_id        = $user->id;
                    $order->save();

                    $assignPlan = $user->assignPlan($plan->id, $request->frequency);

                    if($assignPlan['is_success'])
                    {
                        return redirect()->route('profile')->with('success', __('Plan activated Successfully!'));
                    }
                    else
                    {
                        return redirect()->route('profile')->with('error', __($assignPlan['error']));
                    }
                }
                else
                {
                    return redirect()->route('profile')->with('error', __('Transaction has been ') . __($status));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed!'));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }
}
