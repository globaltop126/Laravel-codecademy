<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Utility;
use App\Models\User;
use App\Models\Plan;
use App\Models\Plan as ModelsPlan;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */ 
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function __construct()
    {
        if(!file_exists(storage_path() . "/installed"))
        {
            header('location:install');
            die;
        }
    }

    /*protected function authenticated(Request $request, $user)
    {
        if($user->delete_status == 1)
        {
            auth()->logout();
        }

        return redirect('/check');
    }*/

    public function store(LoginRequest $request)
    {   

        if(env('RECAPTCHA_MODULE') == 'yes')
        {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }else{
            $validation = [];
        }
        $this->validate($request, $validation);


        $request->authenticate();
        $request->session()->regenerate();
        $user = Auth::user();
        if($user->is_active == 0)
        {
            auth()->logout();
        }

        if($user->type == 'company')
        {
            $free_plan = Plan::where('annual_price', '=', '0.0')->first();
            $plan      = Plan::find($user->plan);
            if($user->plan != $free_plan->id)
            {
                if(date('Y-m-d') > $user->plan_expire_date && $plan->duration != 'unlimited')
                {
                    $user->plan             = $free_plan->id;
                    $user->plan_expire_date = null;
                    $user->save();

                    $clients   = User::where('type', 'client')->where('created_by', '=', \Auth::user()->creatorId())->get();
                    $employees = User::where('type', 'employee')->where('created_by', '=', \Auth::user()->creatorId())->get();

                    if($free_plan->max_client == -1)
                    {
                        foreach($clients as $client)
                        {
                            $client->is_active = 1;
                            $client->save();
                        }
                    }
                    else
                    {
                        $clientCount = 0;
                        foreach($clients as $client)
                        {
                            $clientCount++;
                            if($clientCount <= $free_plan->max_client)
                            {
                                $client->is_active = 1;
                                $client->save();
                            }
                            else
                            {
                                $client->is_active = 0;
                                $client->save();
                            }
                        }

                    }


                    if($free_plan->max_employee == -1)
                    {
                        foreach($employees as $employee)
                        {
                            $employee->is_active = 1;
                            $employee->save();
                        }
                    }
                    else
                    {
                        $employeeCount = 0;
                        foreach($employees as $employee)
                        {
                            $employeeCount++;
                            if($employeeCount <= $free_plan->max_employee)
                            {
                                $employee->is_active = 1;
                                $employee->save();
                            }
                            else
                            {
                                $employee->is_active = 0;
                                $employee->save();
                            }
                        }
                    }

                    return redirect()->route('dashboard')->with('error', 'Your plan expired limit is over, please upgrade your plan');
                }
            }

        }
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function showLoginForm($lang = '')
    {
        if(empty($lang))
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.login', compact('lang'));
    }

    // public function showLoginForm($lang = '')
    // {
    //     if($lang == '')
    //     {
    //         $lang = \App\models\Utility::getValByName('default_language');
    //     }
    //     \App::setLocale($lang);

    //     return view('auth.login', compact('lang'));
    // }

    public function showLinkRequestForm($lang = '')
    {
        if(empty($lang))
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.passwords.email', compact('lang'));
        /*return view('auth.passwords.email', compact('lang'));*/
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {   
       
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
