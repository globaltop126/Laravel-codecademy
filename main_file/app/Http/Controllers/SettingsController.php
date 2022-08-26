<?php
namespace App\Http\Controllers;
use App\Mail\TestMail;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        if(Auth::user()->type == 'admin')
        {
            $timezones      = config('timezones');
            $payment_detail = Utility::getPaymentSetting();
            $settings  = Utility::settings();  
            return view('users.setting', compact('timezones', 'payment_detail','settings'));
        }
        else
        {
            $details        = Auth::user()->decodeDetails();
            $payment_detail = Utility::getPaymentSetting(Auth::user()->id);
            $settings  = Utility::settingsById();  
            return view('users.owner_setting', compact('details', 'payment_detail','settings'));
        }
    }

    public function store(Request $request)
    {   
        // dd($request->all());
        $usr = Auth::user();
        if($usr->type == 'admin')
        {
            $validate = [];

            if($request->from == 'mail')
            {
                $validate = [
                    'mail_driver' => 'required|string',
                    'mail_host' => 'required|string',
                    'mail_port' => 'required|string',
                    'mail_username' => 'required|string',
                    'mail_password' => 'required|string',
                    'mail_from_address' => 'required|string',
                    'mail_from_name' => 'required|string',
                    'mail_encryption' => 'required|string',
                ];
            }
            elseif($request->from == 'payment')
            {
                $validate = [
                    'currency' => 'required|max:3',
                    'currency_code' => 'required|string|max:5',
                ];

                if(isset($request->enable_stripe) && $request->enable_stripe == 'on')
                {
                    $validate['stripe_key']    = 'required|string';
                    $validate['stripe_secret'] = 'required|string';
                }
                if(isset($request->enable_paypal) && $request->enable_paypal == 'on')
                {
                    $validate['paypal_client_id']  = 'required|string';
                    $validate['paypal_secret_key'] = 'required|string';
                }
                if(isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on')
                {
                    $validate['paystack_public_key'] = 'required|string';
                    $validate['paystack_secret_key'] = 'required|string';
                }
                if(isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on')
                {
                    $validate['flutterwave_public_key'] = 'required|string';
                    $validate['flutterwave_secret_key'] = 'required|string';
                }
                if(isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on')
                {
                    $validate['razorpay_public_key'] = 'required|string';
                    $validate['razorpay_secret_key'] = 'required|string';
                }
                if(isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on')
                {
                    $validate['mercado_mode']       = 'required|string';
                    $validate['mercado_access_token']     = 'required|string';
                }
                if(isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on')
                {
                    $validate['paytm_mode']          = 'required|string';
                    $validate['paytm_merchant_id']   = 'required|string';
                    $validate['paytm_merchant_key']  = 'required|string';
                    $validate['paytm_industry_type'] = 'required|string';
                }
                if(isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on')
                {
                    $validate['mollie_api_key']    = 'required|string';
                    $validate['mollie_profile_id'] = 'required|string';
                    $validate['mollie_partner_id'] = 'required|string';
                }
                if(isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on')
                {
                    $validate['skrill_email'] = 'required|email';
                }
                if(isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on')
                {
                    $validate['coingate_mode']       = 'required|string';
                    $validate['coingate_auth_token'] = 'required|string';
                }
                if(isset($request->is_paymentwall_enabled) && $request->is_paymentwall_enabled == 'on')
                {
                    $validate['paymentwall_public_key']       = 'required|string';
                    $validate['paymentwall_private_key'] = 'required|string';
                }
            }
            elseif($request->from == 'pusher')
            {
                $validate = [
                    'pusher_app_id' => 'required|string',
                    'pusher_app_key' => 'required|string',
                    'pusher_app_secret' => 'required|string',
                    'pusher_app_cluster' => 'required|string',
                ];
            }

            $validator = Validator::make(
                $request->all(), $validate
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            if($request->favicon)
            {
                $request->validate(['favicon' => 'required|image|mimes:png|max:2048']);
                $faviconName = 'favicon.png';
                $request->favicon->storeAs('logo', $faviconName);
            }
            if($request->full_logo)
            {
                $request->validate(['full_logo' => 'required|image|mimes:png|max:2048']);
                $logoName = 'logo.png';
                $request->full_logo->storeAs('logo', $logoName);
            }

            if($request->from == 'site_setting')
            {
                $post = $request->all();
                unset($post['_token'], $post['full_logo'], $post['favicon'], $post['from'], $post['timezone']);

                $post['header_text']    = (!isset($request->header_text) && empty($request->header_text)) ? '' : $request->header_text;
                $post['footer_text']    = (!isset($request->footer_text) && empty($request->footer_text)) ? '' : $request->footer_text;
                $post['enable_landing'] = isset($request->enable_landing) ? $request->enable_landing : 'off';
                $post['enable_rtl']     = isset($request->enable_rtl) ? $request->enable_rtl : 'off';

                $post['color'] = isset($request->color) ? $request->color :'#6fd943';

                // dd($post);
        
                $created_at             = date('Y-m-d H:i:s');
                $updated_at             = date('Y-m-d H:i:s');
                if(!isset($request->gdpr_cookie))
                {
                    $post['gdpr_cookie'] = 'off';
                    
                }

                if(!isset($request->SIGNUP)){
                    $post['SIGNUP'] = 'off';
                }

                  if(!isset($request->cookie_text)){
                    $post['cookie_text'] = '';
                }


                 
                // $cookie_text = (!isset($post['cookie_text']) && empty($post['cookie_text'])) ? '' : $post['cookie_text'];
                  

                // dd($post);
                foreach($post as $key => $data)
                {
                    \DB::insert(
                        'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                          $data,
                                                                                                                                                                                                                          $key,
                                                                                                                                                                                                                          $usr->id,
                                                                                                                                                                                                                          $created_at,
                                                                                                                                                                                                                          $updated_at,
                                                                                                                                                                                                                      ]
                    );
                }

                $arrEnv             = [];
                $arrEnv['TIMEZONE'] = $request->timezone;

                Artisan::call('config:cache');
                Artisan::call('config:clear');

                Utility::setEnvironmentValue($arrEnv);

                return redirect()->back()->with('success', __('Basic Setting updated successfully'));
            }

            if($request->from == 'mail')
            {
                $arrEnv = [
                    'MAIL_DRIVER' => $request->mail_driver,
                    'MAIL_HOST' => $request->mail_host,
                    'MAIL_PORT' => $request->mail_port,
                    'MAIL_USERNAME' => $request->mail_username,
                    'MAIL_PASSWORD' => $request->mail_password,
                    'MAIL_ENCRYPTION' => $request->mail_encryption,
                    'MAIL_FROM_ADDRESS' => $request->mail_from_address,
                    'MAIL_FROM_NAME' => $request->mail_from_name,
                ];

                Artisan::call('config:cache');
                Artisan::call('config:clear');

                $env = Utility::setEnvironmentValue($arrEnv);

                if($env)
                {
                    return redirect()->back()->with('success', __('Mailer Setting updated successfully'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Something is wrong'));
                }
            }

            if($request->from == 'pusher')
            {
                $arrEnv = [
                    'PUSHER_APP_ID' => $request->pusher_app_id,
                    'PUSHER_APP_KEY' => $request->pusher_app_key,
                    'PUSHER_APP_SECRET' => $request->pusher_app_secret,
                    'PUSHER_APP_CLUSTER' => $request->pusher_app_cluster,
                ];

                Artisan::call('config:cache');
                Artisan::call('config:clear');

                $env = Utility::setEnvironmentValue($arrEnv);

                if($env)
                {
                    return redirect()->back()->with('success', __('Pusher Setting updated successfully'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Something is wrong'));
                }
            }

            if($request->from == 'payment')
            {
                $arrEnv = [
                    'CURRENCY' => $request->currency,
                    'CURRENCY_CODE' => $request->currency_code,
                ];

                $request->user = Auth::user()->id;
                
                Artisan::call('config:cache');
                Artisan::call('config:clear');

                Utility::setEnvironmentValue($arrEnv);

                // Save Stripe Detail
                if(isset($request->enable_stripe) && $request->enable_stripe == 'on')
                {
                    $post['enable_stripe']         = $request->enable_stripe;
                    $post['stripe_key']            = $request->stripe_key;
                    $post['stripe_secret']         = $request->stripe_secret;
                    $post['stripe_webhook_secret'] = $request->stripe_webhook_secret;
                }
                else
                {
                    $post['enable_stripe'] = 'off';
                }

                // Save Paypal Detail
                if(isset($request->enable_paypal) && $request->enable_paypal == 'on')
                {
                    $post['enable_paypal']     = $request->enable_paypal;
                    $post['paypal_mode']       = $request->paypal_mode;
                    $post['paypal_client_id']  = $request->paypal_client_id;
                    $post['paypal_secret_key'] = $request->paypal_secret_key;
                }
                else
                {
                    $post['enable_paypal'] = 'off';
                }

                // Save Paystack Detail
                if(isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on')
                {
                    $post['is_paystack_enabled'] = $request->is_paystack_enabled;
                    $post['paystack_public_key'] = $request->paystack_public_key;
                    $post['paystack_secret_key'] = $request->paystack_secret_key;
                }
                else
                {
                    $post['is_paystack_enabled'] = 'off';
                }

                // Save Fluuterwave Detail
                if(isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on')
                {
                    $post['is_flutterwave_enabled'] = $request->is_flutterwave_enabled;
                    $post['flutterwave_public_key'] = $request->flutterwave_public_key;
                    $post['flutterwave_secret_key'] = $request->flutterwave_secret_key;
                }
                else
                {
                    $post['is_flutterwave_enabled'] = 'off';
                }

                // Save Razorpay Detail
                if(isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on')
                {
                    $post['is_razorpay_enabled'] = $request->is_razorpay_enabled;
                    $post['razorpay_public_key'] = $request->razorpay_public_key;
                    $post['razorpay_secret_key'] = $request->razorpay_secret_key;
                }
                else
                {
                    $post['is_razorpay_enabled'] = 'off';
                }

                // Save Marcado Detail
                // if(isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on')
                // {
                //     $post['is_mercado_enabled'] = $request->is_mercado_enabled;
                //     $post['mercado_app_id']     = $request->mercado_app_id;
                //     $post['mercado_secret_key'] = $request->mercado_secret_key;
                // }
                // else
                // {
                //     $post['is_mercado_enabled'] = 'off';
                // }

                if(isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on')
                {
                    $request->validate(
                        [
                            'mercado_access_token' => 'required|string',
                        ]
                    );
                    $post['is_mercado_enabled'] = $request->is_mercado_enabled;
                    $post['mercado_access_token']     = $request->mercado_access_token;
                    $post['mercado_mode'] = $request->mercado_mode;
                }
                else
                {
                    $post['is_mercado_enabled'] = 'off';
                }

                // Save Paytm Detail
                if(isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on')
                {
                    $post['is_paytm_enabled']    = $request->is_paytm_enabled;
                    $post['paytm_mode']          = $request->paytm_mode;
                    $post['paytm_merchant_id']   = $request->paytm_merchant_id;
                    $post['paytm_merchant_key']  = $request->paytm_merchant_key;
                    $post['paytm_industry_type'] = $request->paytm_industry_type;
                }
                else
                {
                    $post['is_paytm_enabled'] = 'off';
                }

                // Save Mollie Detail
                if(isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on')
                {
                    $post['is_mollie_enabled'] = $request->is_mollie_enabled;
                    $post['mollie_api_key']    = $request->mollie_api_key;
                    $post['mollie_profile_id'] = $request->mollie_profile_id;
                    $post['mollie_partner_id'] = $request->mollie_partner_id;
                }
                else
                {
                    $post['is_mollie_enabled'] = 'off';
                }

                // Save Skrill Detail
                if(isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on')
                {
                    $post['is_skrill_enabled'] = $request->is_skrill_enabled;
                    $post['skrill_email']      = $request->skrill_email;
                }
                else
                {
                    $post['is_skrill_enabled'] = 'off';
                }

                // Save Coingate Detail
                if(isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on')
                {
                    $post['is_coingate_enabled'] = $request->is_coingate_enabled;
                    $post['coingate_mode']       = $request->coingate_mode;
                    $post['coingate_auth_token'] = $request->coingate_auth_token;
                }
                else
                {
                    $post['is_coingate_enabled'] = 'off';
                }

                //save paymentwall Detail
                if(isset($request->is_paymentwall_enabled) && $request->is_paymentwall_enabled == 'on')
                {
                    $request->validate(
                        [
                            'paymentwall_public_key' => 'required|string',
                            'paymentwall_private_key' => 'required|string',
                        ]
                    );
                    $post['is_paymentwall_enabled'] = $request->is_paymentwall_enabled;
                    $post['paymentwall_public_key'] = $request->paymentwall_public_key;
                    $post['paymentwall_private_key'] = $request->paymentwall_private_key;
                }
                else
                {
                    $post['is_paymentwall_enabled'] = 'off';
                }

                $created_at = date('Y-m-d H:i:s');
                $updated_at = date('Y-m-d H:i:s');

                foreach($post as $key => $data)
                {
                    \DB::insert(
                        'insert into payment_settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                                                                                                                                                                                                                                 $data,
                                                                                                                                                                                                                                 $key,
                                                                                                                                                                                                                                 $request->user,
                                                                                                                                                                                                                                 $created_at,
                                                                                                                                                                                                                                 $updated_at,
                                                                                                                                                                                                                             ]
                    );
                }

                return redirect()->back()->with('success', __('Payment Setting updated successfully'));
            }
        }
        else
        {
            $details = $usr->decodeDetails();
            if($request->from == 'invoice_setting')
            {
                if($request->light_logo)
                {
                    $request->validate(['light_logo' => 'required|image|mimes:png|max:2048']);
                    if(!empty($details['light_logo']) && $details['light_logo'] != 'logo/logo.png')
                    {
                        Utility::checkFileExistsnDelete([$details['light_logo']]);
                    }
                    $light_logo = $usr->id . '_light' . time() . '.' . $request->light_logo->getClientOriginalExtension();
                    $request->light_logo->storeAs('invoice_logo', $light_logo);
                    $details['light_logo'] = 'invoice_logo/' . $light_logo;
                }

                if($request->dark_logo)
                {
                    $request->validate(['dark_logo' => 'required|image|mimes:png|max:2048']);
                    if(!empty($details['dark_logo']) && $details['dark_logo'] != 'logo/logo.png')
                    {
                        Utility::checkFileExistsnDelete([$details['dark_logo']]);
                    }
                    $dark_logo = $usr->id . '_dark' . time() . '.' . $request->dark_logo->getClientOriginalExtension();
                    $request->dark_logo->storeAs('invoice_logo', $dark_logo);
                    $details['dark_logo'] = 'invoice_logo/' . $dark_logo;
                }

                $details['invoice_footer_title'] = (!empty($request->invoice_footer_title)) ? $request->invoice_footer_title : '';
                $details['invoice_footer_note']  = (!empty($request->invoice_footer_note)) ? $request->invoice_footer_note : '';

                $usr->details = json_encode($details);
                $usr->save();

                return redirect()->route('settings')->with('success', __('Invoice Setting successfully updated!'));
            }
            elseif($request->from == 'billing_setting')
            {
                $validator = Validator::make(
                    $request->all(), [
                                       'address' => 'required',
                                       'city' => 'required',
                                       'state' => 'required',
                                       'zipcode' => 'required',
                                       'country' => 'required',
                                       'telephone' => 'required|numeric',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('settings')->with('error', $messages->first());
                }

                $post = $request->all();
                unset($post['_token'], $post['from']);

                foreach($post as $key => $data)
                {
                    $details[$key] = $data;
                }

                $usr->details = json_encode($details);
                $usr->save();

                return redirect()->route('settings')->with('success', __('Billing Setting successfully updated!'));
            }
            elseif($request->from == 'payment')
            {
                $validate = [];

                if(isset($request->enable_stripe) && $request->enable_stripe == 'on')
                {
                    $validate['stripe_key']    = 'required|string';
                    $validate['stripe_secret'] = 'required|string';
                }
                if(isset($request->enable_paypal) && $request->enable_paypal == 'on')
                {
                    $validate['paypal_client_id']  = 'required|string';
                    $validate['paypal_secret_key'] = 'required|string';
                }
                if(isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on')
                {
                    $validate['paystack_public_key'] = 'required|string';
                    $validate['paystack_secret_key'] = 'required|string';
                }
                if(isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on')
                {
                    $validate['flutterwave_public_key'] = 'required|string';
                    $validate['flutterwave_secret_key'] = 'required|string';
                }
                if(isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on')
                {
                    $validate['razorpay_public_key'] = 'required|string';
                    $validate['razorpay_secret_key'] = 'required|string';
                }
                if(isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on')
                {
                    $validate['mercado_mode']       = 'required|string';
                    $validate['mercado_access_token']     = 'required|string';
                }
                if(isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on')
                {
                    $validate['paytm_mode']          = 'required|string';
                    $validate['paytm_merchant_id']   = 'required|string';
                    $validate['paytm_merchant_key']  = 'required|string';
                    $validate['paytm_industry_type'] = 'required|string';
                }
                if(isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on')
                {
                    $validate['mollie_api_key']    = 'required|string';
                    $validate['mollie_profile_id'] = 'required|string';
                    $validate['mollie_partner_id'] = 'required|string';
                }
                if(isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on')
                {
                    $validate['skrill_email'] = '';
                }
                if(isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on')
                {
                    $validate['coingate_mode']       = 'required|string';
                    $validate['coingate_auth_token'] = 'required|string';
                }
                if(isset($request->is_paymentwall_enabled) && $request->is_paymentwall_enabled == 'on')
                {
                    $validate['paymentwall_public_key']       = 'required|string';
                    $validate['paymentwall_private_key'] = 'required|string';
                }


                $validator = Validator::make(
                    $request->all(), $validate
                );

                if($validator->fails())
                {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                // Save Stripe Detail
                if(isset($request->enable_stripe) && $request->enable_stripe == 'on')
                {
                    $post['enable_stripe'] = $request->enable_stripe;
                    $post['stripe_key']    = $request->stripe_key;
                    $post['stripe_secret'] = $request->stripe_secret;
                }
                else
                {
                    $post['enable_stripe'] = 'off';
                }

                // Save Paypal Detail
                if(isset($request->enable_paypal) && $request->enable_paypal == 'on')
                {
                    $post['enable_paypal']     = $request->enable_paypal;
                    $post['paypal_mode']       = $request->paypal_mode;
                    $post['paypal_client_id']  = $request->paypal_client_id;
                    $post['paypal_secret_key'] = $request->paypal_secret_key;
                }
                else
                {
                    $post['enable_paypal'] = 'off';
                }

                // Save Paystack Detail
                if(isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on')
                {
                    $post['is_paystack_enabled'] = $request->is_paystack_enabled;
                    $post['paystack_public_key'] = $request->paystack_public_key;
                    $post['paystack_secret_key'] = $request->paystack_secret_key;
                }
                else
                {
                    $post['is_paystack_enabled'] = 'off';
                }

                // Save Fluuterwave Detail
                if(isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on')
                {
                    $post['is_flutterwave_enabled'] = $request->is_flutterwave_enabled;
                    $post['flutterwave_public_key'] = $request->flutterwave_public_key;
                    $post['flutterwave_secret_key'] = $request->flutterwave_secret_key;
                }
                else
                {
                    $post['is_flutterwave_enabled'] = 'off';
                }

                // Save Razorpay Detail
                if(isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on')
                {
                    $post['is_razorpay_enabled'] = $request->is_razorpay_enabled;
                    $post['razorpay_public_key'] = $request->razorpay_public_key;
                    $post['razorpay_secret_key'] = $request->razorpay_secret_key;
                }
                else
                {
                    $post['is_razorpay_enabled'] = 'off';
                }

                // Save Marcado Detail
                // if(isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on')
                // {
                //     $post['is_mercado_enabled'] = $request->is_mercado_enabled;
                //     $post['mercado_app_id']     = $request->mercado_app_id;
                //     $post['mercado_secret_key'] = $request->mercado_secret_key;
                // }
                // else
                // {
                //     $post['is_mercado_enabled'] = 'off';
                // }

                if(isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on')
                {
                    $request->validate(
                        [
                            'mercado_access_token' => 'required|string',
                        ]
                    );
                    $post['is_mercado_enabled'] = $request->is_mercado_enabled;
                    $post['mercado_access_token']     = $request->mercado_access_token;
                    $post['mercado_mode'] = $request->mercado_mode;
                }
                else
                {
                    $post['is_mercado_enabled'] = 'off';
                }

                // Save Paytm Detail
                if(isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on')
                {
                    $post['is_paytm_enabled']    = $request->is_paytm_enabled;
                    $post['paytm_mode']          = $request->paytm_mode;
                    $post['paytm_merchant_id']   = $request->paytm_merchant_id;
                    $post['paytm_merchant_key']  = $request->paytm_merchant_key;
                    $post['paytm_industry_type'] = $request->paytm_industry_type;
                }
                else
                {
                    $post['is_paytm_enabled'] = 'off';
                }

                // Save Mollie Detail
                if(isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on')
                {
                    $post['is_mollie_enabled'] = $request->is_mollie_enabled;
                    $post['mollie_api_key']    = $request->mollie_api_key;
                    $post['mollie_profile_id'] = $request->mollie_profile_id;
                    $post['mollie_partner_id'] = $request->mollie_partner_id;
                }
                else
                {
                    $post['is_mollie_enabled'] = 'off';
                }

                // Save Skrill Detail
                if(isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on')
                {
                    $post['is_skrill_enabled'] = $request->is_skrill_enabled;
                    $post['skrill_email']      = $request->skrill_email;
                }
                else
                {
                    $post['is_skrill_enabled'] = 'off';
                }

                // Save Coingate Detail
                if(isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on')
                {
                    $post['is_coingate_enabled'] = $request->is_coingate_enabled;
                    $post['coingate_mode']       = $request->coingate_mode;
                    $post['coingate_auth_token'] = $request->coingate_auth_token;
                }
                else
                {
                    $post['is_coingate_enabled'] = 'off';
                }

                // Save Paymentwall Detail 
                if(isset($request->is_paymentwall_enabled) && $request->is_paymentwall_enabled == 'on')
                {
                    $request->validate(
                        [
                            'paymentwall_public_key' => 'required|string',
                            'paymentwall_private_key' => 'required|string',
                        ]
                    );
                    $post['is_paymentwall_enabled'] = $request->is_paymentwall_enabled;
                    $post['paymentwall_public_key'] = $request->paymentwall_public_key;
                    $post['paymentwall_private_key'] = $request->paymentwall_private_key;
                }
                else
                {
                    $post['is_paymentwall_enabled'] = 'off';
                }

                $created_at = date('Y-m-d H:i:s');
                $updated_at = date('Y-m-d H:i:s');

                foreach($post as $key => $data)
                {
                    \DB::insert(
                        'insert into payment_settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                                                                                                                                                                                                                                 $data,
                                                                                                                                                                                                                                 $key,
                                                                                                                                                                                                                                 Auth::user()->id,
                                                                                                                                                                                                                                 $created_at,
                                                                                                                                                                                                                                 $updated_at,
                                                                                                                                                                                                                             ]
                    );
                }

                return redirect()->route('settings')->with('success', __('Payment Setting successfully updated!'));
            }
            elseif($request->from == 'tracker')
            {
                $validator = Validator::make(
                    $request->all(), [
                        'interval_time' => 'required|numeric',
                    ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('settings')->with('error', $messages->first());
                }

                $post = $request->all();
                unset($post['_token'], $post['from']);

                foreach($post as $key => $data)
                {
                    $details[$key] = $data;
                }

                $usr->details = json_encode($details);
                $usr->save();
                return redirect()->route('settings')->with('success', __('Tracker Setting successfully updated!'));
            } 

            elseif($request->from == 'site_setting')
            {   
                $post = $request->all();
                // dd($post);
                unset($post['_token'], $post['full_logo'], $post['favicon'], $post['from'], $post['timezone']);

                $post['color'] = isset($request->color) ? $request->color :'#6fd943';

                $created_at             = date('Y-m-d H:i:s');
                $updated_at             = date('Y-m-d H:i:s');

                
                //dd($post);
                foreach($post as $key => $data)
                {
                    \DB::insert(
                        'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                          $data,
                                                                                                                                                                                                                          $key,
                                                                                                                                                                                                                          $usr->id,
                                                                                                                                                                                                                          $created_at,
                                                                                                                                                                                                                          $updated_at,
                                                                                                                                                                                                                      ]
                    );
                }

               
                // $arrEnv             = [];
                Artisan::call('config:cache');
                Artisan::call('config:clear');

                // Utility::setEnvironmentValue($arrEnv);

                 return redirect()->back()->with('success', __('Basic Setting updated successfully'));
            }
        }
    }

    public function testEmail(Request $request)
    {
        $user = Auth::user();
        if($user->type == 'admin')
        {
            $data                      = [];
            $data['mail_driver']       = $request->mail_driver;
            $data['mail_host']         = $request->mail_host;
            $data['mail_port']         = $request->mail_port;
            $data['mail_username']     = $request->mail_username;
            $data['mail_password']     = $request->mail_password;
            $data['mail_encryption']   = $request->mail_encryption;
            $data['mail_from_address'] = $request->mail_from_address;
            $data['mail_from_name']    = $request->mail_from_name;

            return view('users.test_email', compact('data'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function testEmailSend(Request $request)
    {
        if(Auth::user()->type == 'admin')
        {
            $validator = Validator::make(
                $request->all(), [
                                   'email' => 'required|email',
                                   'mail_driver' => 'required',
                                   'mail_host' => 'required',
                                   'mail_port' => 'required',
                                   'mail_username' => 'required',
                                   'mail_password' => 'required',
                                   'mail_from_address' => 'required',
                                   'mail_from_name' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            try
            {
                config(
                    [
                        'mail.driver' => $request->mail_driver,
                        'mail.host' => $request->mail_host,
                        'mail.port' => $request->mail_port,
                        'mail.encryption' => $request->mail_encryption,
                        'mail.username' => $request->mail_username,
                        'mail.password' => $request->mail_password,
                        // 'mail.from.address' => $request->mail_username,
                        // 'mail.from.name' => config('name'),
                        'mail.from.address' => $request->mail_from_address,
                        'mail.from.name' => $request->mail_from_name,
                    ]
                );
                Mail::to($request->email)->send(new TestMail());
            }
            catch(\Exception $e)
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'message' => $e->getMessage(),
                    ]
                );
                //            return redirect()->back()->with('error', 'Something is Wrong.');
            }

            return response()->json(
                [
                    'is_success' => true,
                    'message' => __('Email send Successfully'),
                ]
            );
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => __('Permission Denied.'),
                ]
            );
        }
    }

    public function saveZoomSettings(Request $request)
    {
        $post = $request->all();

        unset($post['_token']);
        $created_by = \Auth::user()->creatorId();
        
        foreach($post as $key => $data)
        {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', [
                                                                                                                                                                                $data,
                                                                                                                                                                                $key,
                                                                                                                                                                                $created_by,
                                                                                                                                                                                date('Y-m-d H:i:s'),
                                                                                                                                                                                date('Y-m-d H:i:s'),
                                                                                                                                                                            ]
            );
        }
        return redirect()->back()->with('success', __('Setting added successfully saved.'));
    }

    public function slack(Request $request){
        $post = [];
        $post['slack_webhook'] = $request->input('slack_webhook');
        $post['is_project_enabled'] = $request->has('is_project_enabled')?$request->input('is_project_enabled'):0;
        $post['task_notification'] = $request->has('task_notification')?$request->input('task_notification'):0;
        $post['invoice_notificaation'] = $request->has('invoice_notificaation')?$request->input('invoice_notificaation'):0;
        $post['task_move_notificaation'] = $request->has('task_move_notificaation')?$request->input('task_move_notificaation'):0;
        $post['mileston_notificaation'] = $request->has('mileston_notificaation')?$request->input('mileston_notificaation'):0;
        $post['milestone_status_notificaation'] = $request->has('milestone_status_notificaation')?$request->input('milestone_status_notificaation'):0;
        $post['invoice_status_notificaation'] = $request->has('invoice_status_notificaation')?$request->input('invoice_status_notificaation'):0;
        
        if(isset($post) && !empty($post) && count($post) > 0)
        {
            $created_at = $updated_at = date('Y-m-d H:i:s');

            foreach($post as $key => $data)
            {
                DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $data,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      Auth::user()->id,
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }
        }

        return redirect()->back()->with('success', __('Settings updated successfully.'));
    }
    public function telegram(Request $request){

        $validator = Validator::make(
        $request->all(), [
                           'telegram_accestoken' => 'required',
                           'telegram_chatid' => 'required',
                       ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->route('settings')->with('error', $messages->first());
        }


        $post = [];
        $post['telegram_accestoken'] = $request->input('telegram_accestoken');
        $post['telegram_chatid'] = $request->input('telegram_chatid');
        $post['telegram_is_project_enabled'] = $request->has('telegram_is_project_enabled')?$request->input('telegram_is_project_enabled'):0;
        $post['telegram_task_notification'] = $request->has('telegram_task_notification')?$request->input('telegram_task_notification'):0;
        $post['telegram_invoice_notificaation'] = $request->has('telegram_invoice_notificaation')?$request->input('telegram_invoice_notificaation'):0;
        $post['telegram_task_move_notificaation'] = $request->has('telegram_task_move_notificaation')?$request->input('telegram_task_move_notificaation'):0;
        $post['telegram_mileston_notificaation'] = $request->has('telegram_mileston_notificaation')?$request->input('telegram_mileston_notificaation'):0;
        $post['telegram_milestone_status_notificaation'] = $request->has('telegram_milestone_status_notificaation')?$request->input('telegram_milestone_status_notificaation'):0;
        $post['telegram_invoice_status_notificaation'] = $request->has('telegram_invoice_status_notificaation')?$request->input('telegram_invoice_status_notificaation'):0;
        
        if(isset($post) && !empty($post) && count($post) > 0)
        {
            $created_at = date('Y-m-d H:i:s');

            $updated_at = date('Y-m-d H:i:s');

            foreach($post as $key => $data)
            {
                DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $data,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      Auth::user()->id,
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }
        }

        return redirect()->back()->with('success', __('Settings updated successfully.'));
    }

    public function recaptchaSettingStore(Request $request)
    {
        // dd($request->all());
        //return redirect()->back()->with('error', __('This operation is not perform due to demo mode.'));
        $user = \Auth::user();
        $rules = [];
        if($request->recaptcha_module == 'yes')
        {
            $rules['google_recaptcha_key'] = 'required|string|max:50';
            $rules['google_recaptcha_secret'] = 'required|string|max:50';
        }
        $validator = \Validator::make(
            $request->all(), $rules
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        $arrEnv = [
            'RECAPTCHA_MODULE' => $request->recaptcha_module ?? 'no',
            'NOCAPTCHA_SITEKEY' => $request->google_recaptcha_key,
            'NOCAPTCHA_SECRET' => $request->google_recaptcha_secret,
        ];
        if(Utility::setEnvironmentValue($arrEnv))
        {
            return redirect()->back()->with('success', __('Recaptcha Settings updated successfully'));
        }
        else
        {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

}
