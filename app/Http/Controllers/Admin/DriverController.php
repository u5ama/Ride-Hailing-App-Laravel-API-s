<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppSocialLinks;
use App\BaseNumber;
use App\Company;
use App\Driver;
use App\DriverProfile;
use App\EmailBodyTranslation;
use App\EmailFooterTranslation;
use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\LanguageString;
use App\Mail\DriverStatusEmail;
use App\Mail\WelcomeDriverEmail;
use App\Mail\WelcomeEmail;
use App\TransportType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class DriverController extends Controller
{
    /**
     * Display a listing of the Driver.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {

    }

    /**
     * Show the form for creating a new Driver.
     *
     * @return void
     */
    public function create()
    {

    }

    /**
     * Add new Driver Resource
     * @param $id
     * @return Factory|View
     */
    public function addDriver($id)
    {
        $transports = TransportType::listsTranslations('name')->get();
        return view('admin.company.driver.create', ['company_id' => $id, 'transports' => $transports]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == NULL) {
            $validator_array = [
                'du_full_name' => 'required',
                'du_mobile_number' => 'required'
            ];

            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $driver = new Driver();

            if ($request->hasFile('du_profile_pic')) {
                $mime = $request->du_profile_pic->getMimeType();
                $pic = $request->file('du_profile_pic');
                $pic_name = preg_replace('/\s+/', '', $pic->getClientOriginalName());
                $picName = time() . '-' . $pic_name;
                $pic->move('./assets/user/driver/', $picName);
                $compic = 'assets/user/driver/' . $picName;
                $driver->du_profile_pic = $compic;
            }
            /*if(BaseNumber::where(["country_code" => $request->country_code,
            "mobile_number" => $request->du_mobile_number])->exists()){
                $basenumber =   BaseNumber::where(["country_code" => $request->country_code,
                    "mobile_number" => $request->du_mobile_number])->first();
            }else {
                $basenumber = new BaseNumber();
            }
            $basenumber->country_code = $request->country_code;
            $basenumber->mobile_number = $request->du_mobile_number;
            $basenumber->full_mobile_number = $request->du_full_mobile_number;
            $basenumber->otp_api_response = '03 Message (M) is mandatory';
            $basenumber->otp_api_response_status = '1';
            $basenumber->verification_code = $request->input('du_otp_manual');;
            $basenumber->otp_verified = 1;
            $basenumber->save();*/

            $driver->du_full_name = $request->input('du_full_name');
            $driver->du_mobile_number = $request->input('du_mobile_number');
            $driver->du_full_mobile_number = $request->input('du_full_mobile_number');
            $driver->du_user_name = $request->input('du_user_name');
            $driver->du_country_code = $request->input('country_code');
            $driver->email = $request->input('email');
            $driver->password = Hash::make($request->input('password'));
            $driver->du_com_id = $request->input('company_id');
            $driver->du_otp_manual = $request->input('du_otp_manual');
            $driver->du_otp_flag = $request->input('du_otp_flag');
            $driver->save();

            $driver_profile = new DriverProfile();
            $driver_profile->dp_license_number = $request->input('dp_license_number');
            $driver_profile->dp_transport_type_id_ref = $request->input('dp_transport_type_id_ref');
            $driver_profile->car_registration = $request->input('car_registration');
            $driver_profile->dp_date_manufacture = $request->input('dp_date_manufacture');
            $driver_profile->dp_date_registration = $request->input('dp_date_registration');
            $driver_profile->dp_user_id = $driver->id;
            $driver_profile->save();

            $driverObj = Driver::find($driver->id);

            $name = $driverObj->du_full_name;
            $id = $driverObj->id;
            $socialLinks = BaseAppSocialLinks::all();
            $header = EmailHeader::where('id',7)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 7, 'locale' => $driverObj->locale])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 7, 'locale' => $driverObj->locale])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 7,'locale' => $driverObj->locale])->first();
            $user_type = 'driver';
            $langtxt = $driverObj->locale;
            Mail::to($driverObj->email)->send(new WelcomeDriverEmail($name,$id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));


            return response()->json(['success' => true, 'message' => trans('adminMessages.driver_inserted')]);
        } else {
            $driver = Driver::find($id);
            if ($request->hasFile('du_profile_pic')) {
                $mime = $request->du_profile_pic->getMimeType();
                $pic = $request->file('du_profile_pic');
                $pic_name = preg_replace('/\s+/', '', $pic->getClientOriginalName());
                $picName = time() . '-' . $pic_name;
                $pic->move('./assets/user/driver/', $picName);
                $compic = 'assets/user/driver/' . $picName;
                $driver->du_profile_pic = $compic;
            }
            if(BaseNumber::where(["country_code" => $driver->country_code,
                "mobile_number" => $driver->du_mobile_number])->exists()){
                $basenumber =   BaseNumber::where(["country_code" => $request->country_code,
                    "mobile_number" => $request->du_mobile_number])->first();
            }else {
                $basenumber = new BaseNumber();
            }
            $basenumber->country_code = $driver->du_country_code;
            $basenumber->mobile_number = $driver->du_mobile_number;
            $basenumber->full_mobile_number = $driver->du_full_mobile_number;
            $basenumber->otp_api_response = '03 Message (M) is mandatory';
            $basenumber->otp_api_response_status = '1';
            $basenumber->verification_code = $driver->du_otp_manual;;
            $basenumber->otp_verified = 1;
            $basenumber->save();

            $driver->du_full_name = $request->input('du_full_name');
            $driver->du_mobile_number = $request->input('du_mobile_number');
            $driver->du_full_mobile_number = $request->input('du_full_mobile_number');
            $driver->du_user_name = $request->input('du_user_name');
            $driver->du_country_code = $request->input('country_code');
            $driver->email = $request->input('email');
            $driver->du_com_id = $request->input('company_id');
            $driver->du_otp_manual = $request->input('du_otp_manual');
            $driver->du_otp_flag = $request->input('du_otp_flag');

            if (!empty($request->password)) {
                $driver->password = Hash::make($request->input('password'));
            }
            $driver->save();

            $driver_profile = new DriverProfile();
            $driver_profile->dp_license_number = $request->input('dp_license_number');
            $driver_profile->dp_transport_type_id_ref = $request->input('dp_transport_type_id_ref');
            $driver_profile->car_registration = $request->input('car_registration');
            $driver_profile->dp_date_manufacture = $request->input('dp_date_manufacture');
            $driver_profile->dp_date_registration = $request->input('dp_date_registration');
            $driver_profile->dp_user_id = $driver->id;
            $driver_profile->save();
            return response()->json(['success' => true, 'message' => trans('adminMessages.driver_updated'), 'company_id' => $request->company_id]);
        }
    }


    /**
     * Display the specified Driver.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified Driver.
     *
     * @param $company_id
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($company_id, $id)
    {
        $driver = Driver::find($id);
        if ($driver) {
            $transports = TransportType::listsTranslations('name')->get();
            $driver_profile = DriverProfile::where('dp_user_id', $driver->id)->first();
            return view('admin.company.driver.edit', ['driver' => $driver, 'company_id' => $company_id, 'driver_profile' => $driver_profile, 'transports' => $transports]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified Driver in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified Driver from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

    /**
     * Change the status for Driver
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function status($id, $status)
    {
        $driver = Driver::where('id', $id)->update(['du_driver_status' => $status]);
        $driver = Driver::where('id', $id)->first();

        if ($driver->du_driver_status == 'driver_status_when_approved'){

            $driver_name = $driver->du_full_name;
            $socialLinks = BaseAppSocialLinks::all();
            $header = EmailHeader::where('id',8)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 8, 'locale' => $driver->locale])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 8, 'locale' => $driver->locale])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 8,'locale' => $driver->locale])->first();
            $langtxt = $driver->locale;
            $user_type = "driver";

            Mail::to($driver->email)->send(new DriverStatusEmail($driver_name,$driver->id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));
        }
        return response()->json(['success' => true, 'message' => trans('adminMessages.driver_status_updated')]);
    }

    /**
     * Manual OTP update
     * @param Request $request
     * @return JsonResponse
     */
    public function updateManualOTP(Request $request)
    {
        $messages = [
            'required' => 'the_field_should_be_unique',
        ];
        $validator = Validator::make($request->all(), [
            'du_otp_manual' => 'required | unique:drivers',
        ], $messages);
        if ($validator->fails()) {
            $message = "du_otp_manual is not valid";
            return response()->json(['message' => $message, 'success' => false], 401);
        }
        $manualOTP = $request->du_otp_manual;
        $manualOTP = Crypt::encryptString($manualOTP);

        Driver::where('id', $request->driverId)->update(['du_otp_manual' => $request->du_otp_manual, 'password' => $manualOTP]);
        return response()->json(['success' => true, 'message' => trans('adminMessages.driver_Manual_OTP_added')]);
    }
}
