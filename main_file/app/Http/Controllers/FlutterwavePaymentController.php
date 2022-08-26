<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FlutterwavePaymentController extends Controller
{
    public $secret_key;
    public $public_key;
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
        $this->secret_key = isset($payment_setting['flutterwave_secret_key']) ? $payment_setting['flutterwave_secret_key'] : '';
        $this->public_key = isset($payment_setting['flutterwave_public_key']) ? $payment_setting['flutterwave_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_flutterwave_enabled']) ? $payment_setting['is_flutterwave_enabled'] : 'off';

    }

    public function planPayWithFlutterwave(Request $request)
    {
        $planID    = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan      = Plan::find($planID);
        $authuser  = Auth::user();
        $coupon_id = '';
        if($plan)
        {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  = $plan->{$request->flaterwave_payment_frequency . '_price'};
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

                $assignPlan = $authuser->assignPlan($plan->id, $request->flaterwave_payment_frequency);

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

            $res_data['email']             = Auth::user()->email;
            $res_data['total_price']       = $price;
            $res_data['currency']          = $this->currancy;
            $res_data['flag']              = 1;
            $res_data['payment_frequency'] = $request->flaterwave_payment_frequency;
            $res_data['coupon']            = $coupon_id;

            return $res_data;
        }
        else
        {
            return response()->json(
                [
                    'message' => __('Plan is deleted.'),
                ], 401
            );
        }

    }

    public function getPaymentStatus(Request $request, $pay_id, $plan)
    {
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan   = Plan::find($planID);
        $result = array();

        $user = Auth::user();
        if($plan)
        {
            try
            {
                $orderID = time();
                $data    = array(
                    'txref' => $pay_id,
                    'SECKEY' => $this->secret_key,
                    //secret key from pay button generated on rave dashboard
                );
                // make request to endpoint using unirest.
                $headers = array('Content-Type' => 'application/json');
                $body    = \Unirest\Request\Body::json($data);
                $url     = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify"; //please make sure to change this to production url when you go live

                // Make `POST` request and handle response with unirest
                $response = \Unirest\Request::post($url, $headers, $body);
                if(!empty($response))
                {
                    $response = json_decode($response->raw_body, true);
                }
                if(isset($response['status']) && $response['status'] == 'success')
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

                    $paydata = $response['data'];

                    if($request->has('coupon_id') && $request->coupon_id != '')
                    {
                        $coupons = Coupon::find($request->coupon_id);
                        if(!empty($coupons))
                        {
                            $userCoupon         = new UserCoupon();
                            $userCoupon->user   = $user->id;
                            $userCoupon->coupon = $coupons->id;
                            $userCoupon->order  = $orderID;
                            $userCoupon->save();


                            $usedCoupun = $coupons->used_coupon();
                            if($coupons->limit <= $usedCoupun)
                            {
                                $coupons->is_active = 0;
                                $coupons->save();
                            }
                        }
                    }

                    $user->is_plan_purchased = 1;
                    if($user->is_trial_done == 1)
                    {
                        $user->is_trial_done = 2;
                        $user->save();
                    }

                    $order                 = new Order();
                    $order->order_id       = $orderID;
                    $order->name           = $user->name;
                    $order->card_number    = '';
                    $order->card_exp_month = '';
                    $order->card_exp_year  = '';
                    $order->plan_name      = $plan->name;
                    $order->plan_id        = $plan->id;
                    $order->price          = isset($paydata['amount']) ? $paydata['amount'] : 0;
                    $order->price_currency = env('CURRENCY_CODE');
                    $order->txn_id         = isset($paydata['txid']) ? $paydata['txid'] : $pay_id;
                    $order->payment_type   = __('Flutterwave');
                    $order->payment_status = 'success';
                    $order->receipt        = '';
                    $order->user_id        = $user->id;
                    $order->save();

                    $assignPlan = $user->assignPlan($plan->id, $request->payment_frequency);

                    if($assignPlan['is_success'])
                    {
                        return redirect()->route('home')->with('success', __('Plan activated Successfully!'));
                    }
                    else
                    {
                        return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __($assignPlan['error']));
                    }
                }
                else
                {
                    return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Transaction has been failed.'));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Plan not found!'));
            }
        }
    }

    public function invoicePayWithFlutterwave(Request $request)
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
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ], 401
            );
        }
        $invoice = Invoice::find($request->invoice_id);
        if($invoice->getDue() < $request->amount)
        {
            return response()->json(
                [
                    'message' => __('Not Correct amount'),
                ], 401
            );
        }

        $res_data['email']       = Auth::user()->email;
        $res_data['total_price'] = $request->amount;
        $res_data['currency']    = $this->currancy;
        $res_data['flag']        = 1;
        $res_data['invoice_id']  = $invoice->id;
        $request->session()->put('invoice_data', $res_data);
        $this->pay_amount = $request->amount;

        return $res_data;
    }

    public function getInvoicePaymentStatus($pay_id, $invoice_id, Request $request)
    {
        if(!empty($invoice_id) && !empty($pay_id))
        {
            $invoice_id   = decrypt($invoice_id);
            $invoice      = Invoice::find($invoice_id);
            $invoice_data = $request->session()->get('invoice_data');
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            if($invoice && !empty($invoice_data))
            {

                // try
                // {
                    $orderID = time();
                    $data    = array(
                        'txref' => $pay_id,
                        'SECKEY' => $this->secret_key,
                        //secret key from pay button generated on rave dashboard
                    );
                    // make request to endpoint using unirest.
                    $headers = array('Content-Type' => 'application/json');
                    $body    = \Unirest\Request\Body::json($data);
                    $url     = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify"; //please make sure to change this to production url when you go live

                    // Make `POST` request and handle response with unirest
                    $response = \Unirest\Request::post($url, $headers, $body);
                    if(!empty($response))
                    {
                        $response = json_decode($response->raw_body, true);
                    }

                    if(isset($response['status']) && $response['status'] == 'success')
                    {
                        $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->transaction_id = $orderID;
                        $invoice_payment->invoice_id     = $invoice->id;
                        $invoice_payment->amount         = isset($invoice_data['total_price']) ? $invoice_data['total_price'] : 0;
                        $invoice_payment->date           = date('Y-m-d');
                        $invoice_payment->payment_id     = 0;
                        $invoice_payment->payment_type   = 'Flutterwave';
                        $invoice_payment->notes          = '';
                        $invoice_payment->client_id      = Auth::user()->id;
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
                            $resp =  __('Invoice ').\App\Models\Utility::invoiceNumberFormat($invoice->invoice_id) . ' status changed '. (\App\Models\Invoice::$status[$invoice->status]) ;
                            Utility::send_telegram_msg($resp);    
                        }
                        
                        $request->session()->forget('invoice_data');

                        return redirect()->route('invoices.show', $invoice_id)->with('success', __('Invoice paid Successfully!'));
                    }
                    else
                    {
                        return redirect()->route('invoices.show', $invoice_id)->with('error', __('Transaction has been failed.'));
                    }
                // }
                // catch(\Exception $e)
                // {
                //     return redirect()->route('invoices.show', $invoice_id)->with('error', __('Something went wrong.'));
                // }
            }
            else
            {
                return redirect()->route('invoices.show', $invoice_id)->with('error', __('Invoice not found.'));
            }
        }
        else
        {
            return redirect()->route('invoices.index')->with('error', __('Invoice not found.'));
        }
    }
}
