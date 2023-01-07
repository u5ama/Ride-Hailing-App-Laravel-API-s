<?php

namespace App\Http\Controllers\Api\V1;
use App\BaseAppSocialLinks;
use App\BaseMedia;
use App\CustomerInvoice;
use App\Driver;
use App\DriverAccount;
use App\DriverCurrentLocation;
use App\DriverProfile;
use App\DriverRating;
use App\EmailBodyTranslation;
use App\EmailFooterTranslation;
use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\FireBase\FireBase;
use App\Http\Resources\AppReferenceResource;
use App\Http\Resources\BaseMediaResource;
use App\Http\Resources\DriverProfileResource;
use App\Http\Resources\DriverResource;
use App\Http\Resources\GetCancelReasoningResourcePassenger;
use App\Http\Resources\GetDriverAccountResource;
use App\Http\Resources\GetPassengerBookedRidesResource;
use App\Http\Resources\ImageDataResource;
use App\Mail\WelcomeDriverEmail;
use App\Mail\WelcomeEmail;
use App\PassengerRating;
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
use Illuminate\Support\Facades\Crypt;
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
use phpDocumentor\Reflection\Types\Null_;
use Tymon\JWTAuth\Facades\JWTAuth;

class DriverController extends Controller
{
    /**
     * Create driver Registration By Mobile Number
     *send SMS on mobile to create otp
     * @param Request $request,country_code,mobile_number
     * @return Response
     * @throws Exception
     */

    public function driverRegisterMobileNumber(Request $request)
    {
        try {
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $messages = [
            'country_code' => 'required|max:10',
            'mobile_no' => 'required',
        ];
        $validator = Validator::make($request->all(), $messages);

         if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first()
            ], 401);
        }
            if(Driver::where(['du_country_code'=>$request->country_code,'du_mobile_number'=>$request->mobile_no])->where('du_otp_manual','!=',Null)->exists()) {

                $otp = rand(100000, 999999);
                $basenumber = BaseNumber::where(['country_code' => $request->country_code, 'mobile_number' => $request->mobile_no])
                    ->update(['verification_code' => $otp, "otp_verified" => '0']);
                $basenumber = BaseNumber::where(['country_code' => $request->country_code, 'mobile_number' => $request->mobile_no])
                    ->first();

                $message_sms = "<#> Whipp PIN: " . $otp . ". Never share this PIN with anyone. Whipp will never call you to ask for this. kNkQivZhomT";
                $user_number = "96597631404";
//                $sendSMS = Utility::sendSMS($message_sms, $user_number);
                return response()->json([
                    'country_code' => $basenumber->country_code,
                    'mobile_no' => $basenumber->mobile_number,
                    'id' => $basenumber->id,
                    'otp' => $basenumber->verification_code,
                    'message' => LanguageString::translated()->where('bls_name_key','otp_message_manual')->first()->name
                ], 200);
            }


       if(BaseNumber::where(['country_code'=>$request->country_code,'mobile_number'=>$request->mobile_no])->exists()
       && Driver::where(['du_country_code'=>$request->country_code,'du_mobile_number'=>$request->mobile_no])->exists()){
           $otp = rand(100000, 999999);
           $basenumber = BaseNumber::where(['country_code'=>$request->country_code,'mobile_number'=>$request->mobile_no])
           ->update(['verification_code'=>$otp,"otp_verified" => '0']);
           $basenumber = BaseNumber::where(['country_code'=>$request->country_code,'mobile_number'=>$request->mobile_no])
               ->first();

           $message_sms = "<#> Whipp PIN: ".$otp.". Never share this PIN with anyone. Whipp will never call you to ask for this. kNkQivZhomT";
           $user_number = "96597631404";
           $sendSMS = Utility::sendSMS($message_sms,$user_number);
           return response()->json([
               'id' => $basenumber->id,
               'otp' => $basenumber->verification_code,
               'country_code' => $basenumber->country_code,
               'mobile_no' => $basenumber->mobile_number,
               'message' =>""
           ], 200);

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
            'id' => $basenumber->id,
             'otp' => $basenumber->verification_code,
             'country_code' => $basenumber->country_code,
             'mobile_no' => $basenumber->mobile_number,
             'message' =>""
        ], 200);
     }
    }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'mobile_number_not_created','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
            }
    }


    /**
     * Driver get verification code on mobile
     * @param Request $request
     * @return Response
     * @throws Exception
     */

  public function driverGetVerifyCode(Request $request)
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
            'id' => $request->id,
            'verification_code' => $row_code->verification_code,
            'country_code' => $row_code->country_code,
            'mobile_no' => $row_code->mobile_number
        ], 200);

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
     * Driver verify code
     * @param Request $request,verification_code
     * @return Response
     * @throws Exception
     */


    public function driverVerifyCode(Request $request)
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
                'country_code' => 'required',
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


        $row_code = BaseNumber::where(['id'=>$request->id])->first();
       if($row_code && $row_code->verification_code == $request->verification_code){
        $update = BaseNumber::where('id', $request->id)->update(['otp_verified' => 1]);
        if(Driver::where(['du_country_code'=>$row_code->country_code,'du_mobile_number'=>$row_code->mobile_number])->exists()){

            $driver_update = Driver::where(['du_country_code'=>$row_code->country_code,'du_mobile_number'=>$row_code->mobile_number])
                ->update(['du_mobile_number_verified' => 1]);
            $driver = Driver::where(['du_country_code'=>$row_code->country_code,'du_mobile_number'=>$row_code->mobile_number])->first();
            $newToken =\JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBwLmFwaXMucmlkZXdoaXBwLmNvbVwvYXBpXC92MVwvZHJpdmVyVmVyaWZ5Q29kZSIsImlhdCI6MTYyMjcyNTUyNSwiZXhwIjoxNjU0MjYxNTI1LCJuYmYiOjE2MjI3MjU1MjUsImp0aSI6InZYeFVBZkFpZkZCb3FmMmUiLCJzdWIiOjQ0LCJwcnYiOiIzMDViYjc1MmEzZDUwYTUwNjY2ZjI5NzhkMjM4ZTBlYmNmZTU3Zjg3In0.Or_7L91DH5JAJRUqkFLGGw5g96vRQ62Zm5VixOhh3EU'), $forceForever = false);

            if(isset($driver->driver_JWT_Auth_Token) && $driver->driver_JWT_Auth_Token != null) {
                $newToken = \JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($driver->driver_JWT_Auth_Token), $forceForever = false);
            }

            $token=JWTAuth::fromUser($driver);
            $update_token = Driver::where('id',$driver->id)->update(['driver_JWT_Auth_Token'=>$token]);
            if($request->device_type == 1){ $type = 'Android';}
            if($request->device_type == 2){ $type = 'iOS';}
            if(Device::where(['device_type'=>$type,'device_token'=>$request->device_token,'user_id'=>$driver->id,'app_type'=>'Driver','device_u_id'=>$request->device_u_id])->exists()){

                $deleteDevice = Device::where(['user_id'=>$driver->id,'app_type'=>'Driver'])->delete();
                $deleteDevice = Device::where(['device_u_id'=>$request->device_u_id,'app_type'=>'Driver'])->delete();
                $data = ['device_type'=>$request->device_type,'device_token'=>$request->device_token,'user_id'=>$driver->id,'app_type'=>'Driver','device_u_id'=>$request->device_u_id];
                $device = Device::updateOrCreate(['user_id'=>$driver->id,'app_type'=>'Driver'],$data);

            }else{
                $deleteDevice = Device::where(['user_id'=>$driver->id,'app_type'=>'Driver'])->delete();
                $deleteDevice = Device::where(['device_u_id'=>$request->device_u_id,'app_type'=>'Driver'])->delete();
                $data = ['device_type'=>$request->device_type,'device_token'=>$request->device_token,'user_id'=>$driver->id,'app_type'=>'Driver','device_u_id'=>$request->device_u_id];
                $device = Device::updateOrCreate(['user_id'=>$driver->id,'app_type'=>'Driver'],$data);
            }
            $driver_data = Driver::getdriverfull($driver->id);
            $NodeUser = FireBase::storedriver($driver->id,$driver_data);
            return response()->json([
                'new_user'=>false,
                'driver' => $driver_data,
                'token' => $token
            ], 200);
        }else {
            return response()->json([
                'new_user'=>true,
                'id' => (int)$request->id,
                'verification_code' => $request->verification_code,
                'country_code' => $row_code->country_code,
                'mobile_no' => $row_code->mobile_number
            ], 200);
        }
       }else{
           if(Driver::where(['du_otp_manual'=>$request->verification_code,'du_country_code'=>$request->country_code,'du_mobile_number'=>$request->mobile_no])->exists()){
           $driver = Driver::where(['du_otp_manual'=>$request->verification_code,'du_country_code'=>$request->country_code,'du_mobile_number'=>$request->mobile_no])->first();
               if(isset($driver->driver_JWT_Auth_Token) && $driver->driver_JWT_Auth_Token != null) {
                   $newToken = \JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($driver->driver_JWT_Auth_Token), $forceForever = false);
               }
               $token=JWTAuth::fromUser($driver);
               $update_token = Driver::where('id',$driver->id)->update(['driver_JWT_Auth_Token'=>$token]);
               $deleteDevice = Device::where(['user_id' => $driver->id, 'app_type' => 'Driver'])->delete();
               $deleteDevice = Device::where(['device_u_id' => $request->device_u_id, 'app_type' => 'Driver'])->delete();
               $data = ['device_type' => $request->device_type, 'device_token' => $request->device_token, 'user_id' => $driver->id, 'app_type' => 1, 'device_u_id' => $request->device_u_id];
               $device = Device::create($data);
               $driver_data = Driver::getdriverfull($driver->id);
               $NodeUser = FireBase::storedriver($driver->id,$driver_data);
               return response()->json([
                   'new_user' => false,
                   'driver' => $driver_data,
                   'token' => $token
               ], 200);

           }else{

               $message = LanguageString::translated()->where('bls_name_key', 'otp_not_match')->first()->name;
               $error = ['field' => 'otp_not_match', 'message' => $message];
               $errors = [$error];
               return response()->json(['errors' => $errors], 401);
           }
     }
    }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'otp_not_match','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
            }
    }

     /**
     * Create driver Your Identity
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function driverYourIdentity(Request $request)
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
                'email' => 'required|email|unique:drivers,email',
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
            if(Driver::where(['du_country_code'=>$basenumber->country_code,'du_mobile_number'=>$basenumber->mobile_number])->exists()) {

                $message =  LanguageString::translated()->where('bls_name_key','user_already_created')->first()->name;
                $error = ['field'=>'user_email_exist','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);

            }else {
                $locale = $request->header('Accept-Language');
                $driver = new Driver;
                $driver->du_full_name = $request->full_name;
                $driver->email = $request->email;
                $driver->password = bcrypt($request->password);
                $driver->du_country_code = $basenumber->country_code;
                $driver->du_mobile_number = $basenumber->mobile_number;
                $driver->du_full_mobile_number = $basenumber->country_code.$basenumber->mobile_number;
                $driver->locale = $locale;
                $driver->du_is_driver = 1;
                $driver->du_user_type = 2;
                $driver->du_profile_pic = 'assets/default/driver.png';
                $driver->du_com_id = 1;
                $driver->du_driver_status = 'driver_status_when_pending';
                $driver->du_mobile_number_verified = 1;
                $driver->is_signup_mobile = 1;
                $driver->du_created_at = now();
                $driver->du_updated_at = now();
                $driver->save();

                if($request->device_type == 1){ $type = 'Android';}
                if($request->device_type == 2){ $type = 'iOS';}
                if(Device::where(['device_type'=>$type,'device_token'=>$request->device_token,'user_id'=>$driver->id,'app_type'=>'Driver','device_u_id'=>$request->device_u_id])->exists()){

                    $deleteDevice = Device::where(['device_u_id'=>$request->device_u_id,'app_type'=>'Driver'])->delete();
                    $data = ['device_type'=>$request->device_type,'device_token'=>$request->device_token,'user_id'=>$driver->id,'app_type'=>'Driver','device_u_id'=>$request->device_u_id];
                    $device = Device::updateOrCreate(['user_id'=>$driver->id,'app_type'=>'Driver'],$data);

                }else{

                    $deleteDevice = Device::where(['device_u_id'=>$request->device_u_id,'app_type'=>'Driver'])->delete();
                    $data = ['device_type'=>$request->device_type,'device_token'=>$request->device_token,'user_id'=>$driver->id,'app_type'=>'Driver','device_u_id'=>$request->device_u_id];
                    $device = Device::updateOrCreate(['user_id'=>$driver->id,'app_type'=>'Driver'],$data);
                }

//                dd($driver->driver_JWT_Auth_Token);
                if(isset($driver->driver_JWT_Auth_Token) && $driver->driver_JWT_Auth_Token != null) {

                    $newToken = \JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($driver->driver_JWT_Auth_Token), $forceForever = false);

                }
                $token=JWTAuth::fromUser($driver);
                $update_token = Driver::where('id',$driver->id)->update(['driver_JWT_Auth_Token'=>$token]);
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
                $driver_data = Driver::getdriver($driver->id);
                $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                return response()->json([

                    'token' => $token,
                    'driver' => $driver_data,
                ], 200);

            }
        }else{
            $message = LanguageString::translated()->where('bls_name_key','otp_not_match')->first()->name;
            $error = ['field'=>'otp_not_match','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'driver_not_created','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
            }
    }


     /**
     * Driver edit profile data
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function driverEditProfile(Request $request)
    {
        try {
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();

        if(isset($request->profile_pic) && !empty($request->profile_pic) && $request->hasFile('profile_pic')){
            if($driver->profile_pic != "assets/default/driver.png") {
                @unlink(public_path() . '/' . $driver->profile_pic);
            }
            $mime= $request->profile_pic->getMimeType();
            $image = $request->file('profile_pic');
            $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() .'-'.$image_name;
            $image->move('./assets/user/driver/profile_pic/', $ImageName);
            $path_image = 'assets/user/driver/profile_pic/'.$ImageName;
            $update = Driver::where('id', $driver->id)->update(['du_profile_pic' => $path_image]);
            return response()->json([
                'driver' => Driver::getdriver($driver->id),
            ], 200);
        }

        if(isset($request->full_name) && !empty($request->full_name)){
            $update = Driver::where(['id'=>$driver->id])->update(['du_full_name' => $request->full_name]);
        }

        if(isset($request->mobile_no) && !empty($request->mobile_no) && isset($request->country_code) && !empty($request->country_code)){
            $otp = rand(100000,999999);
            if(Driver::where(['du_mobile_number'=>$request->mobile_no,'du_country_code'=>$request->country_code])->exists()){
                $message = LanguageString::translated()->where('bls_name_key','driver_profile_Mobile_exist')->first()->name;
                $error = ['field'=>'driver_profile_not_edit','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }else {
                $base_number = BaseNumber::where(['mobile_number' => $driver->du_mobile_number, 'country_code' => $driver->du_country_code])->first();
                if($base_number) {
                    $base_number_1 = BaseNumber::where(['id' => $base_number->id])->update(['mobile_number' => $request->mobile_no, 'country_code' => $request->country_code, 'full_mobile_number' => $request->country_code . $request->mobile_no, 'verification_code' => $otp]);
                    $update = Driver::where(['id' => $driver->id])->update(['du_mobile_number' => $request->mobile_no, 'du_country_code' => $request->country_code, 'du_mobile_number_verified' => 0]);
                }else{
                    $base_number_1 = BaseNumber::create(['mobile_number' => $request->mobile_no, 'country_code' => $request->country_code, 'full_mobile_number' => $request->country_code . $request->mobile_no, 'verification_code' => $otp]);
                    $update = Driver::where(['id' => $driver->id])->update(['du_mobile_number' => $request->mobile_no, 'du_country_code' => $request->country_code, 'du_mobile_number_verified' => 0]);
                }
                $message_sms = "<#> Whipp PIN: ".$otp.". Never share this PIN with anyone. Whipp will never call you to ask for this. kNkQivZhomT";
                $user_number = "96597631404";
                $sendSMS = Utility::sendSMS($message_sms,$user_number);
            }
            if($update){
                return response()->json([
                    'otp'=> $otp,
                    'id'=>$base_number->id,
                    'mobile_number'=> $request->mobile_no,
                    'country_code'=>$request->country_code,
                    'message' => LanguageString::translated()->where('bls_name_key','we_will_send_code')->first()->name
                ], 200);

            }else{
                $message = LanguageString::translated()->where('bls_name_key','driver_profile_not_edit')->first()->name;
                $error = ['field'=>'driver_profile_not_edit','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }
        }
            if(isset($request->email) && !empty($request->email)) {
                if (Driver::where(['email' => $request->email])->exists()) {
                    $message = LanguageString::translated()->where('bls_name_key', 'email_already_exist')->first()->name;
                    $error = ['field' => 'email_already_exist', 'message' => $message];
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
                    $update = Driver::where(['id' => $driver->id])->update(['email' => $request->email, 'is_email_verified' => 0]);
                    $driverObj = Driver::find($driver->id);
                    $name = $driverObj->du_full_name;
                    $id = $driverObj->id;
                    $socialLinks = BaseAppSocialLinks::all();
                    $header = EmailHeader::where('id', 7)->first();
                    $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 7, 'locale' => $driverObj->locale])->first();

                    $bodyTrans = EmailBodyTranslation::where(['email_body_id' => 7, 'locale' => $driverObj->locale])->first();

                    $footerTrans = EmailFooterTranslation::where(['email_footer_id' => 7, 'locale' => $driverObj->locale])->first();
                    $user_type = 'driver';
                    $langtxt = $driverObj->locale;
                    Mail::to($driverObj->email)->send(new WelcomeDriverEmail($name, $id, $socialLinks, $header, $headerTrans, $bodyTrans, $footerTrans, $langtxt, $user_type));
                }
            }
        if(isset($request->password)){
                $check = Auth::guard('driver')->attempt([
                    'email' => $driver->email,
                    'password' => $request->old_password
                ]);
                if($check) {
                    $update = Driver::where(['id' => $driver->id])->update(['password' => bcrypt($request->password)]);
                }else{
                    $message = LanguageString::translated()->where('bls_name_key','password_does_not_match')->first()->name;
                    $error = ['field'=>'driver_profile_not_edit','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 401);
                }
        }

        return response()->json([
            'driver' => Driver::getdriver($driver->id),
        ], 200);

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

     /**
     * Driver menu List
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getDriverMenu(Request $request){
        try{
            $driver = \Auth::guard('driver')->user();
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            if($driver->du_is_reg_active == 0){
                $data_items = AppReference::where(['bar_status' => 1, 'bar_mod_id_ref' => 2, 'bar_ref_type_id' => 3])->where('base_app_references.id','!=',21)->orderBy('base_app_references.bar_order_by', 'asc')->get();
                // $code = ["2131362208","2131362218","2131362213","2131362215","2131362214","2131362216","2","3"];
                $code = ["home",  "my_wallet", "my_rides", "setting", "invite_friend", "logout"];
                foreach ($data_items as $key => $data_item) {
                    $data_item['slug'] = $code[$key];
                }
            }else{
            $data_items = AppReference::where(['bar_status' => 1, 'bar_mod_id_ref' => 2, 'bar_ref_type_id' => 3])->orderBy('base_app_references.bar_order_by', 'asc')->get();
            // $code = ["2131362208","2131362218","2131362213","2131362215","2131362214","2131362216","2","3"];
            $code = ["home", "driver_registration", "my_wallet", "my_rides", "setting", "invite_friend", "logout"];
            foreach ($data_items as $key => $data_item) {
                $data_item['slug'] = $code[$key];
            }

            }
//            $data_items = AppReference::where(['bar_status'=>1,'bar_mod_id_ref'=>2,'bar_ref_type_id'=>3])->orderBy('base_app_references.bar_order_by','asc')->get();
//           // $code = ["2131362208","2131362218","2131362213","2131362215","2131362214","2131362216","2","3"];
//            $code = ["home","driver_registration","my_wallet","my_rides","setting","invite_friend","logout"];
//            foreach ($data_items as $key=>$data_item){
//                $data_item['slug'] = $code[$key];
//            }
        $drivermenu = AppReferenceResource::collection($data_items);

        return response()->json($drivermenu);
            }catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get my (driver) profile
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function myProfile(Request $request){
        try{
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $driver = \Auth::guard('driver')->user();
            $driver_data = Driver::getdriverfull($driver->id);
//            $NodeUser = FireBase::storedriver($driver->id,$driver_data);
            return response()->json([
                'driver' => $driver_data,
            ], 200);
            }catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['errors' => $errors], 500);
        }
    }

     /**
     * Driver logout
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function logout(Request $request)
    {
        Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
        try {
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
            $currentlocation = DriverCurrentLocation::where('dcl_user_id',$driver->id)->update(['dcl_app_active'=>0]);
            JWTAuth::invalidate($token);
            $success = true;
            $code = '200';
            $message = LanguageString::translated()->where('bls_name_key','you_are_logout')->first()->name;
            return response()->json(['message'=>$message ], 200);
        } catch (\Exception $e) {
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     * Driver delete Profile Picture
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function deleteProfilePic(Request $request){
        try{
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();

            $image_path_de = public_path($driver->du_profile_pic);

            if (file_exists($image_path_de ) && $driver->du_profile_pic != 'assets/default/driver.png') {
                \File::delete($image_path_de);
                $profile_picture_del = Driver::where('id',$driver->id)->update(['du_profile_pic'=>'assets/default/driver.png']);

                $message = LanguageString::translated()->where('bls_name_key','profile_pic_is_deleted')->first()->name;
            }else{
            $message = LanguageString::translated()->where('bls_name_key','profile_pic_is_empty')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
          }
            return response()->json(['message'=>$message,'driver' => Driver::getdriver($driver->id)],200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     * Create Driver Registration Images
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function driverRegistrationImages(Request $request){


        try{
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
           $screen = $request->get('screen');
           $order = $request->get('order');

            if ($request->hasFile('imageFile')){
                $mime= $request->imageFile->getMimeType();
                $image = $request->file('imageFile');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));
            }
            $data = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>$order,
                'bm_user_id'=>$driver->id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=>$driver->id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>$screen,
                'bm_file_size'=>$fileSize
            ];
            $media = BaseMedia::create($data);

            if(DriverProfile::where('dp_user_id',$driver->id)->exists()){
                $driver_profile = DriverProfile::where('dp_user_id',$driver->id)->first();
            }else{
                $driver_profile = new DriverProfile ;
            }
            $driver_profile->dp_created_by = $driver->du_full_name;
            $driver_profile->dp_user_id = $driver->id;
            $driver_profile->dp_created_at = now();
            $driver_profile->save();

            $imageData = ImageDataResource::collection(BaseMedia::where(['bm_user_id'=>$driver->id,'bm_user_type'=>'driver'])->get()->unique('bm_activity_category'));
            return response()->json($imageData,200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Delete Driver Registration images from mobile
     * @param Request $request
     * @return Response
     * @throws Exception
     */


    public function deleteDriverRegistrationImages(Request $request,$id){
        try{
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();

            if(BaseMedia::where(['id'=>$id,'bm_user_id'=>$driver->id])->exists()) {
                $image_file = BaseMedia::where(['id'=>$id,'bm_user_id'=>$driver->id])->first();
                $image_path_de = public_path($image_file->bm_file_path);
                    \File::delete($image_path_de);
                    $delete_image = BaseMedia::where(['id'=>$id,'bm_user_id'=>$driver->id])->delete();
                    $message = LanguageString::translated()->where('bls_name_key', 'your_image_is_deleted')->first()->name;
                } else {
                    $message = LanguageString::translated()->where('bls_name_key', 'you_are_not_allowed')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
                }


            return response()->json(['message'=>$message],200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

     /**
     * Create Driver Registration
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function DriverRegistration(Request  $request){
        try{
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
            $license_number = $request->get('license_number');
            $personal_id = $request->get('personal_id');
            $fuel_type_Id = $request->get('fuel_type_Id');
            $date_manufacture = $request->get('date_manufacture');
            $date_manufacture = Carbon::parse($date_manufacture);
            $date_registration = $request->get('date_registration');
            $date_registration = Carbon::parse($date_registration);
            $transport_type_id = $request->get('transport_type_id');
            $car_registration = $request->get('car_registration');
            $transport_make_id = $request->get('transport_make_id');
            $transport_color_id = $request->get('transport_color_id');
            $transport_year_id = $request->get('transport_year_id');
            $transport_model_id = $request->get('transport_model_id');



        if(DriverProfile::where('dp_user_id',$driver->id)->exists()){
            $driver_profile = DriverProfile::where('dp_user_id',$driver->id)->first();
        }else{
            $driver_profile = new DriverProfile ;
        }
        if(isset($license_number) && $license_number != null){
            $driver_profile->dp_license_number = $license_number;
        }if(isset($personal_id) && $personal_id != null){
            $driver_profile->dp_personal_id = $personal_id;
        }if(isset($transport_type_id) && $transport_type_id != null){
            $driver_profile->dp_transport_type_id_ref = $transport_type_id;
        }if(isset($fuel_type_Id) && $fuel_type_Id != null){
            $driver_profile->dp_fuel_id_ref = $fuel_type_Id;
        }if(isset($car_registration) && $car_registration != null){
            $driver_profile->car_registration = $car_registration;
        }if(isset($date_manufacture) && $date_manufacture != null){
            $driver_profile->dp_date_manufacture = $date_manufacture;
        }if(isset($date_registration) && $date_registration != null){
            $driver_profile->dp_date_registration = $date_registration;
        }if(isset($transport_make_id) && $transport_make_id != null){
            $driver_profile->dp_transport_make_id = $transport_make_id;
        }if(isset($transport_model_id) && $transport_model_id != null){
            $driver_profile->dp_transport_model_id = $transport_model_id;
        }if(isset($transport_color_id) && $transport_color_id != null){
            $driver_profile->dp_transport_color_id = $transport_color_id;
        }if(isset($transport_year_id) && $transport_year_id != null){
            $driver_profile->dp_transport_year_id = $transport_year_id;
        }
            $driver_profile->dp_created_by = $driver->du_full_name;
            $driver_profile->dp_user_id = $driver->id;
            $driver_profile->dp_created_at = now();
            $driver_profile->save();


        $driverProfile = DriverProfileResource::collection(DriverProfile::where('dp_user_id',$driver->id)->get());
            return response()->json($driverProfile[0],200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     * get Driver Profile
     * @return Response
     * @throws Exception
     */

    public function getDriverProfile(){
        try{
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
             $driverProfile = DriverProfileResource::collection(DriverProfile::where('dp_user_id',$driver->id)->get());
             if(count($driverProfile)>0) {
                 return response()->json($driverProfile[0], 200);
             }else{

                 return response()->json((object) null, 200);
             }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     * get Driver Wallet,cash,credit Card list
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getDriverWallet(){
        try{
            $token = JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
             if(!empty($driver)) {
                 $crrDate = date('Y-m-d');
                 $driverCrrDate = DriverAccount::leftJoin('ride_booking_schedules', 'driver_accounts.dc_ride_id', '=', 'ride_booking_schedules.id')->where(['driver_accounts.dc_target_id'=> $driver->id, 'driver_accounts.dc_target_type' => 'driver','driver_accounts.dc_operation_type' => 'ride'])
                     ->whereDate('driver_accounts.created_at', $crrDate)->get();

                 if (isset($driverCrrDate)){
                     foreach ($driverCrrDate as $date){

                         $res = app('geocoder')->reverse($date->rbs_source_lat,$date->rbs_source_long)->get()->first();
                         $des = app('geocoder')->reverse($date->rbs_destination_lat,$date->rbs_destination_long)->get()->first();


                         if ($date->rbs_payment_method == 'wallet'){
                             $pay_image = 'assets/creditCard/Wallet.png';
                         }elseif ($date->rbs_payment_method == 'cash'){
                             $pay_image = 'assets/creditCard/Cash.png';
                         }elseif ($date->rbs_payment_method == 'creditCard'){
                             $pay_image = 'assets/creditCard/Visa.png';
                         }

                         $invoice = CustomerInvoice::where(['ci_ride_id' => $date->dc_ride_id])->first();

                         if (isset($invoice) && $invoice != null){
                             $rideCost = number_format((float)$invoice->ci_customer_invoice_amount, 2, '.', '');
                         }else{
                             $rideCost = number_format((float)$date->rbs_estimated_cost, 2, '.', '');
                         }

                         $date->ride_location = [
                             'from'=> $res->getFormattedAddress(),
                             'to'=> $des->getFormattedAddress(),
                         ];

                         $date->driver_income = $rideCost;
                         $date->driver_source = $date->dc_source_type;
                         $date->ride_status =$date->rbs_ride_status;
                         $date->payment_image = $pay_image;
                         $date->date = date('d-m-Y H:i', strtotime($date->rbs_created_at));

                     }
                 }else{
                     $driverCrrDate = [];
                 }

                 $driverLastDate = DriverAccount::leftJoin('ride_booking_schedules', 'driver_accounts.dc_ride_id', '=', 'ride_booking_schedules.id')->where(['driver_accounts.dc_target_id'=> $driver->id, 'driver_accounts.dc_target_type' => 'driver','driver_accounts.dc_operation_type' => 'ride'])
                     ->whereDate('driver_accounts.created_at','>=', Carbon::now()->subDays(7))
                     ->whereDate('driver_accounts.created_at','<', Carbon::now())->get();


                 if (isset($driverLastDate)){
                     foreach ($driverLastDate as $date){

                         $res = app('geocoder')->reverse($date->rbs_source_lat,$date->rbs_source_long)->get()->first();
                         $des = app('geocoder')->reverse($date->rbs_destination_lat,$date->rbs_destination_long)->get()->first();

                         if ($date->rbs_payment_method == 'wallet'){
                             $pay_image = 'assets/creditCard/Wallet.png';
                         }elseif ($date->rbs_payment_method == 'cash'){
                             $pay_image = 'assets/creditCard/Cash.png';
                         }elseif ($date->rbs_payment_method == 'creditcard'){
                             $pay_image = 'assets/creditCard/Visa.png';
                         }

                         $date->ride_location = [
                             'from'=> $res->getFormattedAddress(),
                             'to'=> $des->getFormattedAddress(),
                         ];

                         $date->driver_income = $date->dc_amount;
                         $date->driver_source = $date->dc_source_type;
                         $date->ride_status =$date->rbs_ride_status;
                         $date->payment_image = $pay_image;
                         $date->date = date('d-m-Y H:i', strtotime($date->rbs_created_at));

                     }
                 }else{
                     $driverLastDate = [];
                 }
                 $driverBalance = DriverAccount::where(['driver_accounts.dc_target_id'=> $driver->id, 'driver_accounts.dc_target_type' => 'driver','driver_accounts.dc_operation_type' => 'ride'])->get()->last();
                    if ($driverBalance){
                        $driverBalance = $driverBalance->dc_balance;
                    }

             return response()->json(['wallet' => view('driver.DriverWallet')->with(['crrDateRides' => $driverCrrDate,'lastDateRides' => $driverLastDate,'driverBalance' => $driverBalance])->render()]);

             }
             else{
                 return response()->json((object) null, 200);
             }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * create driver Current Location
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function driverCurrentLocation(Request $request){
        try{
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();

            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
                'numeric' => 'the_field_should_numeric',
                'gt' => 'the_field_should_greater_than_zero',
                'lt' => 'the_field_should_less_than_180',
            ];
            $validator = Validator::make($request->all(), [

                'lat' => 'required|numeric|gt:0|lt:180',
                'long' => 'required|numeric|gt:0|lt:180',

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

            if($driver->du_driver_status == "driver_status_when_approved") {
                if (DriverCurrentLocation::where(['dcl_user_id' => $driver->id, 'dcl_user_type' => 'driver'])->exists()) {
                    $driver_location = DriverCurrentLocation::where(['dcl_user_id' => $driver->id, 'dcl_user_type' => 'driver'])->first();
                } else {
                    $driver_location = new DriverCurrentLocation;
                }

                $city = $request->city;
                $country = $request->country;
                $driver_location->dcl_user_id = $driver->id;
                $driver_location->dcl_user_type = 'driver';
                $driver_location->dcl_lat = $request->lat;
                $driver_location->dcl_long = $request->long;
                $driver_location->dcl_app_active = $request->app_active;
                if (isset($country) && $country != null) {
                    $driver_location->dcl_country = $country;
                }
                if (isset($city) && $city != null) {
                    $driver_location->dcl_city = $city;
                }
                $driver_location->save();

                $driver = Driver::getdriver($driver->id);

                return response()->json($driver, 200);
            }else{
                $message = LanguageString::translated()->where('bls_name_key',$driver->du_driver_status)->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key', 'error' )->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     * get Driver Reviews list
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getDriverReviews(Request $request){

        try{

            $Reasoning1 = GetCancelReasoningResourcePassenger::collection(AppReference::translated()->where(['bar_status'=>1,'bar_mod_id_ref'=>7,'bar_ref_type_id'=>10])->get());
            if (count($Reasoning1) > 0){
                $Reasoning = $Reasoning1;
            }else{
                $Reasoning = [];
            }
            return response()->json($Reasoning, 200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * rating is created given by passenger for driver
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function passengerRating(Request $request){
        try{
            $token = JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
            $rating = $request->rating;
            $passenger_id = $request->passenger_id;
            $review_id = $request->review_id;
            $ride_id = $request->ride_id;
            $comments1 = $request->comments;
            if(isset($comments1) && $comments1 != null){$comments = $comments1;}else{$comments = null;}
            $user = User::find($passenger_id);
            if(isset($user) && $user->TransactionId->last() != null) {
                Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' =>$user->TransactionId->last()]);
            }else{
                Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => null]);
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
                'passenger_id' => 'required',
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
                'pr_driver_id'=>$driver->id,
                'pr_passenger_id'=>$passenger_id,
                'pr_rating'=>$rating,
                'pr_review_id'=>$review_id,
                'pr_comments'=>$comments,
                'pr_ride_id'=>$ride_id,
                'pr_created_at'=>now(),
                'pr_updated_at'=>now(),
            ];
            $passenger_rating = PassengerRating::create($data_rating);
            $message = LanguageString::translated()->where('bls_name_key','thanks_for_rating')->first()->name;
            return response()->json([
                'message'=>$message
            ], 200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     *  driver Update language (locale)
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function updateLocaleDriver(Request $request){
        Log::info('app.requests', ['request' => $request->all()]);
        try{
            $token=JWTAuth::getToken();
            $user = \Auth::guard('driver')->user();
            $locale = $request->header('Accept-Language');
            $update_locale = Driver::where('id',$user->id)->update(['locale'=>$locale]);
            $message = LanguageString::translated()->where('bls_name_key','language_is_updated')->first()->name;
            return response()->json([
                'message' => $message
            ], 200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

}
