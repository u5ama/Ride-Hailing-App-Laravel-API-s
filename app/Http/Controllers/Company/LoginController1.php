<?php

namespace App\Http\Controllers\Company;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index()
    {
        
        return view('company.auth.login');
    }

    public function loginCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()
                ->route('company::login')
                ->withErrors($validator)
                ->withInput();

        }

        $user = Company::where('com_email', $request->input('email'))
            ->where('com_status', 5)
            ->first();

        if (!$user) {
            return redirect()
                ->route('company::login')
                ->withErrors(['email' => ['Your account is not approved']])
                ->withInput();
        }

        if (!Hash::check($request->input('password'), $user->com_password)) {
            return redirect()
                ->route('company::login')
                ->withErrors(['password' => ['Invalid password']])
                ->withInput();
        } else {
            Auth::login($user);
            return redirect()->route('company::company');
        }

    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('company/login');
    }
}
