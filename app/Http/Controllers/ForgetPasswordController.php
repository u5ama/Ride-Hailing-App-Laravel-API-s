<?php

namespace App\Http\Controllers;

use App\BaseAppSocialLinks;
use App\Company;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Mail\ResetPasswordEmail;

class ForgetPasswordController extends Controller
{

    public function forgetPasswordForm()
    {
        return view('auth.passwords.forget');
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator->errors())
                ->withInput($request->input());
        }

        $user = Company::where(['email' => $request->input('email')])->first();
        $socialLinks = BaseAppSocialLinks::all();
        if($user){

            $token = Password::getRepository()->create($user);
            $array = [
                'name'                   => $user->com_name,
                'actionUrl'              => route('reset-password', [$token]),
                'mail_title'             => 'Password Reset',
                'reset_password_subject' => 'Reset Password',
                'main_title_text'        => 'Password Reset',
                'socialLinks'        =>  $socialLinks,
            ];
            Mail::to($user->email)->send(new ResetPasswordEmail($array));

            return redirect()->back()
                ->with('success_message', 'Please check your mail & reset your password');

        }else{
            return redirect()->back()->with(['error_message' => 'Email not found']);
        }

    }
}
