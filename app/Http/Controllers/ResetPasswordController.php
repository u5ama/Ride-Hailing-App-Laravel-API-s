<?php

namespace App\Http\Controllers;

use App\Company;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;


class ResetPasswordController extends Controller
{

    public function resetPasswordForm($token)
    {

        return view('auth.passwords.reset', ['token' => $token]);


    }

    public function resetPasswordSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8|max:16',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator->errors())
                ->withInput($request->input());
        }

        $password = $request->input('password');
        // Validate the token
        $tokenData = DB::table('password_resets')
            ->where('email', $request->input('email'))->first();

        if (!$tokenData) {
            return redirect()->back()
                ->with('error_message', 'This link is expire!')
                ->withInput($request->input());
        } elseif (Hash::check($request->input('token'), $tokenData->token)) {


            $user = Company::where(['email' => $tokenData->email])->first();
            if (!$user) return redirect()->back()->withErrors(['email' => 'Email not found']);

            $user->password = bcrypt($password);
            $user->update();


            //Delete the token
            DB::table('password_resets')->where('email', $user->email)
                ->delete();

            //Send Email Reset Success Email
            return redirect()->route('company::login');

        }
    }
}
