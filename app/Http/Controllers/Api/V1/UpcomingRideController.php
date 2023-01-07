<?php

namespace App\Http\Controllers\Api\V1;

use App\AppReference;
use App\BaseAppControl;
use App\BaseAppNotification;
use App\Country;
use App\CustomerCreditCard;
use App\CustomerInvoice;
use App\Device;
use App\Driver;
use App\DriverCancelRideHistory;
use App\FarePlanHead;
use App\FireBase\FireBase;
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
use App\Http\Resources\GetUpcomingRidesResource;
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
use App\UpcomingScheduleRides;
use App\User;
use App\Utility\Utility;
use DateTimeZone;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpcomingRideController extends Controller
{
    /**
     *  Create Upcoming Schedule Rides and then display
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function bookUpcomingRide1(Request $request){
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
            'numeric' => 'the_field_should_numeric',
            'gt' => 'the_field_should_greater_than_zero',
        ];
        $validator = Validator::make($request->all(), [
            'passenger' => 'required',
            'destination' => 'required',
            'total_max_rate' => 'required',
            'total_rate' => 'required',
            'transport_type' => 'required',
            'transport_id' => 'required',
            'payment_method' => 'required|numeric|gt:0',
            'destination.lat' => 'required',
            'destination.long' => 'required',
            'destination.distance' => 'required|numeric|gt:0',
            'destination.time' => 'required|numeric|gt:0',
            'passenger.lat' => 'required',
            'passenger.long' => 'required',
            'schedule_start_time' => 'required',
            'schedule_start_date' => 'required',

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

        $source = $request->passenger;
        $destination = $request->destination;
        $latitude = $source['lat'];
        $longitude = $source['long'];
        $schedule_date = $request->schedule_start_date;
        $schedule_date = strtotime($schedule_date);
        $schedule_date = date("Y-m-d", $schedule_date);

        $schedule_time = $request->schedule_start_time;
        $schedule_time = strtotime($schedule_time);
        $schedule_time = date("H:i:s", $schedule_time);

        $before_time = BaseAppControl::where('bac_meta_key','ride_request_time')->first()->bac_meta_value;

        $time = strtotime($schedule_time);
        $time = $time - ($before_time * 60);

        $reminder_date = date("Y-m-d", $time);
        $reminder_time = date("H:i:s", $time);

        $passenger = PassengerCurrentLocation::where('pcl_passenger_id', $user->id)->first();

            $data = [
                'usr_passenger_id' => $user->id,
                'usr_transport_id' => $request->transport_id,
                'usr_transport_type' => $request->transport_type,
                'usr_source_lat' => $latitude,
                'usr_source_long' => $longitude,
                'usr_destination_lat' => $destination['lat'],
                'usr_destination_long' => $destination['long'],
                'usr_destination_distance' => $destination['distance'],
                'usr_destination_time' => $destination['time'],
                'usr_estimated_cost' => $request->total_max_rate,
                'usr_promo_id' => $request->promo_id,
                'usr_payment_method' => $request->payment_method,
                'usr_fare_plan_detail_id' => $passenger->pcl_fare_plan_details_id,
                'usr_ride_status' => 'Pending',
                'usr_schedule_start_time' => $schedule_time,
                'usr_schedule_start_date' => $schedule_date,
                'usr_reminder_date' => $reminder_date,
                'usr_reminder_time' => $reminder_time,
                'usr_created_at' => now(),
            ];


        if(count($data) > 0) {
            $BookAS = UpcomingScheduleRides::create($data);
            $BookAS = UpcomingScheduleRides::where('id',$BookAS->id)->first();

            $upcomingRides = new GetUpcomingRidesResource($BookAS);

            $schedule_time = $BookAS->usr_schedule_start_time;
            $schedule_date = $BookAS->usr_schedule_start_date;

            $message = 'Your ride for '.date("l jS \\of F Y ", strtotime($schedule_date)).'at '.date("h:i:s A", strtotime($schedule_time)).' has been confirmed';
            $bool = true;
            $upcomingRide = $upcomingRides;
        }else{
            $bool = false;
            $upcomingRide = [];
            $message = LanguageString::translated()->where('bls_name_key','your_ride_is_not_scheduled')->first()->name;
        }
            Log::info(json_encode(["response" =>['success' => false,'message'=>$message,'upcomingRide' => $upcomingRide],"statusCode"=>200,"URL"=>$request->url(),"passenger" =>$user]));

            return response()->json(['success' => $bool ,'message'=>$message,'upcomingRide' => $upcomingRide],200);
        }
        catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        Log::error('aap.exception', ["exception" =>$e,"errorCode"=>500,"URL"=>$request->url(),"passenger" =>$user]);
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['errors' => $errors], 500);
        }
    }

    public function bookUpcomingRide(Request $request){
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
            'numeric' => 'the_field_should_numeric',
            'gt' => 'the_field_should_greater_than_zero',
        ];
        $validator = Validator::make($request->all(), [
            'passenger' => 'required',
            'destination' => 'required',
            'total_max_rate' => 'required',
            'total_rate' => 'required',
            'transport_type' => 'required',
            'transport_id' => 'required',
            'payment_method' => 'required|numeric|gt:0',
            'destination.lat' => 'required',
            'destination.long' => 'required',
            'destination.address' => 'required',
            'passenger.address' => 'required',
            'passenger.before_pick_up_minutes' => 'required',
            'passenger.before_pick_up_km' => 'required',
            'ride_total_duration' => 'required',
            'destination.distance' => 'required|numeric|gt:0',
            'destination.time' => 'required|numeric|gt:0',
            'passenger.lat' => 'required',
            'passenger.long' => 'required',
            'schedule_start_time' => 'required',
            'schedule_start_date' => 'required',

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

        $source = $request->passenger;
        $destination = $request->destination;
        $latitude = $source['lat'];
        $longitude = $source['long'];
        $schedule_date = $request->schedule_start_date;

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

        $schedule_date = strtotime($schedule_date);
        $schedule_date = date("Y-m-d", $schedule_date);

        $schedule_time = $request->schedule_start_time;
        $schedule_time = strtotime($schedule_time);
        $schedule_time = date("H:i:s", $schedule_time);

        $before_time = BaseAppControl::where('bac_meta_key','ride_request_time')->first()->bac_meta_value;
        $schedule_time_add = $schedule_date . $schedule_time;
        $time = Carbon::parse($schedule_time_add)->subMinutes($before_time)->format('Y-m-d H:i:s');
//        $time = $time - ($before_time * 60);

        $latitude = $request->passenger['lat'];
        $longitude = $request->passenger['long'];

        $pickup_location = app('geocoder')->reverse($latitude,$longitude)->get()->first();

        App::setLocale('en');
        // get country list
        $country = Country::listsTranslations('name')->select('countries.country_code')->where('country_translations.name', $pickup_location->getCountry()->getName())->first();

        if(isset($country) && $country != null) {

            //timezone for one ALL co-ordinate
            $timezone = (new \App\Utility\Utility)->get_nearest_timezone($latitude, $longitude, $country->country_code);

            // create  passenger current location

            PassengerCurrentLocation::updateOrCreate([
                'pcl_passenger_id' => $user->id,
            ], [
                'pcl_lat' => $latitude,
                'pcl_long' => $longitude,
                'pcl_country' => $pickup_location->getCountry()->getName(),
                'pcl_city' => $pickup_location->getLocality(),
                'pcl_current_date' => now()->setTimezone(new DateTimeZone($timezone)),
                'pcl_current_time' => now()->setTimezone(new DateTimeZone($timezone)),
            ]);
        }

        $time = Utility::convertTimeToUTCzone($time, $timezone, $format = 'Y-m-d H:i:s');

        $reminder_date = Carbon::parse($time)->format('Y-m-d');
        $reminder_time = Carbon::parse($time)->format('H:i:s');

        $passenger = PassengerCurrentLocation::where('pcl_passenger_id', $user->id)->first();

            $data = [
                'usr_passenger_id' => $user->id,
                'usr_transport_id' => $request->transport_id,
                'usr_transport_type' => $request->transport_type,
                'usr_source_lat' => $latitude,
                'usr_source_long' => $longitude,
                'usr_destination_lat' => $destination['lat'],
                'usr_destination_long' => $destination['long'],
                'usr_destination_distance' => $destination['distance'],
                'usr_destination_time' => round($destination['time']),
                'usr_destination_address' => $destination['address'],
                'usr_source_address' => $source['address'],
                'usr_before_pick_up_minutes' => $source['before_pick_up_minutes'],
                'usr_before_pick_up_km' => $source['before_pick_up_km'],
                'usr_ride_total_duration' => $request->ride_total_duration,
                'usr_estimated_cost' => $request->total_max_rate,
                'usr_promo_id' => $request->promo_id,
                'usr_payment_method' => $request->payment_method,
                'usr_fare_plan_detail_id' => $passenger->pcl_fare_plan_details_id,
                'usr_contact_id' => (isset($for_contact_name->id) && $for_contact_name->id != null) ? $for_contact_name->id : null,
                'usr_Trx_id' => $user->TransactionId->last()->trx_ID,
                'usr_ride_status' => 'Pending',
                'usr_schedule_start_time' => $schedule_time,
                'usr_schedule_start_date' => $schedule_date,
                'usr_reminder_date' => $reminder_date,
                'usr_reminder_time' => $reminder_time,
                'usr_created_at' => now(),
            ];


        if(count($data) > 0) {
            $BookAS = UpcomingScheduleRides::create($data);
            $BookAS = UpcomingScheduleRides::where('id',$BookAS->id)->first();

            $upcomingRides = new GetUpcomingRidesResource($BookAS);

            $schedule_time = $BookAS->usr_schedule_start_time;
            $schedule_date = $BookAS->usr_schedule_start_date;

            $message = 'Your ride for '.date("l jS \\of F Y ", strtotime($schedule_date)).'at '.date("h:i:s A", strtotime($schedule_time)).' has been confirmed';
            $bool = true;
            $upcomingRide = $upcomingRides;
        }else{
            $bool = false;
            $upcomingRide = [];
            $message = LanguageString::translated()->where('bls_name_key','your_ride_is_not_scheduled')->first()->name;
        }
            Log::info(json_encode(["response" =>['success' => false,'message'=>$message,'upcomingRide' => $upcomingRide],"statusCode"=>200,"URL"=>$request->url(),"passenger" =>$user]));

            return response()->json(['success' => $bool ,'message'=>$message,'upcomingRide' => $upcomingRide],200);
        }
        catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        Log::error('aap.exception', ["exception" =>$e,"errorCode"=>500,"URL"=>$request->url(),"passenger" =>$user]);
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     *  get Passenger Scheduled Rides list
     * @param Request $request
     * @return Response
     * @throws Exception
     */


    public function getPassengerScheduledRides(Request $request){
        try{

        $token=JWTAuth::getToken();
        $user = \Auth::user();

         $get_ride = UpcomingScheduleRides::where(['upcoming_schedule_rides.usr_passenger_id' => $user->id, 'usr_ride_status' => 'Pending']);

         $get_ride = $get_ride->get();

            $rides = GetUpcomingRidesResource::collection($get_ride);
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
     *  cancel Scheduled Ride
     * @param Request $request,$id
     * @return Response
     * @throws Exception
     */

    public function cancelScheduledRide(Request $request,$id){
        try{
            $token=JWTAuth::getToken();
            $user = \Auth::user();

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
                'cancel_schedule_reasoning_id' => 'required',
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


            $get_ride = UpcomingScheduleRides::where(['upcoming_schedule_rides.usr_passenger_id' => $user->id, 'upcoming_schedule_rides.id'=>$id]);

            $get_ride = $get_ride->update([
                'usr_ride_status' => 'Canceled',
                'usr_cancel_schedule_reasoning_id' => $request->cancel_schedule_reasoning_id,
            ]);

            $message = LanguageString::translated()->where('bls_name_key','you_cancel_the_ride')->first()->name;
            $bool = true;
            return response()->json(['success' => $bool ,'message'=>$message],200);
        }
        catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     *  get Cancel Schedule Reasoning by Passenger
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getCancelScheduleReasoningPassenger(Request $request){

        try{

            $Reasoning = GetCancelReasoningResourcePassenger::collection(AppReference::translated()->where(['bar_status'=>1,'bar_mod_id_ref'=>8,'bar_ref_type_id'=>12])->get());

            return response()->json($Reasoning, 200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

   /**
     *  get Passenger Booked Ride Detail
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

}
