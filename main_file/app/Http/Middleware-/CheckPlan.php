<?php

namespace App\Http\Middleware;

use App\Models\Order;
use App\Models\Plan;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPlan
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check())
        {
            $user = Auth::user();
            // Check plan trial
            if($user->plan != 1 && !empty($user->plan_expire_date))
            {
                if($user->type != 'admin' && $user->is_trial_done < 2)
                {
                    if($user->is_trial_done == 1 && $user->plan_expire_date < date('Y-m-d'))
                    {
                        $user->is_trial_done = 2;
                        $user->save();
                    }
                }

                if($user->type != 'admin' && (empty($user->plan_expire_date) || $user->plan_expire_date < date('Y-m-d')))
                {
                    $plan = Plan::find(1);
                    $user->assignPlan(1);
                    if(!empty($plan))
                    {
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
                                'price' => 0,
                                'price_currency' => !empty(env('CURRENCY_CODE')) ? env('CURRENCY_CODE') : 'usd',
                                'txn_id' => '',
                                'payment_type' => __('Zero Price'),
                                'payment_status' => 'succeeded',
                                'receipt' => null,
                                'user_id' => $user->id,
                            ]
                        );
                    }

                    // $error = $user->is_trial_done ? __('Your Plan is expired.') : ($user->plan_expire_date < date('Y-m-d') ? __('Please upgrade your plan') : '');

                    return redirect('/checks');
                }
            }
        }

        return $next($request);
    }
}
