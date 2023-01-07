<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Redirect;
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

    public function __construct()
    {
      $this->middleware('guest')->except('logout');
    }

    /**
     * @return property guard use for login
     *
     */
    public function guard()
    {
      return auth()->guard('admin');
    }

    public function showLoginForm()
    {
//dd(auth()->guard('admin')->user());
        if(auth()->guard('admin')->check() && auth()->guard('admin')->user()->user_type == 'admin'){
            return redirect()->away('https://app.ridewhipp.com/admin/admin');
        }else {
            return view('admin.auth.login');
        }
    }

    /**
     * Logout from admin
     */
    public function logout()
    {
        auth()->guard('admin')->logout();
        Session::flush();
        return redirect(route('admin::login'));
    }
}
