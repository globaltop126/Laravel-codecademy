<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function create()
    {
        if(Utility::getValByName('SIGNUP') == 'on'){
        return view('auth.register');
        }
        else{
            return abort('404' , 'Page Not Found');
        }
    }


    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if(env('RECAPTCHA_MODULE') == 'yes')
        {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }else{
            $validation = [];
        }
        $this->validate($request, $validation);


        $default_language = Utility::getValByName('default_language');
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'company',
            'lang' => !empty($default_language) ? $default_language : 'en',
            // 'plan' => Plan::first()->id,
            'created_by' => 1,
        ]);
        
        // $user->userDefaultData();


        event(new Registered($user));

        Auth::login($user);

        //return redirect()->route('dashboard')->with('error', 'Your plan expired limit is over, please upgrade your plan');
        //  return $user;
        return redirect(RouteServiceProvider::HOME);

    }

    // Register Form
    public function showRegistrationForm($lang = 'en')
    {
        if(Utility::getValByName('SIGNUP') == 'on'){
            \App::setLocale($lang);
            return view('auth.register',compact('lang'));
        }
        else{
            return abort('404' , 'Page Not Found');
        }
       
    }
}
