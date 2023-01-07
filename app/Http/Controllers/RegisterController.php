<?php

namespace App\Http\Controllers;

use App\BaseAppSocialLinks;
use App\Company;
use App\EmailBodyTranslation;
use App\EmailFooterTranslation;
use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\Mail\CompanyWelcomeEmail;
use App\Mail\WelcomeEmail;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;


class RegisterController extends Controller
{

    public function index()
    {
        return view('auth.register');
    }

    public function registerPost(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:companies',
            'password' => 'required|min:5|max:16',
            'com_contact_number' => 'required|unique:companies',
                    ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator->errors())
                ->withInput($request->input());
        }

        $comlogo = '';
      if ($request->hasFile('com_logo')){
            $mime= $request->com_logo->getMimeType();
            $logo = $request->file('com_logo');
            $logo_name =  preg_replace('/\s+/', '', $logo->getClientOriginalName());
            $logoName = time() .'-'.$logo_name;
            $logo->move('./assets/company/logo/', $logoName);
            $comlogo = 'assets/company/logo/'.$logoName;
        }


        $company = Company::create([
            'com_name' => $request->com_name,
            'com_contact_number' => $request->com_contact_number,
            'com_country_code' => $request->country_code,
            'com_full_contact_number' => $request->com_full_contact_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'com_status' =>4,
            'com_radius' =>1,
            'com_created_by' =>1,
            'com_updated_by'=>1,
        ]);

        $socialLinks = BaseAppSocialLinks::all();

        $company_name = $request->com_name;

        $header = EmailHeader::where('id',1)->first();
        $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 1, 'locale' => 'en'])->first();

        $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 1, 'locale' => 'en'])->first();

        $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 1,'locale' => 'en'])->first();
        $langtxt = 'en';
        $user_type = "user";

        Mail::to($request->email)->send(new CompanyWelcomeEmail($company_name,$company->id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));

        return redirect()->route('register/success');
    }

    public function successMessage(){

        return view('auth.registersuccess');
    }


}
