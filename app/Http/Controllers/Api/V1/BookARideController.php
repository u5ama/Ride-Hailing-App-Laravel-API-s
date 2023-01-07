<?php

namespace App\Http\Controllers\Api\V1;

use App\AppReference;
use App\BaseAppControl;
use App\BaseAppNotification;
use App\BaseAppNotificationIgnored;
use App\Country;
use App\CustomerCreditCard;
use App\CustomerInvoice;
use App\Device;
use App\Driver;
use App\DriverCancelRideHistory;
use App\FarePlanHead;
use App\FireBase\FireBase;
use App\Http\Requests\updateDropOffLocationRequest;
use App\Http\Requests\UpdatePickUpLocationRequest;
use Hashids\Hashids;
use App\Http\Resources\DriverResource;
use App\Http\Resources\GetCancelReasoningResourceDriver;
use App\Http\Resources\GetCancelReasoningResourcePassenger;
use App\Http\Resources\GetDriverBookedRidesResource;
use App\Http\Resources\GetDriverResource;
use App\Http\Resources\GetJobResource;
use App\Http\Resources\GetMyCreditCardsResource;
use App\Http\Resources\GetPassengerBookedRideDetailResource;
use App\Http\Resources\GetPassengerBookedRides;
use App\Http\Resources\GetPassengerBookedRidesResource;
use App\Http\Resources\GetRideResource;
use App\LanguageString;
use App\Notification\Notification;
use App\PassengerAccount;
use App\PassengerCancelRideHistory;
use App\PassengerContactList;
use App\PassengerCurrentLocation;
use App\RideBookingSchedule;
use App\RideIgnoredBy;
use App\TransactionId;
use App\TransportType;
use App\User;
use App\Utility\Utility;
use DateTimeZone;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class BookARideController extends Controller
{
    /**
     * Display a listing of BookARide
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getRide(Request $request){

    try{
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>($user->TransactionId->last()) ? $user->TransactionId->last(): null]);
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
        ];
        // param validation
        $validator = Validator::make($request->all(), [
            'destination' => 'required',
            'destination.lat' => 'required|between:-90,90',
            'destination.long' => 'required|between:-180,180',
            'destination.distance' => 'required|numeric|gt:0',
            'destination.time' => 'required|numeric|gt:0',
            'passenger' => 'required',
            'passenger.lat' => 'required|between:-90,90',
            'passenger.long' => 'required|between:-180,180',
        ], $messages);
        // validator is fail then return false
        if ($validator->fails()) {

            $errors = [];
            foreach ($validator->errors()->messages() as $field => $message) {
                Log::error('app.validationError', ['field' => $field,'message'=>$message,'errorCode'=>401,'URL'=>$request->url(),'passenger' => $user,'token'=>$token]);
                $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                $errors[] = [
                    'field' => $field,
                    'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                ];
            }
            return response()->json(compact('errors'), 401);
        }
        $destination = $request->destination;
        $latitude = $request->passenger['lat'];
        $longitude = $request->passenger['long'];

        $pickup_location = app('geocoder')->reverse($latitude,$longitude)->get()->first();

        App::setLocale('en');
        // get country list
        $country = Country::listsTranslations('name')->select('countries.country_code')->where('country_translations.name', $pickup_location->getCountry()->getName())->first();

        if(isset($country) && $country != null){

        //timezone for one ALL co-ordinate
        $timezone = (new \App\Utility\Utility)->get_nearest_timezone($latitude,$longitude,$country->country_code);

        // create  passenger current location

        PassengerCurrentLocation::updateOrCreate([
            'pcl_passenger_id'   => $user->id,
        ],[
            'pcl_lat' => $latitude,
            'pcl_long' => $longitude,
            'pcl_country' => $pickup_location->getCountry()->getName(),
            'pcl_city' => $pickup_location->getLocality(),
            'pcl_current_date' => now()->setTimezone(new DateTimeZone($timezone)),
            'pcl_current_time' => now()->setTimezone(new DateTimeZone($timezone)),
        ]);

        $drNotIds = RideBookingSchedule::whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Accepted','Driving','Waiting'])->pluck('rbs_driver_id')->toArray();
       // get drivers

            $drivercompletedinvoice = CustomerInvoice::whereIn('ci_transaction_status',['pending','fail'])->pluck('ci_driver_id')->unique()->toArray();

//            $rideNotIds = RideBookingSchedule::whereIn('ride_booking_schedules.rbs_ride_status', ['Completed'])->whereIn('id',$drivercompletedinvoice)->pluck('rbs_driver_id')->unique()->toArray();
//            $rideNotIds1 = RideBookingSchedule::whereIn('ride_booking_schedules.rbs_ride_status', ['Completed'])->whereNotIn('id',$drivercompletedinvoice)->delete();
//            ?
            $drivers = Driver::leftjoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftjoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['driver_current_locations.dcl_app_active'=>1,'drivers.du_driver_status'=>'driver_status_when_approved'])->whereNotIn('drivers.id',$drNotIds)->whereNotIn('drivers.id',$drivercompletedinvoice)->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( dcl_lat ) ) * cos( radians( dcl_long ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( dcl_lat ) ) ) ) AS distance'))
            ->having('distance', '<', BaseAppControl::where('bac_meta_key','driver_search_distance')->first()->bac_meta_value)
           ->orderBy('distance')
            ->get();
        if(isset($drivers) && count($drivers) > 0) {
            $drivers_arrary = $drivers->toArray();
            $drivers_ids = array_map(function ($driver) {
                $ids = $driver['dcl_user_id'];
                return $ids;
            }, $drivers_arrary);

          // ttransport Type list
            $vehicles_types = TransportType::listsTranslations('name')->leftjoin('driver_profiles', 'transport_types.id', '=', 'driver_profiles.dp_transport_type_id_ref')->select('transport_types.id', 'transport_types.tt_image', 'transport_types.tt_marker', 'driver_profiles.dp_user_id')->whereIn('driver_profiles.dp_user_id', $drivers_ids)->get()->unique('id');
            $vehicles_types_array = $vehicles_types->toArray();
            $vehicles_types_ids = array_map(function ($person) {
                $ids = $person['id'];
                return $ids;
            }, $vehicles_types_array);

            foreach ($vehicles_types as $vehicles_type) {

                $passenger = PassengerCurrentLocation::where('pcl_passenger_id', $user->id)->first();
                $passenger_crr_date = $passenger->pcl_current_date;
                $passenger_crr_time = $passenger->pcl_current_time;

                $country = Country::listsTranslations('name')->where('country_translations.name', $passenger->pcl_country)->first();

                $getrates = FarePlanHead::leftjoin('fare_plan_details', 'fare_plan_head.id', '=', 'fare_plan_details.fpd_head_id_ref')
                    ->whereDate('fare_plan_head.fph_start_date','<=',$passenger_crr_date)
                    ->whereDate('fare_plan_head.fph_end_date','>=',$passenger_crr_date)
                    ->where(['fare_plan_head.fph_status' => 1,'fare_plan_head.fph_country_id' => $country->id, 'fare_plan_details.fpd_transport_type_id' => $vehicles_type->id])
                    ->whereTime('fare_plan_details.fpd_start_time','<=',$passenger_crr_time)
                    ->whereTime('fare_plan_details.fpd_end_time','>=',$passenger_crr_time)
                    ->orderBy('fare_plan_head.id','DESC')
                    ->first();

                if(!isset($getrates) && $getrates == null){
                    $getrates = FarePlanHead::leftjoin('fare_plan_details', 'fare_plan_head.id', '=', 'fare_plan_details.fpd_head_id_ref')
                        ->where(['fare_plan_head.fph_is_default' => 'default','fare_plan_head.fph_country_id' => $country->id, 'fare_plan_details.fpd_transport_type_id' => $vehicles_type->id])
                        ->orderBy('fare_plan_head.id','DESC')
                        ->first();
                }

                $KM_rate = $getrates->fpd_per_km_fare * $destination['distance'];

                $Time_rate = $getrates->fpd_per_minute_fare * $destination['time'];

                // New Calculations Updated

                $km_distance = BaseAppControl::where('bac_meta_key','driver_km_b4_customer_pickup')->first()->bac_meta_value;
                $mint_distance = BaseAppControl::where('bac_meta_key','driver_mintue_b4_customer_pickup')->first()->bac_meta_value;

               $v_type_id = $vehicles_type->id;
                $drivers_v_type = array_map(function ($driver) use ($v_type_id) {
                    if( $driver['dp_transport_type_id_ref'] == $v_type_id ){
                        return  $driver;
                    }
                }, $drivers_arrary);
                $drivers_with_v_type = array_filter($drivers_v_type);

                $drivers_with_v_type_index = array_splice($drivers_with_v_type,0,count($drivers_with_v_type), true);

                $rate_estimate_cal = BaseAppControl::where('bac_meta_key','rate_estimate_calculation')->first()->bac_meta_value;

                if ($rate_estimate_cal == 0){
                    $selected_for_estimate_rate_user = $drivers_with_v_type_index[0];
                }

                elseif ($rate_estimate_cal == 1){
                    $selected_for_estimate_rate_user = $drivers_with_v_type_index[count($drivers_with_v_type_index) - 1];
                }

                else{
                    $selected_for_estimate_rate_user = $drivers_with_v_type_index[0];
                }

                $selected_for_estimate_dis_time = Utility::timeAndDistance($selected_for_estimate_rate_user['dcl_lat'], $selected_for_estimate_rate_user['dcl_long'], $latitude, $longitude);

                $selected_for_estimate_rate['distance'] = $selected_for_estimate_dis_time->routes[0]->legs[0]->distance->value/1000;
                $selected_for_estimate_rate['time'] = $selected_for_estimate_dis_time->routes[0]->legs[0]->duration->value/60;

                if ($selected_for_estimate_rate['distance'] > $km_distance){
                    $dist = $selected_for_estimate_rate['distance'] - $km_distance;
                    $KM_rate_before = $getrates->fdp_per_km_fare_before_pickup * $dist;
                }else{
                    $KM_rate_before = 0;
                }

                if ($selected_for_estimate_rate['time'] > $mint_distance){
                    $time = $selected_for_estimate_rate['time'] - $mint_distance;
                    $Time_rate_before = $getrates->fpd_per_minutes_fare_before_pickup * $time;
                }else{
                    $Time_rate_before = 0;
                }

                    $finalRate = $getrates->fpd_base_fare + $KM_rate + $Time_rate;

                $finalRate_before = $KM_rate_before + $Time_rate_before;

                $TotalRate = $finalRate + $finalRate_before;
                // New Calculations Updated

                $TotalRateMAx = $TotalRate + ($TotalRate * $getrates->fpd_estimate_percentage / 100);

                $vehicles_type['TotalRate'] = $TotalRate;
                $vehicles_type['TotalRateMax'] = $TotalRateMAx;

                PassengerCurrentLocation::where('pcl_passenger_id', $user->id)->update([
                    'pcl_fare_plan_details_id' => $getrates->id,
                    'pcl_fare_plan_head_id' => $getrates->fpd_head_id_ref,
                ]);
            }

            $rides = GetRideResource::collection($vehicles_types);

            Log::info('app.response', ['response' => $rides, 'statusCode' => 200, 'URL' => $request->url(),'trxID'=>($user->TransactionId->last()) ? $user->TransactionId->last(): null]);
            App::setLocale($request->header('Accept-Language'));
            return response()->json($rides, 200);
            }else{
                $rides = [];
                Log::info('app.response', ['response' => $rides, 'statusCode' => 200, 'URL' => $request->url(),'trxID'=>($user->TransactionId->last()) ? $user->TransactionId->last(): null]);
                return response()->json($rides, 200);
            }
        }else {
            $rides = [];
            Log::info('app.response', ['response' => $rides, 'statusCode' => 200, 'URL' => $request->url(),'trxID'=>($user->TransactionId->last()) ? $user->TransactionId->last(): null]);
            return response()->json($rides, 200);
        }
         }catch(\Exception $e){
        Log::info('error_log', ['error_message' => $e->getMessage()]);
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['errors' => $errors], 500);
        }
    }

     /**
     * Display a listing of Ride Booking Schedule,Passenger Current Location
     * send notification,create app notification,create log
     * book A Ride Next
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function bookARide(Request $request){
    try{
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $data_trX = Utility::transID($user);
        Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);
        $messages = [
            'required' => 'the_field_is_required',
            'string' => 'the_string_field_is_required',
            'max' => 'the_field_is_out_from_max',
            'min' => 'the_field_is_low_from_min',
            'unique' => 'the_field_should_unique',
            'confirmed' => 'the_field_should_confirmed',
            'email' => 'the_field_should_email',
            'exists' => 'the_field_should_exists',
            'between' => 'the_field_should_between',
            'numeric' => 'the_field_should_numeric',
            'gt' => 'the_field_should_greater_than_zero',
            'lt' => 'the_field_should_less_than_180',
        ];
        $validator = Validator::make($request->all(), [
            'passenger' => 'required',
            'destination' => 'required',
            'total_max_rate' => 'required',
            'total_rate' => 'required',
            'transport_type' => 'required',
            'transport_id' => 'required',
            'payment_method' => 'required',
            'ride_total_duration' => 'required',
            'polyline' => 'required',
            'destination.address' => 'required',
            'passenger.address' => 'required',
            'passenger.before_pick_up_minutes' => 'required',
            'passenger.before_pick_up_km' => 'required',
            'destination.lat' => 'required|between:-90,90',
            'destination.long' => 'required|between:-180,180',
            'destination.distance' => 'required|numeric|gt:0',
            'destination.time' => 'required|numeric|gt:0',
            'passenger.lat' => 'required|between:-90,90',
            'passenger.long' => 'required|between:-180,180',

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
        $ride_available = Utility::restrictedArea($request);
        if($ride_available['success'] == false){

            $message = $ride_available['message'];
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 401);

        }
        $source = $request->passenger;
        $destination = $request->destination;
        $latitude = $source['lat'];
        $longitude = $source['long'];
        $for_contact = $request->for_contact;
        if(isset($for_contact) && $for_contact != null ){

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
            ];
            $validator = Validator::make($request->all(), [

                'for_contact.name' => 'required',
                'for_contact.contact_number' => 'required',

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
            $con_data = [
                'pcl_user_id'=>$user->id,
                'pcl_contact_name'=>$for_contact['name'],
                'pcl_contact_number'=>$for_contact['contact_number'],
            ];
            if(PassengerContactList::where($con_data)->exists()){
                $for_contact_name = PassengerContactList::where($con_data)->first();
            }else {
                $for_contact_name = PassengerContactList::create($con_data);
            }
        }

        $passenger = PassengerCurrentLocation::where('pcl_passenger_id', $user->id)->first();

        $drNotIds = RideBookingSchedule::whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Accepted','Driving','Waiting'])->pluck('rbs_driver_id')->toArray();
        $drivercompletedinvoice = CustomerInvoice::whereIn('ci_transaction_status',['pending','fail'])->pluck('ci_driver_id')->unique()->toArray();

       // driver list base on distance
        $drivers = Driver::leftjoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftjoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['driver_profiles.dp_transport_type_id_ref'=>$request->transport_id,'driver_current_locations.dcl_app_active'=>1,'drivers.du_driver_status'=>'driver_status_when_approved'])->whereNotIn('drivers.id',$drNotIds)->whereNotIn('drivers.id',$drivercompletedinvoice)->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( dcl_lat ) ) * cos( radians( dcl_long ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( dcl_lat ) ) ) ) AS distance'))
            ->having('distance', '<', BaseAppControl::where('bac_meta_key','driver_search_distance')->first()->bac_meta_value)
            ->orderBy('distance')
            ->get();

        $data = [];
        if(isset($drivers) && count($drivers) > 0) {
            foreach ($drivers as $key => $item) {
                if (RideBookingSchedule::where(['rbs_driver_id' => $item->dp_user_id])->whereIn('rbs_ride_status', ['Requested', 'Accepted', 'Driving', 'Waiting'])->exists()) {
                } else {
                    $data = [
                        'rbs_driver_id' => $item->dp_user_id,
                        'rbs_passenger_id' => $user->id,
                        'rbs_driver_lat' => $item->dcl_lat,
                        'rbs_driver_long' => $item->dcl_long,
                        'rbs_transport_id' => $request->transport_id,
                        'rbs_ride_total_duration' => $request->ride_total_duration,
                        'rbs_transport_type' => $request->transport_type,
                        'rbs_destination_address' => $destination['address'],
                        'rbs_source_address' => $source['address'],
                        'rbs_source_address_name' => (isset($source['address_name']))?$source['address_name']:"",
                        'rbs_before_pick_up_minutes' => $source['before_pick_up_minutes'],
                        'rbs_before_pick_up_km' => $source['before_pick_up_km'],
                        'rbs_source_lat' => $latitude,
                        'rbs_source_long' => $longitude,
                        'rbs_destination_lat' => $destination['lat'],
                        'rbs_destination_long' => $destination['long'],
                        'rbs_destination_distance' => $destination['distance'],
                        'rbs_destination_address_name' => (isset($destination['address_name']))?$destination['address_name']:"",
                        'rbs_destination_time' => round($destination['time']),
                        'rbs_driving_start_time' => now(),
                        'rbs_estimated_cost' => $request->total_max_rate,
                        'rbs_estimated_cost_min' => $request->total_rate,
                        'ban_promo_id' => $request->promo_id,
                        'rbs_contact_id' => (isset($for_contact_name->id) && $for_contact_name->id != null) ? $for_contact_name->id : null,
                        'rbs_payment_method' => $request->payment_method,
                        'rbs_fare_plan_detail_id' => $passenger->pcl_fare_plan_details_id,
                        'rbs_fare_plan_head_id' => $passenger->pcl_fare_plan_head_id,
                        'rbs_ride_status' => 1,
                        'rbs_Trx_id' => $user->TransactionId->last()->trx_ID,
                        'rbs_polyline' => $request->polyline,
                        'rbs_created_at' => now(),
                    ];
                    break;
                }
            }
            if (count($data) > 0) {
                $BookAS1 = RideBookingSchedule::create($data);
                $BookAS = RideBookingSchedule::where('id', $BookAS1->id)->first();
                $driver = Driver::find($BookAS->rbs_driver_id);
                $BookAS['bearing'] = -1;
                $setfirebase = FireBase::store($BookAS->id,$BookAS);
                $user_data = User::getuser($user->id);
                $NodeUser = FireBase::storeuser($user->id,$user_data);
                $user = User::find($BookAS->rbs_passenger_id);
                $driver_data = Driver::getdriverfull($driver->id);
                $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                $node = "ride_status";
                $data = "Requested";
                $driver1 = FireBase::updateDriver($driver->id,$node,$data);

                $tokensand = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "Android", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
                $tokensios = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "iOS", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
                App::setLocale($driver->locale);
                $title = LanguageString::translated()->where('bls_name_key', 'passenger_book_a_ride')->first()->name;
                $body =  $driver->du_full_name. " ". LanguageString::translated()->where('bls_name_key', "passenger_book_a_ride_desc")->first()->name;

                if ($body != null || $title != null) {
                    $title = LanguageString::translated()->where('bls_name_key', 'passenger_book_a_ride')->first()->name;
                    $body = $driver->du_full_name. " ". LanguageString::translated()->where('bls_name_key', "passenger_book_a_ride_desc")->first()->name;
                }
                App::setLocale('en');
                $sound = 'default';
                $action = 'BookARide';
                $id = $BookAS->id;
                $type = 'silent';
                $total_drivers = count($drivers);
                $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $BookAS->rbs_passenger_id, $BookAS, $total_drivers);


                if ($notifications != 1) {
                    $notify = 0;
                    BaseAppNotificationIgnored::create([
                        'ani_driver_id' => $driver->id,
                        'ani_ride_id' => $id,
                        'ani_fcm_token_android' => implode("|", $tokensand),
                        'ani_fcm_token_ios' => implode("|", $tokensios)
                    ]);
                } else {
                    $notify = $notifications;
                }


                $noti_data = [
                    'ban_sender_id' => $user->id,
                    'ban_sender_type' => 'Passenger',
                    'ban_recipient_type' => 'Driver',
                    'ban_recipient_id' => $BookAS->rbs_driver_id,
                    'ban_type_of_notification' => $type,
                    'ban_title_text' => $title,
                    'ban_body_text' => $body,
                    'ban_activity' => $action,
                    'ban_notifiable_type' => 'App\RideBookingSchedule',
                    'ban_notifiable_id' => $BookAS->id,
                    'ban_notification_status' => $notify,
                    'ban_created_at' => now(),
                    'ban_updated_at' => now()
                ];
                $app_notification = BaseAppNotification::create($noti_data);

                    $message = LanguageString::translated()->where('bls_name_key', 'we_are_finding_your_driver')->first()->name;
                    $driver = Driver::getdriver($driver->id);
                    $bool = true;
                Log::info(json_encode(["response" =>['success' => false,'driver'=>$driver,'message'=>$message],"statusCode"=>200,"URL"=>$request->url(),"passenger" =>$user]));

                return response()->json(['success' => $bool ,'driver'=>$driver,'message'=>$message],200);

            } else {

                $message = LanguageString::translated()->where('bls_name_key','driver_is_busy')->first()->name;
                Log::error('aap.exception', ["errorCode"=>403,"URL"=>$request->url(),"passenger" =>$user]);
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 403);

            }
        }else{

            $message = LanguageString::translated()->where('bls_name_key','no_driver_found')->first()->name;
            Log::error('aap.exception', ["errorCode"=>403,"URL"=>$request->url(),"passenger" =>$user]);
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 403);

        }
        Log::info(json_encode(["response" =>['success' => false,'driver'=>$driver,'message'=>$message],"statusCode"=>200,"URL"=>$request->url(),"passenger" =>$user]));

        return response()->json(['success' => $bool ,'driver'=>$driver,'message'=>$message],200);

            }catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        Log::error('aap.exception', ["exception" =>$e,"errorCode"=>500,"URL"=>$request->url(),"passenger" =>$user]);
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['errors' => $errors], 500);
        }
    }

     /**
     * Display a listing of Drivers by distance
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getDrivers(Request $request){

        try{

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        $user = User::find($user->id);
        Log::info(json_encode(['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]));
        $latitude = $request->passenger_lat;
        $longitude = $request->passenger_long;
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
            'passenger_lat' => 'required',
            'passenger_long' => 'required',
        ], $messages);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->messages() as $field => $message) {
                Log::error('app.error',['field' => $field,'message'=>$message,'errorCode'=>401,'URL'=>$request->url(),'passenger' => $user,'token'=>$request->header('Authorization')]);
                $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                $errors[] = [
                    'field' => $field,
                    'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                ];
            }
            return response()->json(compact('errors'), 401);
        }

        $driver = Driver::leftjoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->leftjoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->where(['driver_current_locations.dcl_app_active'=>1,'drivers.du_driver_status'=>'driver_status_when_approved'])->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( dcl_lat ) ) * cos( radians( dcl_long ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( dcl_lat ) ) ) ) AS distance'))
            ->having('distance', '<', BaseAppControl::where('bac_meta_key','driver_search_distance')->first()->bac_meta_value)
            ->orderBy('distance')
            ->get();
        $rides = GetDriverResource::collection($driver);

        Log::info(json_encode(["response" =>$rides,"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
        return response()->json($rides);
            }catch(\Exception $e){
        Log::error('aap.exception', ["exception" =>$e,"errorCode"=>500,"URL"=>$request->url()]);

        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get Drivers Time And Distance base on distance give latitude and longitude
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getDriversTimeAndDistance(Request $request){

        try{

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        Log::info(json_encode(['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]));
        $latitude = $request->passenger_lat;
        $longitude = $request->passenger_long;
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
            'passenger_lat' => 'required',
            'passenger_long' => 'required',
        ], $messages);
        if ($validator->fails()) {
            $errors = [];
                foreach ($validator->errors()->messages() as $field => $message) {
                    Log::error('app.error',['field' => $field,'message'=>$message,'errorCode'=>401,'URL'=>$request->url(),'passenger' => $user,'token'=>$request->header('Authorization')]);
                    $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                    $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                    $errors[] = [
                        'field' => $field,
                        'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                    ];
                }
                return response()->json(compact('errors'), 401);
            }
                $driver = Driver::leftjoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->where(['driver_current_locations.dcl_app_active'=>1,'drivers.du_driver_status'=>'driver_status_when_approved'])->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( dcl_lat ) ) * cos( radians( dcl_long ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( dcl_lat ) ) ) ) AS distance'))
                    ->having('distance', '<', BaseAppControl::where('bac_meta_key','driver_search_distance')->first()->bac_meta_value)
                    ->orderBy('distance')
                    ->get();
                $rides = GetDriverResource::collection($driver);

                Log::info(json_encode(["response" =>$rides,"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));

                if (count($rides)>0){
                    return response()->json(['success' => true,'message' => LanguageString::translated()->where('bls_name_key', 'drivers_found' )->first()->name,'driver' => $rides[0]]);
                }else{
                    return response()->json(['success' => false,'message' => LanguageString::translated()->where('bls_name_key', 'driver_not_found')->first()->name,'driver' => null]);
                }

            }
            catch(\Exception $e)
            {
            Log::error('aap.exception', ["exception" =>$e,"errorCode"=>500,"URL"=>$request->url()]);
                $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 500);
            }
        }

    /**
     * get Ride Booking Schedule as job data
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getJob(Request $request){
        try{

            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
           $jobs_data = RideBookingSchedule::leftjoin('users', 'ride_booking_schedules.rbs_passenger_id', '=', 'users.id')->select('ride_booking_schedules.*')->where(['ride_booking_schedules.rbs_driver_id'=>$driver->id])->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Accepted','Driving','Waiting'])->orderBy('ride_booking_schedules.id','desc')->get();

            if (count($jobs_data) > 0){
                $jobs = GetJobResource::collection($jobs_data);
                $jobs  =  $jobs[0];
                $user = User::find($jobs->rbs_passenger_id);
                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);
                $user = User::getuser($jobs->rbs_passenger_id);
                Log::info(json_encode(["response" =>$jobs,"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
            }else{
                $jobs  = null;
                $user  = null;
                Log::info(json_encode(["response" =>$jobs,"statusCode"=>200,"URL"=>$request->url(),'trxID'=>null]));
            }

            return response()->json(['jobs'=>$jobs,'user'=>$user],200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * cancel Job base on ride is Canceled
     * @param Request $request
     * @return Response
     * @throws Exception
     */


    public function cancelJob(Request $request,$id){

        try{
            Log::info('app.requests', ['request' => $request->all()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
            $jobs_rejected = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_driver_id'=>$driver->id])->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Accepted','Driving','Waiting'])->first();
        $jobs_rejected1 = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_driver_id'=>$driver->id])->first();

           $user = User::find($jobs_rejected1->rbs_passenger_id);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

            $user = User::getuser($jobs_rejected1->rbs_passenger_id);
            if($jobs_rejected) {
                $data_reject = [
                    'dcrh_job_id' => $id,
                    'dcrh_passenger_id' => $user->id,
                    'dcrh_driver_id' => $jobs_rejected->rbs_driver_id,
                    'dcrh_reason_id' => $request->reason_id,
                    'dcrh_comments' => $request->comments,
                    'dcrh_created_at' => now()
                ];
                $cancelledRide = DriverCancelRideHistory::create($data_reject);
                $jobs = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->whereIn('rbs_ride_status', ['Requested', 'Accepted', 'Driving', 'Waiting'])->update(['rbs_ride_status' => 'Cancelled', 'rbs_driving_end_time' => now()]);
                $tokensand = Device::where(['user_id' => $user->id, 'device_type' => "Android",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                $tokensios = Device::where(['user_id' => $user->id, 'device_type' => "iOS",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                App::setLocale($user->locale);
                $title =   LanguageString::translated()->where('bls_name_key','driver_cancel_ride')->first()->name;
                $body = $driver->du_full_name. " ". LanguageString::translated()->where('bls_name_key','driver_cancel_ride_body')->first()->name;
                App::setLocale($request->header('Accept-Language'));
                $sound = 'default';
                $action = 'CancelRide';
                $type = 'pushNotification';

                $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $user->id, $jobs_rejected,$drivers =1);
                if($notifications != 1){
                    $notify = 0;
                    BaseAppNotificationIgnored::create([
                        'ani_driver_id' => $driver->id,
                        'ani_ride_id' => $id,
                        'ani_fcm_token_android' => implode("|",$tokensand),
                        'ani_fcm_token_ios' => implode("|",$tokensios)
                    ]);
                }else{
                    $notify = $notifications;

                }
                $noti_data = [
                    'ban_sender_id'=>$driver->id,
                    'ban_sender_type'=>'Driver',
                    'ban_recipient_type'=>'Passenger',
                    'ban_recipient_id'=>$jobs_rejected->rbs_passenger_id,
                    'ban_type_of_notification'=>$type,
                    'ban_title_text'=>$title,
                    'ban_body_text'=>$body,
                    'ban_activity'=>$action,
                    'ban_notifiable_type'=>'App\RideBookingSchedule',
                    'ban_notifiable_id'=>$jobs_rejected->id,
                    'ban_notification_status'=>$notify,
                    'ban_created_at'=>now(),
                    'ban_updated_at'=>now()
                ];
                $app_notification = BaseAppNotification::create($noti_data);


                $to = Carbon::parse($jobs_rejected->rbs_driving_start_time);
                $from = Carbon::parse($jobs_rejected->rbs_driving_end_time);

                $time = $to->diffInMinutes($from);


                $cancelation_time = BaseAppControl::where('bac_meta_key','cancelation_time')->first()->bac_meta_value;
                if ($time >= $cancelation_time) {

                    $time = $time - $cancelation_time;
                    // get cancel rate
                    $rate = Utility::getRateCancel($jobs_rejected->rbs_passenger_id, $time, $jobs_rejected, $id);
                    $rate1 = $rate[0];
                    $basefare = $rate[1];
                    $finalRate_before = $rate[2];
                    $initial_wait_rate = $rate[3];
                    $vatPlan = $rate[4];
                    $taxPlan = $rate[5];

                    $createInvoice = InvoiceController::createInvoiceCancelDriver($rate1, $basefare, $jobs_rejected, $user->TransactionId->last(), $finalRate_before, $initial_wait_rate, $vatPlan, $taxPlan);
                }

                $message = LanguageString::translated()->where('bls_name_key','you_cancel_the_ride')->first()->name;
                $deleteRide = FireBase::delete($id);
                $driver_data = Driver::getdriverfull($driver->id);
                $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                $user_data = User::getuser($user->id);
                $NodeUser = FireBase::storeuser($user->id,$user_data);
                $driver = Driver::getdriver($jobs_rejected->rbs_driver_id);
                $node = "ride_status";
                $data = "Cancelled";
                $driver1 = FireBase::updateDriver($jobs_rejected->rbs_driver_id,$node,$data);
                Log::info(json_encode(["response" =>['driver'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
                return response()->json(['message'=>$message], 200);
            }else{

                $message = LanguageString::translated()->where('bls_name_key','not_allowed')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * reject Job based on ride is rejected
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function rejectJob(Request $request,$id)
    {

        try {
            Log::info('app.requests', ['request' => $request->all()]);
            $token = JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
            $jobs_accepted = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->update(['rbs_ride_status' => 'Rejected']);
            $ignoreBy = RideIgnoredBy::create(['rib_driver_id' => $driver->id, 'rib_ride_id' => $id]);
            $driver_data = Driver::getdriverfull($driver->id);
            $NodeUser = FireBase::storedriver($driver->id,$driver_data);
            if($jobs_accepted == 1) {

                $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->first();
                $latitude = $Passenger->rbs_source_lat;
                $longitude = $Passenger->rbs_source_long;
                $ignoreByget = RideIgnoredBy::where('rib_ride_id', $id)->pluck('rib_driver_id')->toArray();

                $drivers = Driver::leftjoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftjoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['driver_profiles.dp_transport_type_id_ref'=>$Passenger->rbs_transport_id,'driver_current_locations.dcl_app_active'=>1,'drivers.du_driver_status'=>'driver_status_when_approved'])->whereNotIn('drivers.id', $ignoreByget)->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( dcl_lat ) ) * cos( radians( dcl_long ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( dcl_lat ) ) ) ) AS distance'))
                    ->having('distance', '<', BaseAppControl::where('bac_meta_key','driver_search_distance')->first()->bac_meta_value)
                    ->orderBy('distance')
                    ->get();
                $data = [];
                if (count($drivers) > 0) {
                    foreach ($drivers as $key => $item) {
                        if (RideBookingSchedule::where(['rbs_driver_id' => $item->dp_user_id])->whereIn('rbs_ride_status', ['Requested', 'Accepted', 'Driving', 'Waiting'])->exists()) {
                        } else {
                            $data = [
                                'rbs_driver_id' => $item->dp_user_id,
                                'rbs_passenger_id' => $Passenger->rbs_passenger_id,
                                'rbs_driver_lat' => $item->dcl_lat,
                                'rbs_driver_long' => $item->dcl_long,
                                'rbs_transport_id' => $Passenger->rbs_transport_id,
                                'rbs_transport_type' => $Passenger->rbs_transport_type,
                                'rbs_source_lat' => $Passenger->rbs_source_lat,
                                'rbs_source_long' => $Passenger->rbs_source_long,
                                'rbs_destination_lat' => $Passenger->rbs_destination_lat,
                                'rbs_destination_long' => $Passenger->rbs_destination_long,
                                'rbs_destination_distance' => $Passenger->rbs_destination_distance,
                                'rbs_destination_time' => $Passenger->rbs_destination_time,
                                'rbs_ride_status' => 1,
                            ];

                            break;
                        }
                    }
                }

                if (count($data) > 0) {
                    $BookAS = RideBookingSchedule::where(['ride_booking_schedules.id' => $id])->update($data);
                    $BookAS = RideBookingSchedule::where(['ride_booking_schedules.id' => $id])->first();
                    $driver = Driver::getdriver($BookAS->rbs_driver_id);
                    $BookAS['bearing'] = -1;
                    $setfirebase = FireBase::store($BookAS->id,$BookAS);
                    $user = User::find($BookAS->rbs_passenger_id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    $node = "ride_status";
                    $data = "Requested";
                    $driver1 = FireBase::updateDriver($driver->id,$node,$data);
                   // get device token list to send notification
                    $tokensand = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "Android", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
                    $tokensios = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "iOS", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
                    App::setLocale($driver->locale);
                    $title = LanguageString::translated()->where('bls_name_key','passenger_book_call_again')->first()->name;
                    $body = $driver->du_full_name . " " .LanguageString::translated()->where('bls_name_key',"passenger_book_call_again_desc")->first()->name;
                    App::setLocale($request->header('Accept-Language'));
                    $sound = 'default';
                    $action = 'BookARide';
                    $id = $BookAS->id;
                    $type = 'silent';
                    // count total drivers
                    $total_drivers = count($drivers);
                    $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $BookAS->rbs_passenger_id, $BookAS, $total_drivers);
//                    if($notifications != 1){
//                        $notify = 0;
//                        BaseAppNotificationIgnored::create([
//                            'ani_driver_id' => $driver->id,
//                            'ani_ride_id' => $id,
//                            'ani_fcm_token_android' => implode("|",$tokensand),
//                            'ani_fcm_token_ios' => implode("|",$tokensios)
//                        ]);
//                    }else{
//                        $notify = $notifications;
//
//                    }
                    $notify = $notifications;
                    $noti_data = [
                        'ban_sender_id'=>$BookAS->rbs_passenger_id,
                        'ban_recipient_id'=>$BookAS->rbs_driver_id,
                        'ban_sender_type'=>'Passenger',
                        'ban_recipient_type'=>'Driver',
                        'ban_type_of_notification'=>$type,
                        'ban_title_text'=>$title,
                        'ban_body_text'=>$body,
                        'ban_activity'=>$action,
                        'ban_notifiable_type'=>'App\RideBookingSchedule',
                        'ban_notifiable_id'=>$BookAS->id,
                        'ban_notification_status'=>$notify,
                        'ban_created_at'=>now(),
                        'ban_updated_at'=>now()
                    ];
                    $app_notification = BaseAppNotification::create($noti_data);

                    $message = 'successfully Rejected';
                    $user = User::find($BookAS->rbs_passenger_id);
                    Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
                    Log::info(json_encode(["response" => ['driver' => $driver, 'message' => $message], "statusCode" => 200, "URL" => $request->url(), 'trxID' => $user->TransactionId->last()]));

                    return response()->json(['message' => $message], 200);

                } else {
                    $BookAS = RideBookingSchedule::where(['ride_booking_schedules.id' => $id])->first();
                    $user = User::find($BookAS->rbs_passenger_id);
                    $driver = Driver::find($BookAS->rbs_driver_id);

                    $tokensand = Device::where(['user_id' => $BookAS->rbs_passenger_id, 'device_type' => "Android", 'app_type' => 'Passenger'])->pluck('device_token')->toArray();
                    $tokensios = Device::where(['user_id' => $BookAS->rbs_passenger_id, 'device_type' => "iOS", 'app_type' => 'Passenger'])->pluck('device_token')->toArray();
                    App::setLocale($user->locale);
                    $title = LanguageString::translated()->where('bls_name_key','driver_rejected_ride')->first()->name;
                    $body = LanguageString::translated()->where('bls_name_key',"driver_rejected_ride")->first()->name;
                    App::setLocale($request->header('Accept-Language'));
                    $sound = 'default';
                    $action = 'DriversAreBusy';
                    $id = $BookAS->id;
                    $type = 'default';
                    $drivers_count = 0;
                    $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $BookAS->rbs_passenger_id, $BookAS, $drivers_count);
                    if($notifications != 1){
                        $notify = 0;
                        BaseAppNotificationIgnored::create([
                            'ani_driver_id' => $driver->id,
                            'ani_ride_id' => $id,
                            'ani_fcm_token_android' => implode("|",$tokensand),
                            'ani_fcm_token_ios' => implode("|",$tokensios)
                        ]);
                    }else{
                        $notify = $notifications;

                    }
                    $noti_data = [
                        'ban_sender_id'=>$BookAS->rbs_driver_id,
                        'ban_recipient_id'=>$BookAS->rbs_passenger_id,
                        'ban_sender_type'=>'Driver',
                        'ban_recipient_type'=>'Passenger',
                        'ban_type_of_notification'=>$type,
                        'ban_title_text'=>$title,
                        'ban_body_text'=>$body,
                        'ban_activity'=>$action,
                        'ban_notifiable_type'=>'App\RideBookingSchedule',
                        'ban_notifiable_id'=>$BookAS->id,
                        'ban_notification_status'=>$notify,
                        'ban_created_at'=>now(),
                        'ban_updated_at'=>now()
                    ];
                    $app_notification = BaseAppNotification::create($noti_data);

                    $driver = Driver::getdriver($BookAS->rbs_driver_id);
                    $node = "ride_status";
                    $data = "Rejected";
                    $driver1 = FireBase::updateDriver($driver->id,$node,$data);
                    $message = 'Successfully Rejected';
                    $user = User::find($BookAS->rbs_passenger_id);
                    Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
                    Log::info(json_encode(["response" => ['driver' => $driver, 'message' => $message], "statusCode" => 200, "URL" => $request->url(), 'trxID' => $user->TransactionId->last()]));

                    return response()->json(['message' => $message], 200);
                }
            }else{
                $message = LanguageString::translated()->where('bls_name_key','ride_not_rejected')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);

           }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function updatePickUpLocation(UpdatePickUpLocationRequest $request)
    {
        try {
            Log::info('app.requests', ['request' => $request->all()]);
            $token=JWTAuth::getToken();
            $user = \Auth::user();
            $validated = $request->validated();
            $id = $validated['ride_id'];


            if(RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Accepted'])->exists()) {

                $change_pick_up_location_allowed_distance_meters = BaseAppControl::where('bac_meta_key','change_pick_up_location_allowed_distance_meters')->first()->bac_meta_value;
                $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->first();
                $selected_for_estimate_d_t = Utility::timeAndDistance($Passenger->rbs_source_lat, $Passenger->rbs_source_long, $validated['lat'], $validated['long']);
                $selected_for_estimate_rate_km = $selected_for_estimate_d_t->routes[0]->legs[0]->distance->value;
                if($change_pick_up_location_allowed_distance_meters > $selected_for_estimate_rate_km){
                $jobs_accepted = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->update(
                    [
                        'rbs_source_address' => $validated['address'],
                        'rbs_source_long' => $validated['long'],
                        'rbs_source_lat' => $validated['lat'],
                        'rbs_source_address_name' => (isset($request->address_name))?$request->address_name:"",
                    ]);

                $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->first();

                $driver = Driver::find($Passenger->rbs_driver_id);
                $driver_data = Driver::getdriverfull($Passenger->rbs_driver_id);
                $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                $latitude = $Passenger->rbs_source_lat;
                $longitude = $Passenger->rbs_source_long;

                    $BookAS = $Passenger;
                    $driver = Driver::getdriver($BookAS->rbs_driver_id);
                    $BookAS['bearing'] = -1;
                    $setfirebase = FireBase::store($BookAS->id,$BookAS);

                   // get device token list to send notification
                    $tokensand = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "Android", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
                    $tokensios = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "iOS", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
                    App::setLocale($driver->locale);
                    $title = LanguageString::translated()->where('bls_name_key','passenger_pick_up_change')->first()->name;
                    $body = LanguageString::translated()->where('bls_name_key',"passenger_pick_up_change_desc")->first()->name;
                    App::setLocale($request->header('Accept-Language'));
                    $sound = 'default';
                    $action = 'BookARide';
                    $id = $BookAS->id;
                    $type = 'silent';
                    // count total drivers
                    $total_drivers = 1;
                    $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $BookAS->rbs_passenger_id, $BookAS, $total_drivers);
//
                    $notify = $notifications;
                    $noti_data = [
                        'ban_sender_id'=>$BookAS->rbs_passenger_id,
                        'ban_recipient_id'=>$BookAS->rbs_driver_id,
                        'ban_sender_type'=>'Passenger',
                        'ban_recipient_type'=>'Driver',
                        'ban_type_of_notification'=>$type,
                        'ban_title_text'=>$title,
                        'ban_body_text'=>$body,
                        'ban_activity'=>$action,
                        'ban_notifiable_type'=>'App\RideBookingSchedule',
                        'ban_notifiable_id'=>$BookAS->id,
                        'ban_notification_status'=>$notify,
                        'ban_created_at'=>now(),
                        'ban_updated_at'=>now()
                    ];
                    $app_notification = BaseAppNotification::create($noti_data);

                    $message = LanguageString::translated()->where('bls_name_key','passenger_pick_up_change')->first()->name;
                    $user = User::find($BookAS->rbs_passenger_id);
                    Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
                    Log::info(json_encode(["response" => ['driver' => $driver, 'message' => $message], "statusCode" => 200, "URL" => $request->url(), 'trxID' => $user->TransactionId->last()]));

                    return response()->json(['message' => $message], 200);

                } else {
                    $message = LanguageString::translated()->where('bls_name_key','pick_up_change_area_limit_is_exceed')->first()->name;
                    $error = ['field'=>'language_strings','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 401);
                }
            }else{
                $message = LanguageString::translated()->where('bls_name_key','not_allowed')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);

           }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function updateDropOffLocation(updateDropOffLocationRequest $request)
    {
        try {
            Log::info('app.requests', ['request' => $request->all()]);
            $token=JWTAuth::getToken();
            $user = \Auth::user();
            $validated = $request->validated();
            $id = $validated['ride_id'];


            if(RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Accepted','Driving','Waiting'])->exists()) {

                $change_pick_up_location_allowed_distance_meters = BaseAppControl::where('bac_meta_key','change_pick_up_location_allowed_distance_meters')->first()->bac_meta_value;
                $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->first();
                $selected_for_estimate_d_t = Utility::timeAndDistance($Passenger->rbs_source_lat, $Passenger->rbs_source_long, $validated['lat'], $validated['long']);
                $selected_for_estimate_rate_km = $selected_for_estimate_d_t->routes[0]->legs[0]->distance->value;
                if($change_pick_up_location_allowed_distance_meters > $selected_for_estimate_rate_km){
                $jobs_accepted = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->update(
                    [
                        'rbs_destination_address' => $validated['address'],
                        'rbs_destination_long' => $validated['long'],
                        'rbs_destination_lat' => $validated['lat'],
                        'rbs_destination_time' => $validated['time'],
                        'rbs_destination_distance' => $validated['distance'],
                        'rbs_destination_address_name' => (isset($request->address_name))?$request->address_name:"",
                    ]);

                $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->first();

                $driver = Driver::find($Passenger->rbs_driver_id);
                $driver_data = Driver::getdriverfull($Passenger->rbs_driver_id);
                $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                $latitude = $Passenger->rbs_source_lat;
                $longitude = $Passenger->rbs_source_long;

                    $BookAS = $Passenger;
                    $driver = Driver::getdriver($BookAS->rbs_driver_id);
                    $BookAS['bearing'] = -1;
                    $setfirebase = FireBase::store($BookAS->id,$BookAS);

                   // get device token list to send notification
                    $tokensand = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "Android", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
                    $tokensios = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "iOS", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
                    App::setLocale($driver->locale);
                    $title = LanguageString::translated()->where('bls_name_key','passenger_drop_off_change')->first()->name;
                    $body = LanguageString::translated()->where('bls_name_key',"passenger_drop_off_change_desc")->first()->name;
                    App::setLocale($request->header('Accept-Language'));
                    $sound = 'default';
                    $action = 'BookARide';
                    $id = $BookAS->id;
                    $type = 'silent';
                    // count total drivers
                    $total_drivers = 1;
                    $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $BookAS->rbs_passenger_id, $BookAS, $total_drivers);
//
                    $notify = $notifications;
                    $noti_data = [
                        'ban_sender_id'=>$BookAS->rbs_passenger_id,
                        'ban_recipient_id'=>$BookAS->rbs_driver_id,
                        'ban_sender_type'=>'Passenger',
                        'ban_recipient_type'=>'Driver',
                        'ban_type_of_notification'=>$type,
                        'ban_title_text'=>$title,
                        'ban_body_text'=>$body,
                        'ban_activity'=>$action,
                        'ban_notifiable_type'=>'App\RideBookingSchedule',
                        'ban_notifiable_id'=>$BookAS->id,
                        'ban_notification_status'=>$notify,
                        'ban_created_at'=>now(),
                        'ban_updated_at'=>now()
                    ];
                    $app_notification = BaseAppNotification::create($noti_data);

                    $message = LanguageString::translated()->where('bls_name_key','passenger_drop_off_change')->first()->name;
                    $user = User::find($BookAS->rbs_passenger_id);
                    Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
                    Log::info(json_encode(["response" => ['driver' => $driver, 'message' => $message], "statusCode" => 200, "URL" => $request->url(), 'trxID' => $user->TransactionId->last()]));

                    return response()->json(['message' => $message], 200);

                } else {
                    $message = LanguageString::translated()->where('bls_name_key','drop_off_change_area_limit_is_exceed')->first()->name;
                    $error = ['field'=>'language_strings','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 401);
                }
            }else{
                $message = LanguageString::translated()->where('bls_name_key','not_allowed')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
           }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }


    /**
     * accept Job based on ride is accepted by driver or passenger
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function acceptJob(Request $request,$id){

        try{
            Log::info('app.requests', ['request' => $request->all()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
            $jobs_Accepted = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_driver_id'=>$driver->id,'rbs_ride_status'=> 'Requested'])->update(['rbs_ride_status'=>"Accepted",'rbs_driving_start_time'=> now(),'rbs_driving_wait_start_time'=> now()]);

            if($jobs_Accepted == 1) {

                $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->first();
                $user = User::find($Passenger->rbs_passenger_id);

                if (isset($Passenger->rbs_contact_id) && $Passenger->rbs_contact_id !== null){
                    App::setLocale($user->locale);
//                    $hash = new Hashids();
//                    $enId = $hash->encode($Passenger->id);
                    $message_sms = LanguageString::translated()->where('bls_name_key','map_hi')->first()->name .' '.$driver->du_full_name.' '. LanguageString::translated()->where('bls_name_key','map_then')->first()->name. ' '.$user->name . ' '.LanguageString::translated()->where('bls_name_key','map_message')->first()->name. ' '.route('map', [$id]);
                    $user_number = "96597631404";
                    $sendSMS = Utility::sendSMS($message_sms,$user_number);
                    $urlhashed = 'https://app.apis.ridewhipp.com/map/'.$id;
                    $jobs_Accepte_url = RideBookingSchedule::where('ride_booking_schedules.id',$Passenger->id)->update(['ride_booking_schedules.rbs_tracking_url' => $urlhashed ]);

                }

                $Passenger['bearing'] = -1;
                $setfirebase = FireBase::store($id,$Passenger);
                $user = User::find($Passenger->rbs_passenger_id);
                $user_data = User::getuser($user->id);
                $NodeUser = FireBase::storeuser($user->id,$user_data);

                $driver_data = Driver::getdriverfull($driver->id);
                $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                $node = "ride_status";
                $data = "Accepted";
                $driver1 = FireBase::updateDriver($driver->id,$node,$data);

                $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS",'app_type'=>'Passenger'])->pluck('device_token')->toArray();

                $title = LanguageString::translated()->where('bls_name_key','new_ride_booked')->first()->name;
                $body = $driver->du_full_name. " ". LanguageString::translated()->where('bls_name_key',"new_ride_booked_body")->first()->name;
                App::setLocale($request->header('Accept-Language'));

                $sound = 'default';
                $action = 'AcceptedRide';
                $type = 'default';
                $drivers_count = 0;
                $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger,$drivers_count);

               if($notifications != 1){
                    $notify = 0;
                    BaseAppNotificationIgnored::create([
                        'ani_driver_id' => $driver->id,
                        'ani_ride_id' => $id,
                        'ani_fcm_token_android' => implode("|",$tokensand),
                        'ani_fcm_token_ios' => implode("|",$tokensios)
                    ]);
                }else{
                    $notify = $notifications;

                }
                $noti_data = [
                    'ban_sender_id'=>$Passenger->rbs_driver_id,
                    'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                    'ban_sender_type'=>'Driver',
                    'ban_recipient_type'=>'Passenger',
                    'ban_type_of_notification'=>$type,
                    'ban_title_text'=>$title,
                    'ban_body_text'=>$body,
                    'ban_activity'=>$action,
                    'ban_notifiable_type'=>'App\RideBookingSchedule',
                    'ban_notifiable_id'=>$Passenger->id,
                    'ban_notification_status'=>$notify,
                    'ban_created_at'=>now(),
                    'ban_updated_at'=>now()
                ];
                $app_notification = BaseAppNotification::create($noti_data);

                $user = User::find($Passenger->rbs_passenger_id);
                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

               if ($Passenger != null) {
                   $user = User::getuser($Passenger->rbs_passenger_id);
               } else {
                   $user = (object)array();
               }
                Log::info(json_encode(["response" =>['driver'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));

                return response()->json($user, 200);
           }else{
                $message = LanguageString::translated()->where('bls_name_key','id_does_not_match')->first()->name;
               $error = ['field'=>'language_strings','message'=>$message];
               $errors =[$error];
               return response()->json(['errors' => $errors], 401);
           }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * completed Job based on ride is completed and  get payment amount in cash or credit card
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function completedJob(Request $request,$id){
        try{
            Log::info('app.requests', ['request' => $request->all()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();




            App::setLocale("en");
            $jobs_rejected = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_driver_id'=>$driver->id])->whereNotIn('rbs_ride_status', ['Cancelled', 'Completed', 'Rejected'])->update(['rbs_ride_status'=>'Completed','rbs_driving_end_time'=> now()]);

            if($jobs_rejected == 1) {
                $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->first();
               $paymentGateWay = $Passenger->rbs_payment_method;
                $user = User::getuser($Passenger->rbs_passenger_id);

                $getfirebase = FireBase::show($id);
                if(isset($getfirebase['rbs_total_ride_distance_covered'])){
                $destination['distance'] = $getfirebase['rbs_total_ride_distance_covered'];
                $ploy_line = $getfirebase['rbs_polyline'];
                }else{
                    $destination['distance'] = 0;
                    $ploy_line = $Passenger->rbs_polyline;
                }
                $dist_completed =  RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->update(['ride_booking_schedules.rbs_total_ride_distance_covered'=>$getfirebase['rbs_total_ride_distance_covered'],'ride_booking_schedules.rbs_polyline'=>$ploy_line,'ride_booking_schedules.rbs_drop_off_lat'=>$getfirebase['rbs_driver_lat'],'ride_booking_schedules.rbs_drop_off_long'=>$getfirebase['rbs_driver_long']]);
                $to = Carbon::parse($Passenger->rbs_driving_start_time);
                $from = Carbon::parse($Passenger->rbs_driving_end_time);

                $time = $to->diffInMinutes($from);
                $destination['time'] = $time;

                $to = Carbon::parse($Passenger->rbs_driving_wait_start_time);
                $from = Carbon::parse($Passenger->rbs_driving_wait_end_time);

                $wait = $to->diffInMinutes($from);

                $wait_time = BaseAppControl::where('bac_meta_key','driver_initial_wait_time')->first()->bac_meta_value;
                if ($wait > $wait_time){
                    $wait = $wait - $wait_time;
                }else{
                    $wait = $to->diffInMinutes($from);
                }

                $selected_for_estimate_d_t = Utility::timeAndDistance($Passenger->rbs_driver_lat, $Passenger->rbs_driver_long, $Passenger->rbs_source_lat, $Passenger->rbs_source_long);
                $selected_for_estimate_rate_km = $selected_for_estimate_d_t->routes[0]->legs[0]->distance->value/1000;
                $selected_for_estimate_rate_mint = $selected_for_estimate_d_t->routes[0]->legs[0]->duration->value/60;
                $dist_completed =  RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->update(['ride_booking_schedules.rbs_ride_drop_off_distance'=>$destination['distance'],'ride_booking_schedules.rbs_ride_drop_off_time'=>$destination['time'],'ride_booking_schedules.rbs_before_pick_up_minutes'=>$selected_for_estimate_rate_mint,'ride_booking_schedules.rbs_before_pick_up_km'=>$selected_for_estimate_rate_km]);

                $rate = Utility::getRate($Passenger->rbs_passenger_id,$destination,$wait,$Passenger,$id,$selected_for_estimate_rate_km,$selected_for_estimate_rate_mint);
                $rate1 = $rate[0];
                $basefare = $rate[1];
                $finalRate_before = $rate[2];
                $initial_wait_rate = $rate[3];
                $vatPlan = $rate[4];
                $taxPlan = $rate[5];
                $rate_DATA_Email = $rate[6];

               $createInvoice = InvoiceController::createInvoice($rate1,$basefare,$Passenger,$user->TransactionId->last(), $finalRate_before,$initial_wait_rate,$vatPlan,$taxPlan,$rate_DATA_Email);
               $rate = number_format($rate1, 2). " KWD";
                $pay_image = "";
                if ($paymentGateWay == 'wallet'){
                    $pay_image = 'assets/creditCard/Wallet.png';
                }elseif ($paymentGateWay == 'cash'){
                    $pay_image = 'assets/creditCard/Cash.png';
                }elseif ($paymentGateWay == 'creditcard'){
                    $pay_image = 'assets/creditCard/Visa.png';
                }
               if($paymentGateWay == "creditcard") {


                       $successInvoice = CustomerInvoice::where('id',$createInvoice->id)->update(['ci_transaction_status'=>"success"]);
                       $title = "Your Ride is Completed and get payment amount " . $rate . " from your credit card";
                       $body = "Your Ride is Completed and get payment amount " . $rate . " from your credit card";

                       $setfirebase = FireBase::delete($id);
                   $user_data = User::getuser($user->id);
                   $NodeUser = FireBase::storeuser($user->id,$user_data);
                       $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                       $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                       $sound = 'default';
                       $action = 'completedRide';
                       $type = 'default';
                       $drivers_count = 0;
                       $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger,$drivers_count);
                   if($notifications != 1){
                       $notify = 0;
                       BaseAppNotificationIgnored::create([
                           'ani_driver_id' => $driver->id,
                           'ani_ride_id' => $id,
                           'ani_fcm_token_android' => implode("|",$tokensand),
                           'ani_fcm_token_ios' => implode("|",$tokensios)
                       ]);
                   }else{
                       $notify = $notifications;

                   }
                   $noti_data = [
                       'ban_sender_id'=>$Passenger->rbs_driver_id,
                       'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                       'ban_sender_type'=>'Driver',
                       'ban_recipient_type'=>'Passenger',
                       'ban_type_of_notification'=>$type,
                       'ban_title_text'=>$title,
                       'ban_body_text'=>$body,
                       'ban_activity'=>$action,
                       'ban_notifiable_type'=>'App\RideBookingSchedule',
                       'ban_notifiable_id'=>$Passenger->id,
                       'ban_notification_status'=>$notify,
                       'ban_created_at'=>now(),
                       'ban_updated_at'=>now()
                   ];
                   $app_notification = BaseAppNotification::create($noti_data);

                       $user = User::find($Passenger->rbs_passenger_id);
                       Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                       Log::info(json_encode(["response" =>['user'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
                       $user = User::getuser($user->id);
                       $cards = CustomerCreditCard::where('ccc_user_id',$user->id)->get();
                       $cardsjson = GetMyCreditCardsResource::collection($cards);
//                     usleep(2000000);
                     $setfirebase = FireBase::delete($id);
                   $user_data = User::getuser($user->id);
                   $NodeUser = FireBase::storeuser($user->id,$user_data);
                   $driver_data = Driver::getdriverfull($driver->id);
                   $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                   $node = "ride_status";
                   $data = "Completed";
                   $driver1 = FireBase::updateDriver($driver->id,$node,$data);
                       return response()->json(['success'=>true,'ride_id'=>$id,'payment_method_img'=>$pay_image,'user'=>$user,'bill_amount'=>$rate,'invoice_id'=>$createInvoice->id,'date'=>date('d-m-Y',strtotime($createInvoice->ci_created_at)),'time'=>date('H:i',strtotime($createInvoice->ci_created_at)),'credit_card'=>$cardsjson[0],'payment_method'=>$paymentGateWay, 'ride_total_distance'=> number_format($Passenger->rbs_total_ride_distance_covered,2,".",",") ,'ride_total_duration'=> Carbon::parse($Passenger->rbs_driving_start_time)->diffInMinutes($Passenger->rbs_driving_end_time)], 200);





               }if($paymentGateWay == "cash") {


                    App::setLocale($user->locale);
                        $title =  LanguageString::translated()->where('bls_name_key','ride_completed_cash')->first()->name;
                        $body = LanguageString::translated()->where('bls_name_key','ride_completed_cash_desc')->first()->name .' '. $rate;


                        $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    $node = "ride_status";
                    $data = "Completed";
                    $driver1 = FireBase::updateDriver($driver->id,$node,$data);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                        $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android", 'app_type' => 'Passenger'])->pluck('device_token')->toArray();
                        $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS", 'app_type' => 'Passenger'])->pluck('device_token')->toArray();
                        $sound = 'default';
                        $action = 'completedRide';
                        $type = 'default';
                        $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger, $drivers = 1);
                    if($notifications != 1){
                        $notify = 0;
                        BaseAppNotificationIgnored::create([
                            'ani_driver_id' => $driver->id,
                            'ani_ride_id' => $id,
                            'ani_fcm_token_android' => implode("|",$tokensand),
                            'ani_fcm_token_ios' => implode("|",$tokensios)
                        ]);
                    }else{
                        $notify = $notifications;

                    }
                    $noti_data = [
                        'ban_sender_id'=>$Passenger->rbs_driver_id,
                        'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                        'ban_sender_type'=>'Driver',
                        'ban_recipient_type'=>'Passenger',
                        'ban_type_of_notification'=>$type,
                        'ban_title_text'=>$title,
                        'ban_body_text'=>$body,
                        'ban_activity'=>$action,
                        'ban_notifiable_type'=>'App\RideBookingSchedule',
                        'ban_notifiable_id'=>$Passenger->id,
                        'ban_notification_status'=>$notify,
                        'ban_created_at'=>now(),
                        'ban_updated_at'=>now()
                    ];
                    $app_notification = BaseAppNotification::create($noti_data);


                        $user = User::find($Passenger->rbs_passenger_id);
                        Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);

                        Log::info(json_encode(["response" => ['user' => $user], "statusCode" => 200, "URL" => $request->url(), 'trxID' => $user->TransactionId->last()]));
                        $user = User::getuser($user->id);
//                    usleep(2000000);
                    $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    App::setLocale($request->header('Accept-Language'));
                        return response()->json(['success' => true, 'ride_id'=>$id, 'payment_method_img'=>$pay_image,'user' => $user, 'bill_amount' => $rate, 'invoice_id' => $createInvoice->id, 'date' => date('d-m-Y', strtotime($createInvoice->ci_created_at)), 'time' => date('H:i', strtotime($createInvoice->ci_created_at)), 'payment_method' => $paymentGateWay, 'ride_total_distance'=> number_format($Passenger->rbs_total_ride_distance_covered,2,".",",") ,'ride_total_duration'=> Carbon::parse($Passenger->rbs_driving_start_time)->diffInMinutes($Passenger->rbs_driving_end_time)], 200);


                }if($paymentGateWay == "wallet") {

                    $successInvoice = CustomerInvoice::where('id',$createInvoice->id)->update(['ci_transaction_status'=>"success"]);

                    App::setLocale($user->locale);
                        $title =  LanguageString::translated()->where('bls_name_key','ride_completed_wallet')->first()->name;
                        $body = LanguageString::translated()->where('bls_name_key','ride_completed_wallet_desc')->first()->name .' '. $rate. ' '. LanguageString::translated()->where('bls_name_key','ride_completed_wallet_desc2')->first()->name;


                    $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);

                    $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                    $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                    $sound = 'default';
                    $action = 'completedRide';
                    $type = 'default';
                    $drivers_count = 0;
                    $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger,$drivers_count);
                    if($notifications != 1){
                        $notify = 0;
                        BaseAppNotificationIgnored::create([
                            'ani_driver_id' => $driver->id,
                            'ani_ride_id' => $id,
                            'ani_fcm_token_android' => implode("|",$tokensand),
                            'ani_fcm_token_ios' => implode("|",$tokensios)
                        ]);
                    }else{
                        $notify = $notifications;

                    }
                    $noti_data = [
                        'ban_sender_id'=>$Passenger->rbs_driver_id,
                        'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                        'ban_sender_type'=>'Driver',
                        'ban_recipient_type'=>'Passenger',
                        'ban_type_of_notification'=>$type,
                        'ban_title_text'=>$title,
                        'ban_body_text'=>$body,
                        'ban_activity'=>$action,
                        'ban_notifiable_type'=>'App\RideBookingSchedule',
                        'ban_notifiable_id'=>$Passenger->id,
                        'ban_notification_status'=>$notify,
                        'ban_created_at'=>now(),
                        'ban_updated_at'=>now()
                    ];
                    $app_notification = BaseAppNotification::create($noti_data);


                    $user = User::find($Passenger->rbs_passenger_id);
                    Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                    Log::info(json_encode(["response" =>['user'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
                    $user = User::getuser($user->id);
//                    usleep(2000000);
                    $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    App::setLocale($request->header('Accept-Language'));
                    return response()->json(['success'=>true,'user'=>$user,'ride_id'=>$id,'payment_method_img'=>$pay_image,'bill_amount'=>$rate,'invoice_id'=>$createInvoice->id,'date'=>date('d-m-Y',strtotime($createInvoice->ci_created_at)),'time'=>date('H:i',strtotime($createInvoice->ci_created_at)),'payment_method'=>$paymentGateWay, 'ride_total_distance'=> number_format($Passenger->rbs_total_ride_distance_covered,2,".",",") ,'ride_total_duration'=> Carbon::parse($Passenger->rbs_driving_start_time)->diffInMinutes($Passenger->rbs_driving_end_time)], 200);


                }

                    $title = "Your Ride is Completed and payment amount " . $rate . " is fail from your cash";
                    $body = "Your Ride is Completed and  payment amount " . $rate . " is fail from your cash";

                    $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                    $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                    $sound = 'default';
                    $action = 'completedRide';
                    $type = 'default';
                    $drivers_count = 0;
                    $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger,$drivers_count);
                    if($notifications != 1){
                        $notify = 0;
                        BaseAppNotificationIgnored::create([
                            'ani_driver_id' => $driver->id,
                            'ani_ride_id' => $id,
                            'ani_fcm_token_android' => implode("|",$tokensand),
                            'ani_fcm_token_ios' => implode("|",$tokensios)
                        ]);
                    }else{
                        $notify = $notifications;

                    }
                    $noti_data = [
                        'ban_sender_id'=>$Passenger->rbs_driver_id,
                        'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                        'ban_sender_type'=>'Driver',
                        'ban_recipient_type'=>'Passenger',
                        'ban_type_of_notification'=>$type,
                        'ban_title_text'=>$title,
                        'ban_body_text'=>$body,
                        'ban_activity'=>$action,
                        'ban_notifiable_type'=>'App\RideBookingSchedule',
                        'ban_notifiable_id'=>$Passenger->id,
                        'ban_notification_status'=>$notify,
                        'ban_created_at'=>now(),
                        'ban_updated_at'=>now()
                    ];
                    $app_notification = BaseAppNotification::create($noti_data);


                    $user = User::find($Passenger->rbs_passenger_id);
                    Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                    Log::info(json_encode(["response" =>['user'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
                    $user = User::getuser($user->id);
//                    usleep(2000000);
                    $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                App::setLocale($request->header('Accept-Language'));
                    return response()->json(['success'=>true,'user'=>$user,'ride_id'=>$id, 'payment_method_img'=>$pay_image,'bill_amount'=>$rate,'invoice_id'=>$createInvoice->id,'date'=>date('d-m-Y',strtotime($createInvoice->ci_created_at)),'time'=>date('H:i',strtotime($createInvoice->ci_created_at)),'payment_method'=>$paymentGateWay, 'ride_total_distance'=> number_format($Passenger->rbs_total_ride_distance_covered,2,".",",") ,'ride_total_duration'=> Carbon::parse($Passenger->rbs_driving_start_time)->diffInMinutes($Passenger->rbs_driving_end_time)], 200);


            }else{
                $message = LanguageString::translated()->where('bls_name_key','id_does_not_match')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
    public function completedJob1(Request $request,$id){
        try{
            Log::info('app.requests', ['request' => $request->all()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
            App::setLocale("en");
            $jobs_rejected = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_driver_id'=>$driver->id])->update(['rbs_ride_status'=>'Completed','rbs_driving_end_time'=> now()]);

            if($jobs_rejected == 1) {
                $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->first();
               $paymentGateWay = $Passenger->rbs_payment_method;
                $user = User::getuser($Passenger->rbs_passenger_id);

                $getfirebase = FireBase::show($id);
                if(isset($getfirebase['rbs_total_ride_distance_covered'])){
                $destination['distance'] = $getfirebase['rbs_total_ride_distance_covered'];
                }else{
                    $destination['distance'] = 0;
                }
                $dist_completed =  RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->update(['ride_booking_schedules.rbs_total_ride_distance_covered'=>$getfirebase['rbs_total_ride_distance_covered']]);
                $to = Carbon::parse($Passenger->rbs_driving_start_time);
                $from = Carbon::parse($Passenger->rbs_driving_end_time);

                $time = $to->diffInMinutes($from);
                $destination['time'] = $time;

                $to = Carbon::parse($Passenger->rbs_driving_wait_start_time);
                $from = Carbon::parse($Passenger->rbs_driving_wait_end_time);

                $wait = $to->diffInMinutes($from);

                $wait_time = BaseAppControl::where('bac_meta_key','driver_initial_wait_time')->first()->bac_meta_value;
                if ($wait > $wait_time){
                    $wait = $wait - $wait_time;
                }else{
                    $wait = $to->diffInMinutes($from);
                }

                $selected_for_estimate_d_t = Utility::timeAndDistance($Passenger->rbs_driver_lat, $Passenger->rbs_driver_long, $Passenger->rbs_source_lat, $Passenger->rbs_source_long);
                $selected_for_estimate_rate_km = $selected_for_estimate_d_t->routes[0]->legs[0]->distance->value/1000;
                $selected_for_estimate_rate_mint = $selected_for_estimate_d_t->routes[0]->legs[0]->duration->value/60;

                $rate = Utility::getRate($Passenger->rbs_passenger_id,$destination,$wait,$Passenger,$id,$selected_for_estimate_rate_km,$selected_for_estimate_rate_mint);
                $rate1 = $rate[0];
                $basefare = $rate[1];
                $finalRate_before = $rate[2];
                $initial_wait_rate = $rate[3];
                $vatPlan = $rate[4];
                $taxPlan = $rate[5];
                $rate_DATA_Email = $rate[6];

               $createInvoice = InvoiceController::createInvoice($rate1,$basefare,$Passenger,$user->TransactionId->last(), $finalRate_before,$initial_wait_rate,$vatPlan,$taxPlan,$rate_DATA_Email);
               $rate = number_format($rate1, 2). " KWD";
                $pay_image = "";
                if ($paymentGateWay == 'wallet'){
                    $pay_image = 'assets/creditCard/Wallet.png';
                }elseif ($paymentGateWay == 'cash'){
                    $pay_image = 'assets/creditCard/Cash.png';
                }elseif ($paymentGateWay == 'creditcard'){
                    $pay_image = 'assets/creditCard/Visa.png';
                }
               if($paymentGateWay == "creditcard") {


                       $successInvoice = CustomerInvoice::where('id',$createInvoice->id)->update(['ci_transaction_status'=>"success"]);
                       $title = "Your Ride is Completed and get payment amount " . $rate . " from your credit card";
                       $body = "Your Ride is Completed and get payment amount " . $rate . " from your credit card";

                       $setfirebase = FireBase::delete($id);
                   $user_data = User::getuser($user->id);
                   $NodeUser = FireBase::storeuser($user->id,$user_data);
                       $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                       $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                       $sound = 'default';
                       $action = 'completedRide';
                       $type = 'default';
                       $drivers_count = 0;
                       $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger,$drivers_count);
                   if($notifications != 1){
                       $notify = 0;
                       BaseAppNotificationIgnored::create([
                           'ani_driver_id' => $driver->id,
                           'ani_ride_id' => $id,
                           'ani_fcm_token_android' => implode("|",$tokensand),
                           'ani_fcm_token_ios' => implode("|",$tokensios)
                       ]);
                   }else{
                       $notify = $notifications;

                   }
                   $noti_data = [
                       'ban_sender_id'=>$Passenger->rbs_driver_id,
                       'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                       'ban_sender_type'=>'Driver',
                       'ban_recipient_type'=>'Passenger',
                       'ban_type_of_notification'=>$type,
                       'ban_title_text'=>$title,
                       'ban_body_text'=>$body,
                       'ban_activity'=>$action,
                       'ban_notifiable_type'=>'App\RideBookingSchedule',
                       'ban_notifiable_id'=>$Passenger->id,
                       'ban_notification_status'=>$notify,
                       'ban_created_at'=>now(),
                       'ban_updated_at'=>now()
                   ];
                   $app_notification = BaseAppNotification::create($noti_data);

                       $user = User::find($Passenger->rbs_passenger_id);
                       Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                       Log::info(json_encode(["response" =>['user'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
                       $user = User::getuser($user->id);
                       $cards = CustomerCreditCard::where('ccc_user_id',$user->id)->get();
                       $cardsjson = GetMyCreditCardsResource::collection($cards);
//                     usleep(2000000);
                     $setfirebase = FireBase::delete($id);
                   $user_data = User::getuser($user->id);
                   $NodeUser = FireBase::storeuser($user->id,$user_data);
                   $driver_data = Driver::getdriverfull($driver->id);
                   $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                   $node = "ride_status";
                   $data = "Completed";
                   $driver1 = FireBase::updateDriver($driver->id,$node,$data);
                       return response()->json(['success'=>true,'ride_id'=>$id,'payment_method_img'=>$pay_image,'user'=>$user,'bill_amount'=>$rate,'invoice_id'=>$createInvoice->id,'date'=>date('d-m-Y',strtotime($createInvoice->ci_created_at)),'time'=>date('H:i',strtotime($createInvoice->ci_created_at)),'credit_card'=>$cardsjson[0],'payment_method'=>$paymentGateWay, 'ride_total_distance'=> number_format($Passenger->rbs_total_ride_distance_covered,2,".",",") ,'ride_total_duration'=> Carbon::parse($Passenger->rbs_driving_start_time)->diffInMinutes($Passenger->rbs_driving_end_time)], 200);





               }if($paymentGateWay == "cash") {


                    App::setLocale($user->locale);
                        $title =  LanguageString::translated()->where('bls_name_key','ride_completed_cash')->first()->name;
                        $body = LanguageString::translated()->where('bls_name_key','ride_completed_cash_desc')->first()->name .' '. $rate;


                        $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    $node = "ride_status";
                    $data = "Completed";
                    $driver1 = FireBase::updateDriver($driver->id,$node,$data);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                        $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android", 'app_type' => 'Passenger'])->pluck('device_token')->toArray();
                        $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS", 'app_type' => 'Passenger'])->pluck('device_token')->toArray();
                        $sound = 'default';
                        $action = 'completedRide';
                        $type = 'default';
                        $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger, $drivers = 1);
                    if($notifications != 1){
                        $notify = 0;
                        BaseAppNotificationIgnored::create([
                            'ani_driver_id' => $driver->id,
                            'ani_ride_id' => $id,
                            'ani_fcm_token_android' => implode("|",$tokensand),
                            'ani_fcm_token_ios' => implode("|",$tokensios)
                        ]);
                    }else{
                        $notify = $notifications;

                    }
                    $noti_data = [
                        'ban_sender_id'=>$Passenger->rbs_driver_id,
                        'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                        'ban_sender_type'=>'Driver',
                        'ban_recipient_type'=>'Passenger',
                        'ban_type_of_notification'=>$type,
                        'ban_title_text'=>$title,
                        'ban_body_text'=>$body,
                        'ban_activity'=>$action,
                        'ban_notifiable_type'=>'App\RideBookingSchedule',
                        'ban_notifiable_id'=>$Passenger->id,
                        'ban_notification_status'=>$notify,
                        'ban_created_at'=>now(),
                        'ban_updated_at'=>now()
                    ];
                    $app_notification = BaseAppNotification::create($noti_data);


                        $user = User::find($Passenger->rbs_passenger_id);
                        Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);

                        Log::info(json_encode(["response" => ['user' => $user], "statusCode" => 200, "URL" => $request->url(), 'trxID' => $user->TransactionId->last()]));
                        $user = User::getuser($user->id);
//                    usleep(2000000);
                    $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    App::setLocale($request->header('Accept-Language'));
                        return response()->json(['success' => true, 'ride_id'=>$id, 'payment_method_img'=>$pay_image,'user' => $user, 'bill_amount' => $rate, 'invoice_id' => $createInvoice->id, 'date' => date('d-m-Y', strtotime($createInvoice->ci_created_at)), 'time' => date('H:i', strtotime($createInvoice->ci_created_at)), 'payment_method' => $paymentGateWay, 'ride_total_distance'=> number_format($Passenger->rbs_total_ride_distance_covered,2,".",",") ,'ride_total_duration'=> Carbon::parse($Passenger->rbs_driving_start_time)->diffInMinutes($Passenger->rbs_driving_end_time)], 200);


                }if($paymentGateWay == "wallet") {

                    $successInvoice = CustomerInvoice::where('id',$createInvoice->id)->update(['ci_transaction_status'=>"success"]);

                    App::setLocale($user->locale);
                        $title =  LanguageString::translated()->where('bls_name_key','ride_completed_wallet')->first()->name;
                        $body = LanguageString::translated()->where('bls_name_key','ride_completed_wallet_desc')->first()->name .' '. $rate. ' '. LanguageString::translated()->where('bls_name_key','ride_completed_wallet_desc2')->first()->name;


                    $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);

                    $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                    $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                    $sound = 'default';
                    $action = 'completedRide';
                    $type = 'default';
                    $drivers_count = 0;
                    $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger,$drivers_count);
                    if($notifications != 1){
                        $notify = 0;
                        BaseAppNotificationIgnored::create([
                            'ani_driver_id' => $driver->id,
                            'ani_ride_id' => $id,
                            'ani_fcm_token_android' => implode("|",$tokensand),
                            'ani_fcm_token_ios' => implode("|",$tokensios)
                        ]);
                    }else{
                        $notify = $notifications;

                    }
                    $noti_data = [
                        'ban_sender_id'=>$Passenger->rbs_driver_id,
                        'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                        'ban_sender_type'=>'Driver',
                        'ban_recipient_type'=>'Passenger',
                        'ban_type_of_notification'=>$type,
                        'ban_title_text'=>$title,
                        'ban_body_text'=>$body,
                        'ban_activity'=>$action,
                        'ban_notifiable_type'=>'App\RideBookingSchedule',
                        'ban_notifiable_id'=>$Passenger->id,
                        'ban_notification_status'=>$notify,
                        'ban_created_at'=>now(),
                        'ban_updated_at'=>now()
                    ];
                    $app_notification = BaseAppNotification::create($noti_data);


                    $user = User::find($Passenger->rbs_passenger_id);
                    Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                    Log::info(json_encode(["response" =>['user'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
                    $user = User::getuser($user->id);
//                    usleep(2000000);
                    $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    App::setLocale($request->header('Accept-Language'));
                    return response()->json(['success'=>true,'user'=>$user,'ride_id'=>$id,'payment_method_img'=>$pay_image,'bill_amount'=>$rate,'invoice_id'=>$createInvoice->id,'date'=>date('d-m-Y',strtotime($createInvoice->ci_created_at)),'time'=>date('H:i',strtotime($createInvoice->ci_created_at)),'payment_method'=>$paymentGateWay, 'ride_total_distance'=> number_format($Passenger->rbs_total_ride_distance_covered,2,".",",") ,'ride_total_duration'=> Carbon::parse($Passenger->rbs_driving_start_time)->diffInMinutes($Passenger->rbs_driving_end_time)], 200);


                }

                    $title = "Your Ride is Completed and payment amount " . $rate . " is fail from your cash";
                    $body = "Your Ride is Completed and  payment amount " . $rate . " is fail from your cash";

                    $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                    $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                    $sound = 'default';
                    $action = 'completedRide';
                    $type = 'default';
                    $drivers_count = 0;
                    $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger,$drivers_count);
                    if($notifications != 1){
                        $notify = 0;
                        BaseAppNotificationIgnored::create([
                            'ani_driver_id' => $driver->id,
                            'ani_ride_id' => $id,
                            'ani_fcm_token_android' => implode("|",$tokensand),
                            'ani_fcm_token_ios' => implode("|",$tokensios)
                        ]);
                    }else{
                        $notify = $notifications;

                    }
                    $noti_data = [
                        'ban_sender_id'=>$Passenger->rbs_driver_id,
                        'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                        'ban_sender_type'=>'Driver',
                        'ban_recipient_type'=>'Passenger',
                        'ban_type_of_notification'=>$type,
                        'ban_title_text'=>$title,
                        'ban_body_text'=>$body,
                        'ban_activity'=>$action,
                        'ban_notifiable_type'=>'App\RideBookingSchedule',
                        'ban_notifiable_id'=>$Passenger->id,
                        'ban_notification_status'=>$notify,
                        'ban_created_at'=>now(),
                        'ban_updated_at'=>now()
                    ];
                    $app_notification = BaseAppNotification::create($noti_data);


                    $user = User::find($Passenger->rbs_passenger_id);
                    Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                    Log::info(json_encode(["response" =>['user'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
                    $user = User::getuser($user->id);
//                    usleep(2000000);
                    $setfirebase = FireBase::delete($id);
                    $user_data = User::getuser($user->id);
                    $NodeUser = FireBase::storeuser($user->id,$user_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                    $driver_data = Driver::getdriverfull($driver->id);
                    $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                App::setLocale($request->header('Accept-Language'));
                    return response()->json(['success'=>true,'user'=>$user,'ride_id'=>$id, 'payment_method_img'=>$pay_image,'bill_amount'=>$rate,'invoice_id'=>$createInvoice->id,'date'=>date('d-m-Y',strtotime($createInvoice->ci_created_at)),'time'=>date('H:i',strtotime($createInvoice->ci_created_at)),'payment_method'=>$paymentGateWay, 'ride_total_distance'=> number_format($Passenger->rbs_total_ride_distance_covered,2,".",",") ,'ride_total_duration'=> Carbon::parse($Passenger->rbs_driving_start_time)->diffInMinutes($Passenger->rbs_driving_end_time)], 200);


            }else{
                $message = LanguageString::translated()->where('bls_name_key','id_does_not_match')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * completed Job By Passenger based on ride is completed
     * send notification, your ride is completed
     * @param Request $request,$id
     * @return Response
     * @throws Exception
     */

    public function completedJobByPassenger(Request $request,$id){

            Log::info('app.requests', ['request' => $request->all()]);
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);



        $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->first();

            if($Passenger) {
                 $user = User::getuser($Passenger->rbs_passenger_id);

                $driver = Driver::getdriver($Passenger->rbs_driver_id);
                $getfirebase = FireBase::updateNode($id,'rbs_ride_status','Completed');
                $title = "Your Ride is Completed ";
                $body = "Your Ride is Completed ";


                $tokensand = Device::where(['user_id' => $Passenger->rbs_driver_id, 'device_type' => "Android",'app_type'=>'Driver'])->pluck('device_token')->toArray();
                $tokensios = Device::where(['user_id' => $Passenger->rbs_driver_id, 'device_type' => "iOS",'app_type'=>'Driver'])->pluck('device_token')->toArray();
                $sound = 'default';
                $action = 'completedRideByPassenger';
                $type = 'default';
                $drivers_count = 0;
                $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger,$drivers_count);
                if($notifications != 1){
                    $notify = 0;
                    BaseAppNotificationIgnored::create([
                        'ani_driver_id' => $driver->id,
                        'ani_ride_id' => $id,
                        'ani_fcm_token_android' => implode("|",$tokensand),
                        'ani_fcm_token_ios' => implode("|",$tokensios)
                    ]);
                }else{
                    $notify = $notifications;

                }
                $noti_data = [
                    'ban_sender_id'=>$Passenger->rbs_passenger_id,
                    'ban_recipient_id'=>$Passenger->rbs_driver_id,
                    'ban_sender_type'=>'Driver',
                    'ban_recipient_type'=>'Passenger',
                    'ban_type_of_notification'=>$type,
                    'ban_title_text'=>$title,
                    'ban_body_text'=>$body,
                    'ban_activity'=>$action,
                    'ban_notifiable_type'=>'App\RideBookingSchedule',
                    'ban_notifiable_id'=>$Passenger->id,
                    'ban_notification_status'=>$notify,
                    'ban_created_at'=>now(),
                    'ban_updated_at'=>now()
                ];
                $app_notification = BaseAppNotification::create($noti_data);


                $user = User::find($Passenger->rbs_passenger_id);
                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                Log::info(json_encode(["response" =>['user'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
                $user = User::getuser($user->id);
                return response()->json(['success'=>true,'user'=>$user,'ride_id'=>$id, 'payment_method'=>$Passenger->rbs_payment_method], 200);



            }else{
                $message = LanguageString::translated()->where('bls_name_key','id_does_not_match')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }

    }

  /**
     * driver start Driving
     * send notification
     * @param Request $request,$id
     * @return Response
     * @throws Exception
     */

    public function startDriving(Request $request,$id){
        try{

            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
            $jobs_rejected = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_driver_id'=>$driver->id])->update(['rbs_ride_status'=>'Driving','rbs_driving_start_time'=> now(),'rbs_driving_wait_end_time'=> now()]);

            if($jobs_rejected == 1) {
                $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->first();
                $driver = Driver::find($Passenger->rbs_driver_id);
                $BookAS['bearing'] = -1;
                $setfirebase = FireBase::store($Passenger->id,$Passenger);
                $user = User::find($Passenger->rbs_passenger_id);
                $user_data = User::getuser($user->id);
                $NodeUser = FireBase::storeuser($user->id,$user_data);

                $driver_data = Driver::getdriverfull($driver->id);
                $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                $node = "ride_status";
                $data = "Driving";
                $driver1 = FireBase::updateDriver($driver->id,$node,$data);
                $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                $user = User::find($Passenger->rbs_passenger_id);
                App::setLocale($user->locale);
                $title =  $driver->du_full_name." " .LanguageString::translated()->where('bls_name_key','driver_started_driving')->first()->name;
                $body = $driver->du_full_name." " .LanguageString::translated()->where('bls_name_key','driver_started_driving')->first()->name;
                App::setLocale($request->header('Accept-Language'));
                $sound = 'default';
                $action = 'startDriving';
                $type = 'default';
                $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger,$drivers =1);

                if($notifications != 1){
                    $notify = 0;
                    BaseAppNotificationIgnored::create([
                        'ani_driver_id' => $driver->id,
                        'ani_ride_id' => $id,
                        'ani_fcm_token_android' => implode("|",$tokensand),
                        'ani_fcm_token_ios' => implode("|",$tokensios)
                    ]);
                }else{
                    $notify = $notifications;
                }

                $noti_data = [
                    'ban_sender_id'=>$Passenger->rbs_driver_id,
                    'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                    'ban_sender_type'=>'Driver',
                    'ban_recipient_type'=>'Passenger',
                    'ban_type_of_notification'=>$type,
                    'ban_title_text'=>$title,
                    'ban_body_text'=>$body,
                    'ban_activity'=>$action,
                    'ban_notifiable_type'=>'App\RideBookingSchedule',
                    'ban_notifiable_id'=>$Passenger->id,
                    'ban_notification_status'=>$notify,
                    'ban_created_at'=>now(),
                    'ban_updated_at'=>now()
                ];
                $app_notification = BaseAppNotification::create($noti_data);
                $user = User::find($Passenger->rbs_passenger_id);
                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                if ($Passenger != null) {
                    $user = User::getuser($Passenger->rbs_passenger_id);
                } else {
                    $user = [];
                }
                Log::info(json_encode(["response" =>['user'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));

                return response()->json($user, 200);
            }else{
                $message = LanguageString::translated()->where('bls_name_key','id_does_not_match')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get My Ride Driver list
     * @param Request $request,$id
     * @return Response
     * @throws Exception
     */

    public function getMyRideDriver(Request  $request, $id){

        try{
            Log::info('app.requests', ['request' => $request->all()]);
            $token=JWTAuth::getToken();
            $user = \Auth::user();
            $driver = Driver::getdriver($id);
            if ($driver != null){

            }else{
                $driver  = (object)[];
            }
            return response()->json($driver,200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * driver is Waiting to ride
     * send notification to passenger for waite driver
     * @param Request $request,$id
     * @return Response
     * @throws Exception
     */


    public function driverIsWaiting(Request $request,$id){

        try{
            Log::info('app.requests', ['request' => $request->all()]);
            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();
            $jobs_rejected = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_driver_id'=>$driver->id])->update(['rbs_ride_status'=>'Waiting','rbs_driving_wait_start_time'=> now()]);

            if($jobs_rejected == 1) {
                $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->first();

                $driver = Driver::find($Passenger->rbs_driver_id);
                $BookAS['bearing'] = -1;
                $setfirebase = FireBase::store($Passenger->id,$Passenger);
                $user = User::find($Passenger->rbs_passenger_id);
                $user_data = User::getuser($user->id);
                $NodeUser = FireBase::storeuser($user->id,$user_data);

                $driver_data = Driver::getdriverfull($driver->id);
                $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                $node = "ride_status";
                $data = "Waiting";
                $driver1 = FireBase::updateDriver($driver->id,$node,$data);


                $tokensand = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "Android",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                $tokensios = Device::where(['user_id' => $Passenger->rbs_passenger_id, 'device_type' => "iOS",'app_type'=>'Passenger'])->pluck('device_token')->toArray();
                $user = User::find($Passenger->rbs_passenger_id);
                App::setLocale($user->locale);
                $title =  LanguageString::translated()->where('bls_name_key','driver_waiting_on_location')->first()->name;
                $body = LanguageString::translated()->where('bls_name_key','driver_waiting_on_location')->first()->name;
                App::setLocale($request->header('Accept-Language'));
                $sound = 'default';
                $action = 'WaitingRide';
                $type = 'pushNotification';
                $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $Passenger->rbs_passenger_id, $Passenger,$drivers =1);

                if($notifications != 1){
                    $notify = 0;
                    BaseAppNotificationIgnored::create([
                        'ani_driver_id' => $driver->id,
                        'ani_ride_id' => $id,
                        'ani_fcm_token_android' => implode("|",$tokensand),
                        'ani_fcm_token_ios' => implode("|",$tokensios)
                    ]);
                }else{
                    $notify = $notifications;

                }
                $noti_data = [
                    'ban_sender_id'=>$Passenger->rbs_driver_id,
                    'ban_recipient_id'=>$Passenger->rbs_passenger_id,
                    'ban_sender_type'=>'Driver',
                    'ban_recipient_type'=>'Passenger',
                    'ban_type_of_notification'=>$type,
                    'ban_title_text'=>$title,
                    'ban_body_text'=>$body,
                    'ban_activity'=>$action,
                    'ban_notifiable_type'=>'App\RideBookingSchedule',
                    'ban_notifiable_id'=>$Passenger->id,
                    'ban_notification_status'=>$notify,
                    'ban_created_at'=>now(),
                    'ban_updated_at'=>now()
                ];
                $app_notification = BaseAppNotification::create($noti_data);
                $user = User::find($Passenger->rbs_passenger_id);
                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                if ($Passenger != null) {
                    $user = User::getuser($Passenger->rbs_passenger_id);
                } else {
                    $user = (object)array();
                }
                Log::info(json_encode(["response" =>['driver'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));

                return response()->json($user, 200);
            }else{
                $message = LanguageString::translated()->where('bls_name_key','id_does_not_match')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return   response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get list of Cancel Reasoning ride by passenger
     * @param Request $request
     * @return Response
     * @throws Exception
     */


    public function getCancelReasoningPassenger(Request $request){

        try{

        $Reasoning = GetCancelReasoningResourcePassenger::collection(AppReference::translated()->where(['bar_status'=>1,'bar_mod_id_ref'=>6,'bar_ref_type_id'=>9])->get());

        return response()->json($Reasoning, 200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get list of Cancel Reasoning ride by Driver
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getCancelReasoningDriver(Request $request){
        try{

        $Reasoning = GetCancelReasoningResourceDriver::collection(AppReference::translated()->where(['bar_status'=>1,'bar_mod_id_ref'=>6,'bar_ref_type_id'=>8])->get());

        return response()->json($Reasoning, 200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Cancel  ride by passenger
     * Create Passenger Cancel Ride History
     * send notification to driver canceled ride by passenger
     * @param Request $request,$id
     * @return Response
     * @throws Exception
     */

    public function CancelRideByPassenger(Request $request,$id){

        try{

            $token=JWTAuth::getToken();
            $user = \Auth::user();
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);


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
            ];
            // param validation
            $validator = Validator::make($request->all(), [
                'reason_id' => 'required',


            ], $messages);
            // validator is fail then return false
            if ($validator->fails()) {

                $errors = [];
                foreach ($validator->errors()->messages() as $field => $message) {
                    Log::error('app.validationError', ['field' => $field,'message'=>$message,'errorCode'=>401,'URL'=>$request->url(),'passenger' => $user,'token'=>$token]);
                    $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                    $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                    $errors[] = [
                        'field' => $field,
                        'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                    ];
                }
                return response()->json(compact('errors'), 401);
            }



            $jobs_rejected = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_passenger_id'=>$user->id])->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Accepted','Driving','Waiting'])->first();







            if(isset($jobs_rejected) && $jobs_rejected != null) {
               $data_reject = [
                   'pcrh_job_id' => $id,
                   'pcrh_passenger_id' => $user->id,
                   'pcrh_driver_id' => $jobs_rejected->rbs_driver_id,
                   'pcrh_reason_id' => $request->reason_id,
                   'pcrh_comments' => $request->comments,
                   'pcrh_created_at' => now()
               ];
               $driver = Driver::getdriver($jobs_rejected->rbs_driver_id);

               $cancelledRide = PassengerCancelRideHistory::create($data_reject);
               $jobs = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->whereIn('rbs_ride_status', ['Requested', 'Accepted', 'Driving', 'Waiting'])->update(['rbs_ride_status' => 'Cancelled', 'rbs_driving_end_time' => now()]);
                $jobs_rejected = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_passenger_id'=>$user->id])->first();

                $tokensand = Device::where(['user_id' => $driver->id, 'device_type' => "Android",'app_type'=>'Driver'])->pluck('device_token')->toArray();
                $tokensios = Device::where(['user_id' => $driver->id, 'device_type' => "iOS",'app_type'=>'Driver'])->pluck('device_token')->toArray();

                App::setLocale($user->locale);
                    $title =  $user->name.' '.LanguageString::translated()->where('bls_name_key','passenger_cancelled_ride')->first()->name;
                    $body = $user->name.' '.LanguageString::translated()->where('bls_name_key','passenger_cancelled_ride')->first()->name;
                App::setLocale($request->header('Accept-Language'));

                $sound = 'default';
                $action = 'CancelRide';
                $type = 'pushNotification';
                $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $user->id, $jobs_rejected,$drivers =1);

                if($notifications != 1){
                    $notify = 0;
                    BaseAppNotificationIgnored::create([
                        'ani_driver_id' => $driver->id,
                        'ani_ride_id' => $id,
                        'ani_fcm_token_android' => implode("|",$tokensand),
                        'ani_fcm_token_ios' => implode("|",$tokensios)
                    ]);
                }else{
                    $notify = $notifications;

                }
                $noti_data = [
                    'ban_sender_id'=>$jobs_rejected->rbs_passenger_id,
                    'ban_recipient_id'=>$jobs_rejected->rbs_driver_id,
                    'ban_sender_type'=>'Passenger',
                    'ban_recipient_type'=>'Driver',
                    'ban_type_of_notification'=>$type,
                    'ban_title_text'=>$title,
                    'ban_body_text'=>$body,
                    'ban_activity'=>$action,
                    'ban_notifiable_type'=>'App\RideBookingSchedule',
                    'ban_notifiable_id'=>$jobs_rejected->id,
                    'ban_notification_status'=>$notify,
                    'ban_created_at'=>now(),
                    'ban_updated_at'=>now()
                ];
                $app_notification = BaseAppNotification::create($noti_data);

                $to = Carbon::parse($jobs_rejected->rbs_driving_start_time);
                $from = Carbon::parse($jobs_rejected->rbs_driving_end_time);

                $time = $to->diffInMinutes($from);

                $cancelation_time = BaseAppControl::where('bac_meta_key','cancelation_time')->first()->bac_meta_value;

                if ($time >= $cancelation_time) {

                    $time = $time - $cancelation_time;
                    $rate = Utility::getRateCancel($jobs_rejected->rbs_passenger_id, $time, $jobs_rejected, $id);
                    $rate1 = $rate[0];
                    $basefare = $rate[1];
                    $finalRate_before = $rate[2];
                    $initial_wait_rate = $rate[3];
                    $vatPlan = $rate[4];
                    $taxPlan = $rate[5];

                    $createInvoice = InvoiceController::createInvoiceCancel($rate1, $basefare, $jobs_rejected, $user->TransactionId->last(), $finalRate_before, $initial_wait_rate, $vatPlan, $taxPlan);


                }

                $message = LanguageString::translated()->where('bls_name_key','you_cancel_the_ride')->first()->name;
                $deleteRide = FireBase::delete($id);
                $user_data = User::getuser($user->id);
                $NodeUser = FireBase::storeuser($user->id,$user_data);
                $driver_data = Driver::getdriverfull($driver->id);
                $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                $node = "ride_status";
                $data = "Cancelled";
                $driver1 = FireBase::updateDriver($driver->id,$node,$data);
           }else{
                $message = LanguageString::translated()->where('bls_name_key','id_does_not_match')->first()->name;
            }
            Log::info(json_encode(["response" =>['driver'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
            return response()->json(['message'=>$message], 200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
    public function CancelRideByPassengerBeforeRide(Request $request,$id){
        try{

            $token=JWTAuth::getToken();
            $user = \Auth::user();
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

            $jobs_rejected = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_passenger_id'=>$user->id])->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Accepted','Driving','Waiting'])->first();

            if(isset($jobs_rejected) && $jobs_rejected != null) {
               $data_reject = [
                   'pcrh_job_id' => $id,
                   'pcrh_passenger_id' => $user->id,
                   'pcrh_driver_id' => $jobs_rejected->rbs_driver_id,
                   'pcrh_reason_id' => 75,
                   'pcrh_comments' => "",
                   'pcrh_created_at' => now()
               ];
               $driver = Driver::getdriver($jobs_rejected->rbs_driver_id);

//               $cancelledRide = PassengerCancelRideHistory::create($data_reject);
               $jobs = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_passenger_id' => $user->id])->whereIn('rbs_ride_status', ['Requested', 'Accepted', 'Driving', 'Waiting'])->update(['rbs_ride_status' => 'Cancelled', 'rbs_driving_end_time' => now()]);
                $jobs_rejected = RideBookingSchedule::where(['ride_booking_schedules.id'=>$id,'ride_booking_schedules.rbs_passenger_id'=>$user->id])->first();

                $tokensand = Device::where(['user_id' => $driver->id, 'device_type' => "Android",'app_type'=>'Driver'])->pluck('device_token')->toArray();
                $tokensios = Device::where(['user_id' => $driver->id, 'device_type' => "iOS",'app_type'=>'Driver'])->pluck('device_token')->toArray();

                App::setLocale($user->locale);
                    $title =  $user->name.' '.LanguageString::translated()->where('bls_name_key','passenger_cancelled_ride')->first()->name;
                    $body = $user->name.' '.LanguageString::translated()->where('bls_name_key','passenger_cancelled_ride')->first()->name;
                App::setLocale($request->header('Accept-Language'));

                $sound = 'default';
                $action = 'CancelRide';
                $type = 'pushNotification';
                $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $user->id, $jobs_rejected,$drivers =1);

                if($notifications != 1){
                    $notify = 0;
                    BaseAppNotificationIgnored::create([
                        'ani_driver_id' => $driver->id,
                        'ani_ride_id' => $id,
                        'ani_fcm_token_android' => implode("|",$tokensand),
                        'ani_fcm_token_ios' => implode("|",$tokensios)
                    ]);
                }else{
                    $notify = $notifications;

                }
                $noti_data = [
                    'ban_sender_id'=>$jobs_rejected->rbs_passenger_id,
                    'ban_recipient_id'=>$jobs_rejected->rbs_driver_id,
                    'ban_sender_type'=>'Passenger',
                    'ban_recipient_type'=>'Driver',
                    'ban_type_of_notification'=>$type,
                    'ban_title_text'=>$title,
                    'ban_body_text'=>$body,
                    'ban_activity'=>$action,
                    'ban_notifiable_type'=>'App\RideBookingSchedule',
                    'ban_notifiable_id'=>$jobs_rejected->id,
                    'ban_notification_status'=>$notify,
                    'ban_created_at'=>now(),
                    'ban_updated_at'=>now()
                ];
                $app_notification = BaseAppNotification::create($noti_data);

//                $to = Carbon::parse($jobs_rejected->rbs_driving_start_time);
//                $from = Carbon::parse($jobs_rejected->rbs_driving_end_time);
//
//                $time = $to->diffInMinutes($from);
//
//                $cancelation_time = BaseAppControl::where('bac_meta_key','cancelation_time')->first()->bac_meta_value;
//
//                if ($time >= $cancelation_time) {
//
//                    $time = $time - $cancelation_time;
//                    $rate = Utility::getRateCancel($jobs_rejected->rbs_passenger_id, $time, $jobs_rejected, $id);
//                    $rate1 = $rate[0];
//                    $basefare = $rate[1];
//                    $finalRate_before = $rate[2];
//                    $initial_wait_rate = $rate[3];
//                    $vatPlan = $rate[4];
//                    $taxPlan = $rate[5];
//
//                    $createInvoice = InvoiceController::createInvoiceCancel($rate1, $basefare, $jobs_rejected, $user->TransactionId->last(), $finalRate_before, $initial_wait_rate, $vatPlan, $taxPlan);
//
//
//                }

                $message = LanguageString::translated()->where('bls_name_key','you_cancel_the_ride')->first()->name;
                $deleteRide = FireBase::delete($id);
                $user_data = User::getuser($user->id);
                $NodeUser = FireBase::storeuser($user->id,$user_data);
                $driver_data = Driver::getdriverfull($driver->id);
                $NodeUser = FireBase::storedriver($driver->id,$driver_data);
                $node = "ride_status";
                $data = "Cancelled";
                $driver1 = FireBase::updateDriver($driver->id,$node,$data);
           }else{
                $message = LanguageString::translated()->where('bls_name_key','id_does_not_match')->first()->name;
            }
            Log::info(json_encode(["response" =>['driver'=>$user],"statusCode"=>200,"URL"=>$request->url(),'trxID'=>$user->TransactionId->last()]));
            return response()->json(['message'=>$message], 200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get Passenger Booked Rides list based on all status ('Requested', 'Accepted', 'Driving', 'Waiting')
     * send notification
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getPassengerBookedRides(Request $request){
        try{
        $token=JWTAuth::getToken();
        $user = \Auth::user();

         $get_ride = RideBookingSchedule::with('driverRating')->where(['ride_booking_schedules.rbs_passenger_id' => $user->id]);
         if(isset($request->searchable_value) && $request->searchable_value == 'completed'){

             $get_ride->whereNotIn('rbs_ride_status', ['Requested', 'Accepted', 'Driving', 'Waiting']);
         }
         elseif(isset($request->searchable_value) && $request->searchable_value == 'in_progress'){

             $get_ride->whereNotIn('rbs_ride_status', ['Cancelled','Rejected', 'Completed']);
         }else{

             $get_ride->whereIn('rbs_ride_status', ['Requested', 'Accepted', 'Driving', 'Waiting']);
         }

         $get_ride = $get_ride->orderBy('rbs_created_at', 'DESC')->get();
//        $get_ride1 = $get_ride->toArray();
//         $driver = array_column($get_ride1,'rbs_driver_id');
//        $drivers = Driver::whereIn('id',$driver)->get();
//         dd($drivers);

            $rides = GetPassengerBookedRidesResource::collection($get_ride);
            $results = Utility::paginate($rides,$request);
            return response()->json(['rides'=>$results['data'],'pagination_urls'=>$results['pagination_urls']], 200);
        }
        catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get Passenger Booked Ride Details
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getPassengerBookedRideDetail(Request $request){
        try{
        $token=JWTAuth::getToken();
        $user = \Auth::user();

         $get_ride = RideBookingSchedule::with('driverRating')->where(['ride_booking_schedules.id' => $request->rideId,'ride_booking_schedules.rbs_passenger_id' => $user->id])->first();

         $rides = new GetPassengerBookedRideDetailResource($get_ride);
         return response()->json($rides, 200);

        }
        catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get Driver Booked Rides list based on all status  'Requested', 'Accepted', 'Driving', 'Waiting'
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getDriverBookedRides(Request $request){
        try{
            $token=JWTAuth::getToken();
            $user = \Auth::guard('driver')->user();
         $get_ride = RideBookingSchedule::with('passengerRating')->where(['ride_booking_schedules.rbs_driver_id' => $user->id]);

            if(isset($request->searchable_value) && $request->searchable_value == 'completed'){

                $get_ride->whereNotIn('rbs_ride_status', ['Requested', 'Accepted', 'Driving', 'Waiting']);
            }
            elseif(isset($request->searchable_value) && $request->searchable_value == 'in_progress'){

                $get_ride->whereNotIn('rbs_ride_status', ['Cancelled','Rejected', 'Completed']);
            }else{

                $get_ride->whereIn('rbs_ride_status', ['Requested', 'Accepted', 'Driving', 'Waiting']);
            }

            $get_ride = $get_ride->orderBy('rbs_created_at', 'DESC')->get();

            $rides = GetDriverBookedRidesResource::collection($get_ride);
            $results = Utility::paginate($rides,$request);

            return response()->json(['rides'=>$results['data'],'pagination_urls'=>$results['pagination_urls']], 200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * get Ride Updated Address using firebase
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getRideUpdatedAddress(Request $request){
        try{
            $token=JWTAuth::getToken();
            $user = \Auth::guard('driver')->user();

            $getfirebase = FireBase::show($request->id);

            return response()->json($getfirebase, 200);

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
}
