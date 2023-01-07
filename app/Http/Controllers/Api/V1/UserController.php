<?php

namespace App\Http\Controllers\Api\V1;
use App\BaseAppSocialLinks;
use App\Driver;
use App\DriverProfile;
use App\DriverRating;
use App\EmailBodyTranslation;
use App\EmailFooterTranslation;
use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\FireBase\FireBase;
use App\Http\Resources\AppReferenceResource;
use App\Http\Resources\DriverProfileResource;
use App\Http\Resources\GetCancelReasoningResourcePassenger;
use App\Http\Resources\GetContactListResource;
use App\Http\Resources\GetPassengerRecentGroupAddressResource;
use App\Http\Resources\PassengerAdressesResource;
use App\Http\Resources\PassengerGroupAdressesResource;
use App\Language;
use App\Mail\WelcomeEmail;
use App\PassengerAddress;
use App\PassengerContactList;
use App\RideBookingSchedule;
use App\User;
use App\BaseNumber;
use App\Device;
use App\Utility\Utility;
use Carbon\Carbon;
use DB;
use App\LanguageString;
use App\AppReference;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\Flysystem\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    /**
     *  Create user register Mobile Number
     * Create Otp
     *send SMS to user on mobile
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function registerMobileNumber(Request $request)
    {
        try {
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
            ];
            $validator = Validator::make($request->all(), [
                'country_code' => 'required|max:10',
                'mobile_no' => 'required',
            ], $messages);
            if ($validator->fails()) {

                $errors = [];
                foreach ($validator->errors()->messages() as $field => $message) {
                    $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                    $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                    $errors[] = [
                        'field' => $field,
                        'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                    ];
                }
                return response()->json(compact('errors'), 401);
            }


       if(BaseNumber::where(['country_code'=>$request->country_code,'mobile_number'=>$request->mobile_no])->exists()
        && User::where(['country_code'=>$request->country_code,'mobile_no'=>$request->mobile_no])->exists()){
           $otp = rand(100000, 999999);
           $basenumber = BaseNumber::where(['country_code'=>$request->country_code,'mobile_number'=>$request->mobile_no])
           ->update(['verification_code'=>$otp,"otp_verified" => '0']);
           $basenumber = BaseNumber::where(['country_code'=>$request->country_code,'mobile_number'=>$request->mobile_no])
               ->first();
           $message_sms = "<#> Whipp PIN: ".$otp.". Never share this PIN with anyone. Whipp will never call you to ask for this. kNkQivZhomT";
           $user_number = "96597631404";
           $sendSMS = Utility::sendSMS($message_sms,$user_number);

           return response()->json([
               'id' => (int)$basenumber->id,
               'otp' => $basenumber->verification_code,
               'country_code' => $basenumber->country_code,
               'mobile_no' => $basenumber->mobile_number
           ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);

       }else{

         $otp = rand(100000, 999999);
        $basenumber = new BaseNumber();
        $basenumber->country_code = $request->input('country_code');
        $basenumber->mobile_number = $request->input('mobile_no');
        $basenumber->full_mobile_number = $request->input('country_code').''.$request->input('mobile_no');
        $basenumber->otp_api_response = '03 Message (M) is mandatory';
        $basenumber->otp_api_response_status = '1';
        $basenumber->verification_code = $otp;
        $basenumber->otp_verified = '0';
        $basenumber->save();
           $message_sms = "<#> Whipp PIN: ".$otp.". Never share this PIN with anyone. Whipp will never call you to ask for this. kNkQivZhomT";
           $user_number = "96597631404";
           $sendSMS = Utility::sendSMS($message_sms,$user_number);
         return response()->json([
            'id' => (int)$basenumber->id,
             'otp' => $basenumber->verification_code,
             'country_code' => $basenumber->country_code,
             'mobile_no' => $basenumber->mobile_number
        ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
     }
    }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'mobile_number_not_created','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
            }
    }


    /**
     *  get verify code by user
     * @param Request $request
     * @return Response
     * @throws Exception
     */

  public function getVerifyCode(Request $request)
    {
        try {
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
            ];
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ], $messages);
            if ($validator->fails()) {

                $errors = [];
                foreach ($validator->errors()->messages() as $field => $message) {
                    $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                    $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                    $errors[] = [
                        'field' => $field,
                        'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                    ];
                }
                return response()->json(compact('errors'), 401);
            }

        $row_code = BaseNumber::where(['id'=>$request->id])->first();
       if($row_code->verification_code){
        return response()->json([
            'id' => (int)$request->id,
            'verification_code' => $row_code->verification_code,
            'country_code' => $row_code->country_code,
            'mobile_no' => $row_code->mobile_number
        ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
       }else{
         $message = LanguageString::translated()->where('bls_name_key','otp_not_match')->first()->name;
            $error = ['field'=>'otp_not_match','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
     }
    }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'otp_not_match','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
            }
    }

    /**
     *  user verify code or opt
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function verifyCode(Request $request)
    {
        try {
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
            ];
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'verification_code' => 'required',
                'device_u_id' => 'required',
                'device_type' => 'required',
                'device_token' => 'required',
            ], $messages);
            if ($validator->fails()) {

                $errors = [];
                foreach ($validator->errors()->messages() as $field => $message) {
                    $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                    $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                    $errors[] = [
                        'field' => $field,
                        'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                    ];
                }
                return response()->json(compact('errors'), 401);
            }

        $row_code = BaseNumber::where(['id'=>$request->id])->first();
       if($row_code->verification_code == $request->verification_code){
        $update = BaseNumber::where('id', $request->id)->update(['otp_verified' => 1]);
        if(User::where(['country_code'=>$row_code->country_code,'mobile_no'=>$row_code->mobile_number])->exists()){

            $user_update = User::where(['country_code'=>$row_code->country_code,'mobile_no'=>$row_code->mobile_number])
                ->update(['mobile_number_verified'=>1]);
            $user = User::where(['country_code'=>$row_code->country_code,'mobile_no'=>$row_code->mobile_number])->first();

            if(isset($user->user_JWT_Auth_Token) && $user->user_JWT_Auth_Token != null) {
                $newToken = \JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($user->user_JWT_Auth_Token), $forceForever = false);
            }

            $token=JWTAuth::fromUser($user);
            $update_token = User::where('id',$user->id)->update(['user_JWT_Auth_Token'=>$token]);
            if($request->device_type == 1){ $type = 'Android';}
            if($request->device_type == 2){ $type = 'iOS';}
            if(Device::where(['device_type'=>$type,'device_token'=>$request->device_token,'user_id'=>$user->id,'app_type'=>'Passenger','device_u_id'=>$request->device_u_id])->exists()){
                $deleteDevice = Device::where(['user_id'=>$user->id,'app_type'=>'Passenger'])->delete();
                $deleteDevice = Device::where(['device_u_id'=>$request->device_u_id,'app_type'=>'Passenger'])->delete();
                $data = ['device_type'=>$request->device_type,'device_token'=>$request->device_token,'user_id'=>$user->id,'app_type'=>'Passenger','device_u_id'=>$request->device_u_id];
                $device = Device::updateOrCreate(['user_id'=>$user->id],$data);
            }else{
                $deleteDevice = Device::where(['user_id'=>$user->id,'app_type'=>'Passenger'])->delete();
                $deleteDevice = Device::where(['device_u_id'=>$request->device_u_id,'app_type'=>'Passenger'])->delete();
                $data = ['device_type'=>$request->device_type,'device_token'=>$request->device_token,'user_id'=>$user->id,'app_type'=>'Passenger','device_u_id'=>$request->device_u_id];
                $device = Device::updateOrCreate(['user_id'=>$user->id],$data);
            }
            $user_data = User::getuser($user->id);
            $NodeUser = FireBase::storeuser($user->id,$user_data);
            return response()->json([
                'new_user'=>false,
                'user' => $user_data,
                'token' => $token
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }else {
            return response()->json([
                'new_user'=>true,
                'id' => (int)$request->id,
                'verification_code' => $request->verification_code,
                'country_code' => $row_code->country_code,
                'mobile_no' => $row_code->mobile_number

            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
       }else{

         $message = LanguageString::translated()->where('bls_name_key','otp_not_match')->first()->name;
            $error = ['field'=>'otp_not_match','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 401);
     }
    }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'otp_not_match','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
            }
    }

   /**
     * Create user your Identity
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function yourIdentity(Request $request)
    {
        try {

            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
            ];
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'verification_code' => 'required',
                'full_name' => 'required',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required',
                'device_u_id' => 'required',
                'device_type' => 'required',
                'device_token' => 'required',
            ], $messages);
            if ($validator->fails()) {

                $errors = [];
                foreach ($validator->errors()->messages() as $field => $message) {
                    $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                    $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                    $errors[] = [
                        'field' => $field,
                        'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                    ];
                }
                return response()->json(compact('errors'), 401);
            }

        $basenumber = BaseNumber::where(['id'=>$request->id,'verification_code'=>$request->verification_code,'otp_verified'=>1])->first();
       if($basenumber){
        if(User::where(['country_code'=>$basenumber->country_code,'mobile_no'=>$basenumber->mobile_number])->exists()) {

            $message =  LanguageString::translated()->where('bls_name_key','user_already_created')->first()->name;
            $error = ['field'=>'user_email_exist','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 401);

        }else {
            $locale = $request->header('Accept-Language');
            $user = new User();
            $user->name = $request->full_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->country_code = $basenumber->country_code;
            $user->mobile_no = $basenumber->mobile_number;
            $user->locale = $locale;
            $user->panel_mode = '0';
            $user->user_type = 'user';
            $user->profile_pic = 'assets/default/user.png';
            $user->parent_id = '0';
            $user->status = 'Active';
            $user->save();

            $name = $user->name;
            $id = $user->id;

            $socialLinks = BaseAppSocialLinks::all();

            $header = EmailHeader::where('id',1)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 1, 'locale' => $user->locale])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 1, 'locale' => $user->locale])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 1,'locale' => $user->locale])->first();
            $langtxt = $user->locale;
            $user_type = "user";
            Mail::to($request->email)->send(new WelcomeEmail($name,$id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));

            if(isset($user->user_JWT_Auth_Token) && $user->user_JWT_Auth_Token != null) {
                $newToken = \JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($user->user_JWT_Auth_Token), $forceForever = false);
            }

            $token=JWTAuth::fromUser($user);
            $update_token = User::where('id',$user->id)->update(['user_JWT_Auth_Token'=>$token]);


            if($request->device_type == 1){ $type = 'Android';}
            if($request->device_type == 2){ $type = 'iOS';}
            if(Device::where(['device_type'=>$type,'device_token'=>$request->device_token,'user_id'=>$user->id,'app_type'=>'Passenger','device_u_id'=>$request->device_u_id])->exists()){
//                $deleteDevice = Device::where(['user_id'=>$user->id,'app_type'=>'Passenger'])->delete();
                $deleteDevice = Device::where(['device_u_id'=>$request->device_u_id,'app_type'=>'Passenger'])->delete();
                $data = ['device_type'=>$request->device_type,'device_token'=>$request->device_token,'user_id'=>$user->id,'app_type'=>'Passenger','device_u_id'=>$request->device_u_id];
                $device = Device::updateOrCreate(['user_id'=>$user->id,'app_type'=>'Passenger'],$data);
            }else{
//                $deleteDevice = Device::where(['user_id'=>$user->id,'app_type'=>'Passenger'])->delete();
                $deleteDevice = Device::where(['device_u_id'=>$request->device_u_id,'app_type'=>'Passenger'])->delete();
                $data = ['device_type'=>$request->device_type,'device_token'=>$request->device_token,'user_id'=>$user->id,'app_type'=>'Passenger','device_u_id'=>$request->device_u_id];
                $device = Device::updateOrCreate(['user_id'=>$user->id,'app_type'=>'Passenger'],$data);
            }
            $user_data = User::getuser($user->id);
            $NodeUser = FireBase::storeuser($user->id,$user_data);

            return response()->json([

                'token' => $token,
                'user' => $user_data,
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);

        }
        }else{
            $message = LanguageString::translated()->where('bls_name_key','otp_not_match')->first()->name;
            $error = ['field'=>'otp_not_match','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','user_not_created')->first()->name;
            $error = ['field'=>'user_not_created','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
            }
    }


   /**
     * User edit Profile
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function editProfile(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
        if(isset($request->profile_pic) && !empty($request->profile_pic) && $request->hasFile('profile_pic')){
            if($user->profile_pic != "assets/default/user.png") {
                @unlink(public_path() . '/' . $user->profile_pic);
            }
            $mime= $request->profile_pic->getMimeType();
            $image = $request->file('profile_pic');
            $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() .'-'.$image_name;
            $image->move('./assets/user/Passenger/profile_pic/', $ImageName);
            $path_image = 'assets/user/Passenger/profile_pic/'.$ImageName;
            $update = User::where('id', $user->id)->update(['profile_pic' => $path_image]);
        }

        if(isset($request->full_name) && !empty($request->full_name)){
            $update = User::where('id', $user->id)->update(['name' => $request->full_name]);
        }

        if(isset($request->mobile_no) && !empty($request->mobile_no) && isset($request->country_code) && !empty($request->country_code)){


            if(User::where(['mobile_no'=>$request->mobile_no,'country_code'=>$request->country_code])->exists()){
                $message = LanguageString::translated()->where('bls_name_key','driver_profile_Mobile_exist')->first()->name;
                $error = ['field'=>'driver_profile_not_edit','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }else {
                $otp = rand(100000, 999999);
                $base_number = BaseNumber::where(['mobile_number' => $user->mobile_no, 'country_code' => $user->country_code])->first();
                if($base_number) {
                    $base_number_1 = BaseNumber::where(['id' => $base_number->id])->update(['mobile_number' => $request->mobile_no, 'country_code' => $request->country_code, 'full_mobile_number' => $request->country_code . $request->mobile_no, 'verification_code' => $otp]);
                    $update = User::where('id', $user->id)->update(['mobile_no' => $request->mobile_no, 'country_code' => $request->country_code, 'mobile_number_verified' => 0]);
                }else{

                    $base_number_1 = BaseNumber::create(['mobile_number' => $request->mobile_no, 'country_code' => $request->country_code, 'full_mobile_number' => $request->country_code . $request->mobile_no, 'verification_code' => $otp]);
                    $update = User::where('id', $user->id)->update(['mobile_no' => $request->mobile_no, 'country_code' => $request->country_code, 'mobile_number_verified' => 0]);
                }
                $message_sms = "<#> Whipp PIN: ".$otp.". Never share this PIN with anyone. Whipp will never call you to ask for this. kNkQivZhomT";
                $user_number = "96597631404";
                $sendSMS = Utility::sendSMS($message_sms,$user_number);
            }
            if($update){
                return response()->json([
                    'otp'=> $otp,
                    'id'=>(int)$base_number->id,
                    'mobile_no'=> $request->mobile_no,
                    'country_code'=>$request->country_code
                ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);

            }else{
                $message = LanguageString::translated()->where('bls_name_key','user_profile_not_edit')->first()->name;
                $error = ['field'=>'user_profile_not_edit','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }
        }
        if(isset($request->email) && !empty($request->email)) {
            if (User::where(['email' => $request->email])->exists()) {
                $message = LanguageString::translated()->where('bls_name_key', 'p_email_already_exist')->first()->name;
                $error = ['field' => 'p_email_already_exist', 'message' => $message];
                $errors = [$error];
                return response()->json(['errors' => $errors], 401);
            } else {
                $messages = [
                    'required' => 'the_field_is_required'
                ];
                $validator = Validator::make($request->all(), [
                    'email' => 'required',
                ], $messages);
                if ($validator->fails()) {

                    $errors = [];
                    foreach ($validator->errors()->messages() as $field => $message) {
                        $messageval = LanguageString::translated()->where('bls_name_key', $message[0])->first()->name;
                        $field_msg = LanguageString::translated()->where('bls_name_key', $field)->first()->name;
                        $errors[] = [
                            'field' => $field,
                            'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                        ];
                    }
                    return response()->json(compact('errors'), 401);
                }
                $update = User::where('id', $user->id)->update(['email' => $request->email]);
                $name = $user->name;
                $id = $user->id;
                $socialLinks = BaseAppSocialLinks::all();

                $header = EmailHeader::where('id', 1)->first();
                $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 1, 'locale' => $user->locale])->first();

                $bodyTrans = EmailBodyTranslation::where(['email_body_id' => 1, 'locale' => $user->locale])->first();

                $footerTrans = EmailFooterTranslation::where(['email_footer_id' => 1, 'locale' => $user->locale])->first();
                $langtxt = $user->locale;
                $user_type = "user";
                Mail::to($request->email)->send(new WelcomeEmail($name, $id, $socialLinks, $header, $headerTrans, $bodyTrans, $footerTrans, $langtxt, $user_type));
            }
        }if(isset($request->password)){
                $check = Auth::guard('web')->attempt([
                    'email' => $user->email,
                    'password' => $request->old_password
                ]);
                if($check) {

                    $update = User::where('id', $user->id)->update(['password' => bcrypt($request->password)]);
                }else{
                    $message = LanguageString::translated()->where('bls_name_key','password_does_not_match')->first()->name;
                    $error = ['field'=>'driver_profile_not_edit','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 401);
                }

        }

        return response()->json([
            'user' => User::getuser($user->id)
        ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Display a listing of Passenger Menu
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getPassengerMenu(Request $request){
        try{

            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $data_items =   AppReference::where(['bar_status'=>1,'bar_mod_id_ref'=>2,'bar_ref_type_id'=>2])->orderBy('base_app_references.bar_order_by','asc')->get();
            $code = ["book_ride","whipp_wallet","payment","my_rides","my_places","setting","invite_friend","become_captain","logout"];
            foreach ($data_items as $key=>$data_item){
                $data_item['slug'] = $code[$key];
            }
        $passengermenu = AppReferenceResource::collection($data_items);

        return $passengermenu;
            }catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'error','message'=>$message];
        $errors =[$error];
        return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get my (user) Profile
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function myProfile(Request $request){
        try{

            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $driver = \Auth::guard('api')->user();

            return response()->json([
                'user' => User::getuser($driver->id),
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * driver give rate to passenger
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function driverRating(Request $request){
        try{
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $rating = $request->rating;
            $driver_id = $request->driver_id;
            $ride_id = $request->ride_id;
            $review_id = $request->review_id;
            $comments1 = $request->comments;
            if(isset($comments1) && $comments1 != null){$comments = $comments1;}else{$comments = null;}
            if($user && $user->TransactionId->last() != null) {
                Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' =>$user->TransactionId->last()]);
            }else{
                Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' =>null]);
            }
        Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
        $messages = [
            'required' => 'the_field_is_required',
            'string' => 'the_string_field_is_required',
            'max' => 'the_field_is_out_from_max',
            'min' => 'the_field_is_low_from_min',
            'unique' => 'the_field_should_unique',
            'confirmed' => 'the_field_should_confirmed',
            'email' => 'the_field_should_email',
            'exists' => 'the_field_should_exists',
        ];
        $validator = Validator::make($request->all(), [
            'rating' => 'required',
            'driver_id' => 'required',
            'review_id' => 'required',
            'ride_id' => 'required',
        ], $messages);
        if ($validator->fails()) {

            $errors = [];
            foreach ($validator->errors()->messages() as $field => $message) {
                $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                $errors[] = [
                    'field' => $field,
                    'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                ];
            }
            return response()->json(compact('errors'), 401);
        }

            $data_rating = [
                'dr_driver_id'=>$driver_id,
                'dr_passenger_id'=>$user->id,
                'dr_rating'=>$rating,
                'dr_review_id'=>$review_id,
                'dr_ride_id'=>$ride_id,
                'dr_comments'=>$comments,
                'dr_created_at'=>now(),
                'dr_updated_at'=>now(),
            ];
        $driver_rating = DriverRating::create($data_rating);
        $message = LanguageString::translated()->where('bls_name_key','thanks_for_rating')->first()->name;
            return response()->json([
                'message'=>$message
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get Passenger Reviews list
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getPassengerReviews(Request $request){

        try{
            $driver_id = $request->driver_id;
            $driver = Driver::getdriver($driver_id);
            $Reasoning = GetCancelReasoningResourcePassenger::collection(AppReference::translated()->where(['bar_status'=>1,'bar_mod_id_ref'=>7,'bar_ref_type_id'=>11])->get());

            return response()->json(['review'=>$Reasoning,'driver'=>$driver], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

     /**
     * User Logout
     * @param Request $request
     * @return Response
     * @throws Exception
     */




    public function logout(Request $request)
    {
        Log::info('app.requests', ['request' => $request->all()]);
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            $success = true;
            $code = '200';
            $message = LanguageString::translated()->where('bls_name_key','you_are_logout')->first()->name;
            return response()->json(['message'=>$message ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

   /**
     * User delete Profile Picture
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function deleteProfilePic(Request $request){
        Log::info('app.requests', ['request' => $request->all()]);

        try{

            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);

            $image_path_de = public_path($user->profile_pic);

            if (file_exists($image_path_de ) && $user->profile_pic != 'assets/default/user.png') {
                \File::delete($image_path_de);
                $profile_picture_del = User::where('id',$user->id)->update(['profile_pic'=>'assets/default/user.png']);
                $message = LanguageString::translated()->where('bls_name_key','Passenger_profile_pic_is_deleted')->first()->name;
            }else{
                $message = LanguageString::translated()->where('bls_name_key','Passenger_profile_pic_is_empty')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }



            return response()->json(['message'=>$message,'user' => User::getuser($user->id)],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Create passenger Address
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function passengerAddress(Request  $request){
        try{

            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $address_type_id = $request->get('address_type_id');
            $address_text = $request->get('address_text');
            $lat = $request->get('lat');
            $long = $request->get('long');
            $group_name = $request->get('group_name');
            $pa_group_slug = str_replace(' ', '',$request->get('group_name'));

            $data = [
                'pa_user_id'=>$user->id,
                'pa_address_type'=>$address_type_id,
                'pa_address_text'=>$address_text,
                'pa_lat'=>$lat,
                'pa_long'=>$long,
                'pa_group_name'=>$group_name,
                'pa_group_slug'=>$pa_group_slug

            ];

            $passengerAddress = PassengerAddress::create($data);
            $passengerAddresses = [];
            $passengerAddresses = PassengerAdressesResource::collection(AppReference::listsTranslations('name')->select('base_app_references.id','base_app_references.bar_mod_id_ref','base_app_references.bar_ref_type_id','base_app_references.bar_icon','base_app_references.bar_image','base_app_references.bar_icon_unselected','base_app_references.bar_image_unselected','base_app_references.bar_status')->where(['bar_status'=>1,'bar_mod_id_ref'=>4,'bar_ref_type_id'=>5])->get());

            return response()->json(['address_data'=>$passengerAddresses],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     * Update passenger Address
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function passengerAddressUpdate(Request  $request){
        try{

            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $address_type_id = $request->get('address_type_id');
            $address_text = $request->get('address_text');
            $lat = $request->get('lat');
            $long = $request->get('long');
            $id = $request->get('id');

            $data = [
                'pa_user_id'=>$user->id,
                'pa_address_type'=>$address_type_id,
                'pa_address_text'=>$address_text,
                'pa_lat'=>$lat,
                'pa_long'=>$long,
            ];

            if(PassengerAddress::where(['id'=>$id,'pa_user_id'=>$user->id])->exists()){

                $address = PassengerAddress::where(['id'=>$id,'pa_user_id'=>$user->id])->first();

                    $address->pa_user_id= $user->id;
                if(isset($address_text) && $address_text != null){
                    $address->pa_address_text= $address_text;
                }if(isset($address_type_id) && $address_type_id != null){
                    $address->pa_address_type= $address_type_id;
                }if(isset($lat) && $lat != null){
                    $address->pa_lat= $lat;
                }if(isset($long) && $long != null){
                    $address->pa_long= $long;
                }
                $address->save();
            }

            $passengerAddresses = [];
            $passengerAddresses = PassengerAdressesResource::collection(AppReference::listsTranslations('name')->select('base_app_references.id','base_app_references.bar_mod_id_ref','base_app_references.bar_ref_type_id','base_app_references.bar_icon','base_app_references.bar_image','base_app_references.bar_icon_unselected','base_app_references.bar_image_unselected','base_app_references.bar_status')->where(['bar_status'=>1,'bar_mod_id_ref'=>4,'bar_ref_type_id'=>5])->get());

            return response()->json(['address_data'=>$passengerAddresses],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     * Get passenger Address
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getPassengerAddress(Request  $request){
        try{

            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $passengerAddresses = [];
            $passengerAddresses = PassengerAdressesResource::collection(AppReference::listsTranslations('name')->select('base_app_references.id','base_app_references.bar_mod_id_ref','base_app_references.bar_ref_type_id','base_app_references.bar_icon','base_app_references.bar_image','base_app_references.bar_icon_unselected','base_app_references.bar_image_unselected','base_app_references.bar_status')->where(['bar_status'=>1,'bar_mod_id_ref'=>4,'bar_ref_type_id'=>5])->get());

            return response()->json(['address_data'=>$passengerAddresses],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     * get Passenger Address Recent
     * @param Request $request
     * @return Response
     * @throws Exception
     */


    public function getPassengerAddressRecent(Request  $request){
        try{

            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
           $passengerAddressesRecent =  [];

            $passengerAddressesRecents =  RideBookingSchedule::where(['ride_booking_schedules.rbs_passenger_id'=>$user->id])->orderBy('id','desc')->limit(10)->get();

            foreach ($passengerAddressesRecents as $item){
                $sourceLat = floatval($item->rbs_source_lat);
                $sourceLong = floatval($item->rbs_source_long);
                $desLat = floatval($item->rbs_destination_lat);
                $desLong = floatval($item->rbs_destination_long);
                $pickup_location = app('geocoder')->reverse($sourceLat,$sourceLong)->get()->first();
                $drop_off = app('geocoder')->reverse($desLat,$desLong)->get()->first();

                array_push($passengerAddressesRecent,[
                    'id'=>null,
                    'user_id'=>null,
                    'address_type'=>$item->pa_address_type,
                    'lat'=>$item->rbs_destination_lat,
                    'long'=>$item->rbs_destination_long,
                    'group_name'=>LanguageString::translated()->where('bls_name_key','drop_off_location')->first()->name,
                    'address_text'=>(isset($drop_off) && $drop_off != null) ? $drop_off->getFormattedAddress() : "",
                    'group_slug'=>null,
                    'group_type'=>'recent'
                ]);
                array_push($passengerAddressesRecent, [
                    'id'=>null,
                    'user_id'=>null,
                    'address_type'=>null,
                    'lat'=>$item->rbs_source_lat,
                    'long'=>$item->rbs_source_long,
                    'group_name'=>LanguageString::translated()->where('bls_name_key','pick_up_location')->first()->name,
                    'address_text'=>(isset($pickup_location) && $pickup_location != null) ? $pickup_location->getFormattedAddress() : "",
                    'group_slug'=>null,
                    'group_type'=>'recent'
                ]);

            }
            $temp_array = array();
            $i = 0;
            $key_array = array();
            $key = 'address_text';
            foreach($passengerAddressesRecent as $val) {
                if (!in_array($val[$key], $key_array)) {
                    $key_array[$i] = $val[$key];
                    array_push($temp_array,$val);
                }
                $i++;
            }

            return response()->json($temp_array,200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

     /**
     * passenger Addres sDestroy
     * @param Request $request
     * @return Response
     * @throws Exception
     */



    public function passengerAddressDestroy(Request $request, $id){
        try{
            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
           if(PassengerAddress::where(['id'=>$id,'pa_user_id'=>$user->id])->exists()){
            PassengerAddress::where(['id'=>$id,'pa_user_id'=>$user->id])->delete();
            $message = LanguageString::translated()->where('bls_name_key','Passenger_address_is_deleted')->first()->name;
        }else{
            $message = LanguageString::translated()->where('bls_name_key','Passenger_address_is_empty')->first()->name;
               $error = ['field'=>'language_strings','message'=>$message];
               $errors =[$error];
               return response()->json(['errors' => $errors], 401);
        }
            return response()->json(['message'=>$message],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

   /**
     * passenger Verify Email
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function verifyEmailPassenger(Request $request){
        try{

            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $name = $user->name;
            $id = $user->id;
            $socialLinks = BaseAppSocialLinks::all();
            $header = EmailHeader::where('id',1)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 1, 'locale' => $user->locale])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 1, 'locale' => $user->locale])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 1,'locale' => $user->locale])->first();
            $langtxt = $user->locale;
            $user_type = "user";
            Mail::to($user->email)->send(new WelcomeEmail($name,$id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));
            $message = 'Verification Email has been send to you';
            return response()->json(['message'=>$message],200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

   /**
     * update Locale (Language)
     * @param Request $request
     * @return Response
     * @throws Exception
     */


    public function updateLocale(Request $request){
        Log::info('app.requests', ['request' => $request->all()]);
        try{
            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $locale = $request->header('Accept-Language');
            $update_locale = User::where('id',$user->id)->update(['locale'=>$locale]);
            $message = LanguageString::translated()->where('bls_name_key','language_is_updated')->first()->name;
            return response()->json([
                'message' => $message
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Get Contact List
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getContactList(Request $request){
        Log::info('app.requests', ['request' => $request->all()]);
        try{
            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $getContactList = GetContactListResource::collection(PassengerContactList::where('pcl_user_id',$user->id)->get());
            return response()->json($getContactList, 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }


}
