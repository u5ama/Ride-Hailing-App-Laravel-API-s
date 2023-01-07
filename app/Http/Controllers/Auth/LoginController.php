<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'admin/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin')->except('logout');
    }
    public function showLoginForm(){
        if(auth()->guard('admin')->check() && auth()->guard('admin')->user()->user_type == 'admin'){
            return redirect()->away('https://app.ridewhipp.com/admin/admin');
        }
        if(auth()->guard('company')->check() && auth()->guard('company')->user()->user_type == 'company'){
            return redirect()->away('https://app.ridewhipp.com/company/company');
        }
        return redirect()->away('https://app.ridewhipp.com');;

    }
}
