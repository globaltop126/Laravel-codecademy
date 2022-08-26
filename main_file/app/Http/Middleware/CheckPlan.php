<?php

namespace App\Http\Middleware;

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
            if($user->type == 'Owner' && $user->is_trial_done < 2)
            {
                if($user->is_trial_done == 1 && $user->plan_expire_date < date('Y-m-d'))
                {
                    $user->is_trial_done = 2;

                    $user->save();
                }
            }

            // Check plan when owner login
            if($user->type == 'Owner' && (empty($user->plan_expire_date) || $user->plan_expire_date < date('Y-m-d')))
            {
                $error = $user->is_trial_done ? __('Your Plan is expired.') : ($user->plan_expire_date < date('Y-m-d') ? __('Please upgrade your plan') : '');
               
                return redirect()->route('plans.index')->with('error', $error);
            }
        }

        return $next($request);
    }
}
