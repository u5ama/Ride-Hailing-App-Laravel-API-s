<?php
namespace App\Utility;

use App\BaseAppControl;
use App\BaseAppNotification;
use App\BaseAppNotificationIgnored;
use App\Country;
use App\CustomerInvoice;
use App\Device;
use App\Driver;
use App\DriverCurrentLocation;
use App\FarePlanHead;
use App\FireBase\FireBase;
use App\GeoFencing;
use App\LanguageString;
use App\Notification\Notification;
use App\PassengerCurrentLocation;
use App\PromoCode;
use App\Redirection\Redirection;
use App\RideIgnoredBy;
use App\TransactionId;
use App\UpcomingScheduleRides;
use App\User;
use Carbon\Carbon;
use App\RideBookingSchedule;
use App\TimeZone;
use DateTimeZone;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use  Ixudra\Curl\Facades\Curl;
use Mockery\Exception;

class Utility
{

    public static function sendSMS($sms_body,$user_number){

        $sms_body = str_replace(' ', '%20', $sms_body);
        $sms_body = str_replace('<#>', '%3C%23%3E', $sms_body);
//        dd('http://62.215.226.164/fccsms.aspx?UID=usrwhipp&p=Whi:125&S=Whipp&G='.$user_number.'&M='.$sms_body.'&L=L');
        $response = \Curl::to('http://62.215.226.164/fccsms.aspx?UID=usrwhipp&p=Whi:125&S=Whipp&G='.$user_number.'&M='.$sms_body.'&L=L')
            ->get();
        return $response ;
    }

    public static function timeAndDistance($lat1,$long1,$lat2,$long2){

        $response = \Curl::to('https://maps.googleapis.com/maps/api/directions/json?origin='.$lat1.','.$long1.'&destination='.$lat2.','.$long2.'&sensor=false&mode=driving&key=AIzaSyBIQvm_uPbD5wDycbUlunGYq2F14wZjwtM')
            ->get();

        return json_decode($response) ;

    }

    public static function paymentGateWay($request,$invoice,$user,$rate,$pc_source_type,$paymentGatWay,$testmode){

        // Send a POST request to: http://www.foo.com/bar with arguments 'foz' = 'baz' using JSON and return as associative array

//        dd($paymentGatWay->pgs_base_url);
//        dd(Crypt::encrypt('AoeO44qZ2q5vXwONk13hu2NRreQ82Ysq7I6Aeis9'));

        $response = \Curl::to($paymentGatWay->pgs_base_url)
            ->withHeader('Authorization: Bearer hWFfEkzkYE1X691J4qmcuZHAoet7Ds7ADhL')
//            ->withHeader('Cookie: XSRF-TOKEN=eyJpdiI6IkFtR1FMbnNsellWTlR1dTdvXC84Yk1nPT0iLCJ2YWx1ZSI6IlcwY001VUpTb2hQUEx5eW93WXZaWHQ0blhMWWZuK0lGeVRRUmNyWW0ycXpNWlYxRTVObVBqbGZycGtDWkNIWjkxYzJmdlhqenBETFBZYjNqbEhUU3lnPT0iLCJtYWMiOiJkN2RmNWIyM2ViNTk2YjBjZTJlZmMwNjU3OTE4MmFhMjVkZjE4NGNlYzVjYWI1YjdhZWEwNjUzMWU1N2EzODk1In0%3D; laravel_session=eyJpdiI6Im5oWG13MUdSSHRCeEtJaENSZjNqbUE9PSIsInZhbHVlIjoiQW9hWnROdVFkT1NHWXIzY0hORlltZllQUkZKUnMzbE1BRjdqVHFpN1k1RnU5U1wvaTZkaVBWNmVLTUw3Rk5vSUtXVXVvdVFxRUFOOHpVdkZkSjh4SVhnPT0iLCJtYWMiOiJhNzEyZjExYzZmNjIxNzRiMTlkMTQxNmFlZDhlYjJlZjQ3NGRlYjE3ZDZhYmEwZmNlMzkyN2EyM2I0OGViNGFmIn0%3D')
            ->withData( array('merchant_id' => $paymentGatWay->pgs_merchant_id,
                'username' => $paymentGatWay->pgs_username,
                'password' => $paymentGatWay->pgs_password,
                'api_key' => $paymentGatWay->pgs_api_key,
                'order_id' => $invoice->id,
                'total_price' => $rate,
                'CurrencyCode ' => 'KWD',
                'success_url' => $paymentGatWay->pgs_success_url,
                'error_url' => $paymentGatWay->pgs_error_url,
                'test_mode' => $testmode,
                'CstFName'=>$user->name,
                'CstEmail'=>$user->email,
                'CstMobile'=>$user->country_code.$user->mobile_no,
                'payment_gateway'=>$pc_source_type,
                'whitelabled' => $paymentGatWay->pgs_whitelabled,
                'ProductTitle'=>"['Ride']",
                'ProductName'=>"['Ride']",
                'ProductPrice'=>"['1']",
                'ProductQty'=>"['1']",
                'Reference'=>'123456789',
                'notifyURL'=>$paymentGatWay->pgs_notifyURL) )
            ->asJson( true )
            ->post();
        return $response;
    }

    public static function getRate($passengerId,$destination,$wait,$vehicles_type_id,$id,$selected_for_estimate_rate_km,$selected_for_estimate_rate_mint)
    {

        $passenger = PassengerCurrentLocation::where('pcl_passenger_id', $passengerId)->first();

        $country = Country::listsTranslations('name')->where('country_translations.name', $passenger->pcl_country)->first();
        $passenger = RideBookingSchedule::where('id', $id)->first();

        $getrates = FarePlanHead::leftjoin('fare_plan_details', 'fare_plan_head.id', '=', 'fare_plan_details.fpd_head_id_ref')->where(['fare_plan_head.fph_status'=>1,'fare_plan_head.fph_country_id'=>$country->id,'fare_plan_details.id' => $passenger->rbs_fare_plan_detail_id])->first();

        $KM_rate = $getrates->fpd_per_km_fare * $destination['distance'];

        $Time_rate = $getrates->fpd_per_minute_fare	 * $destination['time'];

        // New Calculations Updated

        $km_distance = BaseAppControl::where('bac_meta_key','driver_km_b4_customer_pickup')->first()->bac_meta_value;
        $mint_distance = BaseAppControl::where('bac_meta_key','driver_mintue_b4_customer_pickup')->first()->bac_meta_value;
        $dist = 0;
        if ($selected_for_estimate_rate_km > $km_distance){
            $dist = $selected_for_estimate_rate_km - $km_distance;
            $KM_rate_before = $getrates->fdp_per_km_fare_before_pickup * $dist;
        }else{
            $KM_rate_before = 0;
        }
        $time = 0;
        if ($selected_for_estimate_rate_mint > $mint_distance){
            $time = $selected_for_estimate_rate_mint - $mint_distance;
            $Time_rate_before = $getrates->fpd_per_minutes_fare_before_pickup * $time;
        }else{
            $Time_rate_before = 0;
        }


        $finalRate = $getrates->fpd_base_fare + $KM_rate + $Time_rate;


        $finalRate_before = $KM_rate_before + $Time_rate_before;

     //   $TotalRate = $finalRate + $finalRate_before;

        $finalRate_wait = 0 + ($getrates->fpd_wait_cost_per_minute_fare	 * $wait);

        $TotalRate = $finalRate + $finalRate_wait + $finalRate_before;

        if($vehicles_type_id->ban_promo_id != null &&  PromoCode::where('id',$vehicles_type_id->ban_promo_id)->exists()){
            $promo = PromoCode::find($vehicles_type_id->ban_promo_id);
            if($promo->pco_promo_value_type == 'value'){
                $discount = $promo->pco_promo_value;
            }if($promo->pco_promo_value_type == 'percentage'){
                $discount = $TotalRate * $promo->pco_promo_value / 100;
            }
        }else{
            $discount = 0;
        }
        $rateData = [
            'drop_off_distance'=>$destination['distance'],
            'drop_off_time'=>$destination['time'],
            'fare_rate_drop_off_distance'=>$getrates->fpd_per_km_far,
            'fare_rate_drop_off_time'=>$getrates->fpd_per_minute_fare,
            'before_pick_up_total_distance'=>$selected_for_estimate_rate_km,
            'free_before_pick_up_total_distance'=>$km_distance,
            'before_pick_up_distance_charge'=>$dist,
            'before_pick_up_total_time'=>$selected_for_estimate_rate_mint,
            'free_before_pick_up_total_time'=>$mint_distance,
            'before_pick_up_time_charge'=>$time,
            'before_pick_up_total_distance_rate'=>$getrates->fpd_per_minutes_fare_before_pickup,
            'before_pick_up_total_time_rate'=>$getrates->fpd_per_minutes_fare_before_pickup,
            'wait_after_arrived'=>$wait,
            'wait_charges'=>$getrates->fpd_wait_cost_per_minute_fare,
            'destination_final_KM_rate'=>$KM_rate,
            'destination_final_time_rate'=>$Time_rate,
            'destination_base_charges'=>$getrates->fpd_base_fare,
            'destination_total_with_out_pick_up_and_wait'=>$finalRate,
            'destination_total_pick_up'=>$finalRate_before,
            'destination_total_wait'=>$finalRate_wait,
            'total_bill'=>$TotalRate,
            'discount_if_voucher'=>$discount
        ];

        return [$TotalRate - $discount,$getrates->fpd_base_fare,$finalRate_before,$finalRate_wait,$getrates->fph_vat_per,$getrates->fph_tax_per,$rateData];
    }
    public static function getRateCancel($passengerId,$time, $jobs_rejected, $id)
    {

        $passenger = PassengerCurrentLocation::where('pcl_passenger_id', $passengerId)->first();

        $country = Country::listsTranslations('name')->where('country_translations.name', $passenger->pcl_country)->first();
        $passenger = RideBookingSchedule::where('id', $id)->first();

        $getrates = FarePlanHead::leftjoin('fare_plan_details', 'fare_plan_head.id', '=', 'fare_plan_details.fpd_head_id_ref')->where(['fare_plan_head.fph_status'=>1,'fare_plan_head.fph_country_id'=>$country->id,'fare_plan_details.id' => $passenger->rbs_fare_plan_detail_id])->first();

        $rate = $getrates->fpd_cancel_charge + ($getrates->fpd_cancel_minute * $time);


        return [$rate,$getrates->fpd_cancel_charge,0,0,$getrates->fph_vat_per,$getrates->fph_tax_per];
    }

    public static function getRate1($destination,$wait,$vehicles_type_id){

        $passenger = PassengerCurrentLocation::where('pcl_passenger_id', $vehicles_type_id->rbs_passenger_id)->first();

        $getrates = FarePlanHead::leftjoin('fare_plan_details', 'fare_plan_head.id', '=', 'fare_plan_details.fpd_head_id_ref')->where(['fare_plan_head.fph_status'=>1,'fare_plan_details.id' => $passenger->pcl_fare_plan_details_id])->first();

        $KM_rate = $getrates->fpd_base_fare + ($getrates->fpd_per_km_fare * $destination['distance']);

        $Time_rate = $getrates->fpd_base_fare + ($getrates->fpd_per_minute_fare	 * $destination['time']);
        $finalRate_wait = 0 + ($getrates->fpd_wait_cost_per_minute_fare	 * $wait);

        if($KM_rate > $Time_rate){
            $finalRate = $KM_rate;
        }else{
            $finalRate = $Time_rate;
        }
        $TotalRate = $finalRate + $finalRate_wait;
//        $TotalRateMAx = $TotalRate + ($TotalRate*$getrates->fpd_estimate_percentage/100);
//        $vehicles_type['TotalRate'] = $TotalRate;
//        $vehicles_type['TotalRateMax'] = $TotalRateMAx;
        if($vehicles_type_id->ban_promo_id != null &&  PromoCode::where('id',$vehicles_type_id->ban_promo_id)->exists()){
            $promo = PromoCode::find($vehicles_type_id->ban_promo_id);
            if($promo->pco_promo_value_type == 'value'){
                $discount = $promo->pco_promo_value;
            }if($promo->pco_promo_value_type == 'percentage'){
                $discount = $TotalRate * $promo->pco_promo_value / 100;
            }
        }else{
            $discount = 0;
        }
        return $TotalRate - $discount;
    }



   public static function getCCType($cardNumber) {
// Remove non-digits from the number
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

// Validate the length
        $len = strlen($cardNumber);
        if ($len < 12 || $len > 19) {
            return "Invalid credit card number. Length does not match";
        }else{
            switch($cardNumber) {
                case(preg_match ('/^4/', $cardNumber) >= 1):
                    return ['type'=>'Visa','image'=>'assets/creditCard/Visa.png'];
                case(preg_match ('/^5[1-5]/', $cardNumber) >= 1):
                    return ['type'=>'Mastercard','image'=>'assets/creditCard/Master.png'];
                case(preg_match ('/^3[47]/', $cardNumber) >= 1):
                    return ['type'=>'Amex','image'=>'assets/creditCard/Amex.png'];
                case(preg_match ('/^3(?:0[0-5]|[68])/', $cardNumber) >= 1):
                    return ['type'=>'Diners Club','image'=>'assets/creditCard/DinersClub.png'];
                case(preg_match ('/^6(?:011|5)/', $cardNumber) >= 1):
                    return ['type'=>'Discover','image'=>'assets/creditCard/Discover.png'];
                case(preg_match ('/^(?:2131|1800|35\d{3})/', $cardNumber) >= 1):
                    return ['type'=>'JCB','image'=>'assets/creditCard/JCB.png'];
                default:
                    return "Could not determine the credit card type.";

            }
        }
    }

    public static function validateChecksum($number) {

        // Remove non-digits from the number
        $number = preg_replace('/\D/', '', $number);

        // Get the string length and parity
        $number_length = strlen($number);
        $parity = $number_length % 2;

        // Split up the number into single digits and get the total
        $total=0;
        for ($i=0; $i<$number_length; $i++) {
            $digit=$number[$i];

            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit*=2;

                // If the sum is two digits, add them together
                if ($digit > 9) {
                    $digit-=9;
                }
            }

            // Sum up the digits
            $total+=$digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return ($total % 10 == 0) ? TRUE : FALSE;

    }


    public static function create_option($table,$value,$display,$selected="",$where=NULL){
        $options = "";
        $condition = "";
        if($where != NULL){
            $condition .= "WHERE ";
            foreach( $where as $key => $v ){
                $condition.=$key."'".$v."' ";
            }
        }

        $sl = 1;

        $query = DB::select("SELECT $value, $display FROM $table $condition");

        foreach($query as $d){
            $name_obj = json_decode($d->$display);
            if(isset($name_obj->$sl) && !empty($name_obj->$sl)){
                $name_v = $name_obj->$sl;
            }else{
                $name_v = $d->$display;
            }

            if( $selected!="" && $selected == $d->$value ){
                $options.="<option value='".$d->$value."' selected='true'>".ucwords($name_v)."</option>";
            }else{
                $options.="<option value='".$d->$value."'>".ucwords($name_v)."</option>";
            }
        }
        echo $options;
    }

    public static function create_option_where($table,$value,$display,$where,$selected=""){
        $options = "";
        $condition = "";
        if($where != NULL){
            $condition .= "WHERE ";
            $condition.="'".$where."' ";
//            foreach( $where as $key => $v ){
//                $condition.=$key."'".$v."' ";
//            }
        }

        $sl = 1;

        $query = DB::select("SELECT $value, $display FROM $table $condition");

        foreach($query as $d){
            $name_obj = json_decode($d->$display);
            if(isset($name_obj->$sl) && !empty($name_obj->$sl)){
                $name_v = $name_obj->$sl;
            }else{
                $name_v = $d->$display;
            }

            if( $selected!="" && $selected == $d->$value ){
                $options.="<option value='".$d->$value."' selected='true'>".ucwords($name_v)."</option>";
            }else{
                $options.="<option value='".$d->$value."'>".ucwords($name_v)."</option>";
            }
        }
        echo $options;
    }

    public static function has_permission($name,$role_id)
    {
        $permission = DB::table('permissions')
            ->where('permission', $name)
            ->where('role_id', $role_id)
            ->get();
        if ( ! $permission->isEmpty() ) {
            return true;
        }
        return false;
    }

    public static function distance($lat1, $lon1, $lat2, $lon2,$unit) {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;

        if ($unit == "K") {
            return $kilometers;
        } else if ($unit == "N") {
            return $miles;
        } else {
            return $miles;
        }

    }

    public static function create_at_time($date, $lagTime)
    {

        $year_ago = $lagTime["year_ago"];
        $month_ago = $lagTime["month_ago"];
        $day_ago = $lagTime["day_ago"];
        $hour_ago = $lagTime["hour_ago"];
        $minute_ago = $lagTime["minute_ago"];
        $years_ago = $lagTime["years_ago"];
        $months_ago = $lagTime["months_ago"];
        $days_ago = $lagTime["days_ago"];
        $hours_ago = $lagTime["hours_ago"];
        $minutes_ago = $lagTime["minutes_ago"];
        $date = Carbon::parse($date);
        $now = Carbon::now();
        $years = $date->diffInYears($now);
        $months = $date->diffInMonths($now);
        $days = $date->diffInDays($now);
        $hours = $date->diffInHours($now);
        $minutes = $date->diffInMinutes($now);
        switch ($date) {
            case $minutes < 61:
                if ($minutes <= 1) {
                    $times_ago = $minute_ago;
                } else {
                    $times_ago = $minutes_ago;
                }
                $create_at_time = $minutes . ' ' . $times_ago;
                break;
            case $hours < 25:
                if ($hours <= 1) {
                    $times_ago = $hour_ago;
                } else {
                    $times_ago = $hours_ago;
                }
                $create_at_time = $hours . ' ' . $times_ago;
                break;
            case $days < 32:
                if ($days <= 1) {
                    $times_ago = $day_ago;
                } else {
                    $times_ago = $days_ago;
                }
                $create_at_time = $days . ' ' . $times_ago;
                break;
            case $months < 13:
                if ($months <= 1) {
                    $times_ago = $month_ago;
                } else {
                    $times_ago = $months_ago;
                }
                $create_at_time = $months . ' ' . $times_ago;
                break;
            case $months > 12:
                if ($years <= 1) {
                    $times_ago = $year_ago;
                } else {
                    $times_ago = $years_ago;
                }
                $create_at_time = $years . ' ' . $times_ago;
                break;
        }
        return $create_at_time;
    }

    public static function lagTime()
    {

        $minutes_ago = LanguageString::translated()->where('bls_name_key', 'minutes_ago_text')->first()->name;
        $hours_ago = LanguageString::translated()->where('bls_name_key', 'hours_ago_text')->first()->name;
        $days_ago = LanguageString::translated()->where('bls_name_key', 'days_ago_text')->first()->name;
        $months_ago = LanguageString::translated()->where('bls_name_key', 'months_ago_text')->first()->name;
        $years_ago = LanguageString::translated()->where('bls_name_key', 'years_ago_text')->first()->name;
        $minute_ago = LanguageString::translated()->where('bls_name_key', 'minute_ago_text')->first()->name;
        $hour_ago = LanguageString::translated()->where('bls_name_key', 'hour_ago_text')->first()->name;
        $day_ago = LanguageString::translated()->where('bls_name_key', 'day_ago_text')->first()->name;
        $month_ago = LanguageString::translated()->where('bls_name_key', 'month_ago_text')->first()->name;
        $year_ago = LanguageString::translated()->where('bls_name_key', 'year_ago_text')->first()->name;

        return [
            "year_ago" => $year_ago,
            "month_ago" => $month_ago,
            "day_ago" => $day_ago,
            "hour_ago" => $hour_ago,
            "minute_ago" => $minute_ago,
            "years_ago" => $years_ago,
            "months_ago" => $months_ago,
            "days_ago" => $days_ago,
            "hours_ago" => $hours_ago,
            "minutes_ago" => $minutes_ago];

    }

    function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
        $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
            : DateTimeZone::listIdentifiers();

        if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

            $time_zone = '';
            $tz_distance = 0;

            //only one identifier?
            if (count($timezone_ids) == 1) {
                $time_zone = $timezone_ids[0];
            } else {

                foreach($timezone_ids as $timezone_id) {
                    $timezone = new DateTimeZone($timezone_id);
                    $location = $timezone->getLocation();
                    $tz_lat   = $location['latitude'];
                    $tz_long  = $location['longitude'];

                    $theta    = $cur_long - $tz_long;
                    $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
                        + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                    $distance = acos($distance);
                    $distance = abs(rad2deg($distance));
                    // echo '<br />'.$timezone_id.' '.$distance;

                    if (!$time_zone || $tz_distance > $distance) {
                        $time_zone   = $timezone_id;
                        $tz_distance = $distance;
                    }

                }
            }
            return  $time_zone;
        }
        return 'none?';
    }

    public static function paginate($data, $request)
    {
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($data);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $data = $itemCollection->slice((($currentPage * $perPage) - $perPage), $perPage)->all();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($data, count($itemCollection), $perPage);

        // set url path for generted links
        $paginatedItems->setPath($request->url());
        $datatest = $paginatedItems->toArray();
        $data_keys = $datatest['data'];
        $data = [];
        foreach ($data_keys as $notification) {
            $data[] = $notification;
        }
        $pagination_urls = ['current_page' => $datatest['current_page'], 'first_page_url' => $datatest['first_page_url'], 'from' => $datatest['from'], 'last_page' => $datatest['last_page'],
            'last_page_url' => $datatest['last_page_url'], 'next_page_url' => $datatest['next_page_url'], 'path' => $datatest['path'],
            'per_page' => $datatest['per_page'], 'prev_page_url' => $datatest['prev_page_url'], 'to' => $datatest['to'], 'total' => $datatest['total']];

        return ['data' => $data, 'pagination_urls' => $pagination_urls];
    }



    public static function transID($user){

       $transID = TransactionId::all()->last();
       if(!$transID){
           $trX = Carbon::now()->year.Carbon::now()->month.Carbon::now()->day.'00000001';
           $trX = $trX * 1;
           $data_trX = ['trx_user_id'=>$user->id,'trx_user_type'=>"passenger",'trx_ID'=>$trX];
           $newtrX = TransactionId::create($data_trX);
           return 'success';
       }else{

           if(date('Y-m-d',strtotime($transID->trx_created_at)) == date('Y-m-d',strtotime(now()))) {
               $trX = $transID->trx_ID + 1;
               $data_trX = ['trx_user_id'=>$user->id,'trx_user_type'=>"passenger",'trx_ID'=>$trX];
               $newtrX = TransactionId::create($data_trX);
               return 'success';
           }else{

               $trX = Carbon::now()->year.Carbon::now()->month.Carbon::now()->day.'00000001';
               $trX = $trX * 1;
               $data_trX = ['trx_user_id'=>$user->id,'trx_user_type'=>"passenger",'trx_ID'=>$trX];
               $newtrX = TransactionId::create($data_trX);
               return 'success';

           }

       }

    }
    public static function InvoicetransID($user){

       $transID = CustomerInvoice::all()->last();
       if(!$transID){

           $trX = Carbon::now()->year.Carbon::now()->month.Carbon::now()->day.'00000001';
           $trX = $trX * 1;

           return $trX;
       }else{

           if($transID->ci_invoice_id != null && date('Y-m-d',strtotime($transID->ci_created_at)) == date('Y-m-d',strtotime(now()))) {
               $trX = $transID->ci_invoice_id + 1;
               return $trX;
           }else{
               $trX = Carbon::now()->year.Carbon::now()->month.Carbon::now()->day.'00000001';
               $trX = $trX * 1;
               return $trX;
           }

       }

    }
    public static function bookARideNext($id,$driver,$request)
    {

        $Passenger = RideBookingSchedule::where(['ride_booking_schedules.id' => $id, 'ride_booking_schedules.rbs_driver_id' => $driver->id])->first();
        $latitude = $Passenger->rbs_source_lat;
        $longitude = $Passenger->rbs_source_long;
        $ignoreByget = [$driver->id];
        $drivers = Driver::leftjoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftjoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['driver_profiles.dp_transport_type_id_ref' => $Passenger->rbs_transport_id, 'driver_current_locations.dcl_app_active' => 1])->whereNotIn('drivers.id', $ignoreByget)->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( dcl_lat ) ) * cos( radians( dcl_long ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( dcl_lat ) ) ) ) AS distance'))
            ->having('distance', '<', BaseAppControl::where('bac_meta_key', 'driver_search_distance')->first()->bac_meta_value)
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
                        'rbs_payment_method' => $Passenger->rbs_payment_method,
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
//            $tokensand = Device::where(['user_id'=>$BookAS->rbs_driver_id,'device_type'=>"Android"])->pluck('device_token')->toArray();
            $tokensand = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "Android", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
            $tokensios = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "iOS", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
            App::setLocale($driver->locale);
            $title = LanguageString::translated()->where('bls_name_key', 'you_book_the_ride')->first()->name;
            $body = LanguageString::translated()->where('bls_name_key', "you_book_the_ride_of")->first()->name . " " . $driver->du_full_name;
            App::setLocale($request->header('Accept-Language'));
            $sound = 'default';
            $action = 'BookARide';
            $id = $BookAS->id;
            $type = 'silent';
            $total_drivers = count($drivers);
            $notifications = Notification::sendnotifications($tokensios, $tokensand, $title, $body, $sound, $action, $id, $type, $driver->id, $BookAS->rbs_passenger_id, $BookAS, $total_drivers);
            if($notifications != 1){
                $notify = 0;
                BaseAppNotificationIgnored::create([
                    'ani_driver_id' => $driver->id,
                    'ani_ride_id' => $id,
                    'ani_fcm_token_android' => $tokensand,
                    'ani_fcm_token_ios' => $tokensios
                ]);
            }else{
                $notify = $notifications;

            }
            $noti_data = [
                'ban_sender_id' => $BookAS->rbs_passenger_id,
                'ban_recipient_id' => $BookAS->rbs_driver_id,
                'ban_sender_type' => 'Passenger',
                'ban_recipient_type' => 'Driver',
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


            //           dd($notifications);
            $message = 'successfully Rejected';
            $user = User::find($BookAS->rbs_passenger_id);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
            Log::info(json_encode(["response" => ['driver' => $driver, 'message' => $message], "statusCode" => 200, "URL" => $request->url(), 'trxID' => $user->TransactionId->last()]));

            return  ['success'=>true,'driver'=>$driver];
        }else{

            return ['success'=>false,'driver'=>[]];
        }

    }

    public static function BookADriverBySchedule()
    {
        $timezone = date_default_timezone_get();
        $time = strtotime(now());

        $time = date('Y-m-d H:i:s', $time);

        $time = Utility::convertTimeToUTCzone($time, $timezone, $format = 'Y-m-d H:i:s');

        $reminder_date = Carbon::parse($time)->format('Y-m-d');
        $reminder_time = Carbon::parse($time)->format('H:i:s');

        $upcomingJobs = UpcomingScheduleRides::whereDate('usr_reminder_date',$reminder_date)->whereTime('usr_reminder_date','<',$reminder_time)->where(['usr_ride_status'=>'Pending'])->get();
//       dd($upcomingJobs);
        $bool = false;
        $driver = [];
        $message = LanguageString::translated()->where('bls_name_key','driver_is_busy')->first()->name;
        $user = (object)[];
        foreach ($upcomingJobs as $upcomingJob){

            $user = User::find($upcomingJob->usr_passenger_id);

            $data_trX = Utility::transID($user);
            Log::info('app.requests', ['request' => $upcomingJob,'trxID'=>$user->TransactionId->last()]);
            $latitude = $upcomingJob->usr_source_lat;
            $longitude = $upcomingJob->usr_source_long;
            $transport_id = $upcomingJob->usr_transport_id;
            $transport_type = $upcomingJob->usr_transport_type;
            $source_lat = $upcomingJob->usr_source_lat;
            $source_long = $upcomingJob->usr_source_long;
            $destination_distance = $upcomingJob->usr_destination_distance;
            $destination_time = $upcomingJob->usr_destination_time;
            $estimated_cost = $upcomingJob->usr_estimated_cost;
            $fare_plan_detail_id = $upcomingJob->usr_fare_plan_detail_id;
            $payment_method = $upcomingJob->usr_payment_method;
            $usr_destination_address = $upcomingJob->usr_destination_address;
            $usr_source_address = $upcomingJob->usr_source_address;
            $usr_before_pick_up_minutes = $upcomingJob->usr_before_pick_up_minutes;
            $usr_before_pick_up_km = $upcomingJob->usr_before_pick_up_km;
            $usr_ride_total_duration = $upcomingJob->usr_ride_total_duration;
            $reminder_date = $upcomingJob->usr_reminder_date;
            $promo_id = $upcomingJob->usr_promo_id;
            $passenger = PassengerCurrentLocation::where('pcl_passenger_id', $user->id)->first();

            $drivers = Driver::leftjoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftjoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['driver_profiles.dp_transport_type_id_ref'=>$transport_id,'driver_current_locations.dcl_app_active'=>1,'drivers.du_driver_status'=>'driver_status_when_approved'])->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( dcl_lat ) ) * cos( radians( dcl_long ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( dcl_lat ) ) ) ) AS distance'))
                ->having('distance', '<', BaseAppControl::where('bac_meta_key','driver_search_distance')->first()->bac_meta_value)
                ->orderBy('distance')
                ->get();
            $data = [];
            foreach ($drivers as $key=>$item){
                if( RideBookingSchedule::where(['rbs_driver_id'=>$item->dp_user_id])->whereIn('rbs_ride_status', ['Requested','Accepted','Driving','Waiting'])->exists()){
                }else{
                    $data =[
                        'rbs_driver_id'=>$item->dp_user_id,
                        'rbs_passenger_id'=>$user->id,
                        'rbs_driver_lat'=>$item->dcl_lat,
                        'rbs_driver_long'=>$item->dcl_long,
                        'rbs_transport_id'=>$transport_id,
                        'rbs_transport_type'=>$transport_type,
                        'rbs_source_lat'=>$latitude,
                        'rbs_source_long'=>$longitude,
                        'rbs_destination_address' => $usr_destination_address,
                        'rbs_source_address' => $usr_source_address,
                        'rbs_before_pick_up_minutes' => $usr_before_pick_up_minutes,
                        'rbs_before_pick_up_km' => $usr_before_pick_up_km,
                        'rbs_ride_total_duration' => $usr_ride_total_duration,
                        'rbs_destination_lat'=>$source_lat,
                        'rbs_destination_long'=>$source_long,
                        'rbs_destination_distance'=>$destination_distance,
                        'rbs_destination_time'=>$destination_time,
                        'rbs_estimated_cost'=>$estimated_cost,
                        'ban_promo_id'=>$promo_id,
                        'rbs_payment_method'=>$payment_method,
                        'rbs_us_ride_id'=>$upcomingJob->id,
                        'rbs_fare_plan_detail_id'=>$fare_plan_detail_id,
                        'rbs_ride_status'=>1,
                        'rbs_created_at'=>now(),
                    ];
                    break;
                }
            }
            if(count($data) > 0) {
                $BookAS = RideBookingSchedule::create($data);
                $BookAS = RideBookingSchedule::where('id',$BookAS->id)->first();
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
                $driver = Driver::find($BookAS->rbs_driver_id);
//            $tokensand = Device::where(['user_id'=>$BookAS->rbs_driver_id,'device_type'=>"Android"])->pluck('device_token')->toArray();
                $tokensand = Device::where(['user_id'=>$BookAS->rbs_driver_id,'device_type'=>"Android",'app_type'=>'Driver'])->pluck('device_token')->toArray();
                $tokensios = Device::where(['user_id'=>$BookAS->rbs_driver_id,'device_type'=>"iOS",'app_type'=>'Driver'])->pluck('device_token')->toArray();
                App::setLocale($driver->locale);
                $title = LanguageString::translated()->where('bls_name_key','you_book_the_ride')->first()->name;
                $body = LanguageString::translated()->where('bls_name_key',"you_book_the_ride_of")->first()->name." ". $driver->du_full_name;
                App::setLocale('en');
                $sound = 'default';
                $action = 'BookARide';
                $id = $BookAS->id;
                $type = 'silent';
                $total_drivers = count($drivers);
                $notifications = Notification::sendnotifications($tokensios,$tokensand,$title,$body,$sound,$action,$id,$type,$driver->id,$BookAS->rbs_passenger_id,$BookAS,$total_drivers);
                $noti_data = [
                    'ban_sender_id'=>$user->id,
                    'ban_sender_type'=>'Passenger',
                    'ban_recipient_type'=>'Driver',
                    'ban_recipient_id'=>$BookAS->rbs_driver_id,
                    'ban_type_of_notification'=>$type,
                    'ban_title_text'=>$title,
                    'ban_body_text'=>$body,
                    'ban_activity'=>$action,
                    'ban_notifiable_type'=>'App\RideBookingSchedule',
                    'ban_notifiable_id'=>$BookAS->id,
                    'ban_notification_status'=>$notifications,
                    'ban_created_at'=>now(),
                    'ban_updated_at'=>now()
                ];
                $app_notification = BaseAppNotification::create($noti_data);


                $message = LanguageString::translated()->where('bls_name_key','we_are_finding_your_driver')->first()->name;
                $driver = Driver::getdriver($driver->id);
                $bool = true;
                $upcomingJobs = UpcomingScheduleRides::where('id',$upcomingJob->id)->update(['usr_ride_status'=>'Accepted']);

            }else{
                $bool = false;
                $driver = [];
                $message = LanguageString::translated()->where('bls_name_key','driver_is_busy')->first()->name;
            }


        }

        Log::info(json_encode(["response" =>['success' => $bool,'driver'=>$driver,'message'=>$message],"statusCode"=>200,"URL"=>'Cron Job',"passenger" =>$user]));

        return response()->json(['success' => $bool ,'driver'=>$driver,'message'=>$message],200);
    }
    public static function removeRequestedJobExpired()
    {
        $extime = date('Y-m-d H:i:s',strtotime(now()->subMinutes(2)));

        $rides = RideBookingSchedule::where(['rbs_ride_status' => 'Requested'])->where('rbs_driving_start_time','<=',$extime)->get();

        foreach ($rides as $ride) {
            $ridejobud = RideBookingSchedule::where('id',$ride->id)->update(['rbs_ride_status' => 'Rejected','rbs_driving_end_time'=>now()]);

            $max_auto_reject_limit = BaseAppControl::where('bac_meta_key','max_rejected_job_for_aut_reject_on_no_response')->first()->bac_meta_value * 1;
            $driverjobs = RideBookingSchedule::where(['rbs_ride_status' => 'Rejected','rbs_driver_id'=>$ride->rbs_driver_id])->whereDate('rbs_created_at', '=', Carbon::today()->toDateString())->get()->count();
//            dd( $max_auto_reject_limit);
            $BookAS = RideBookingSchedule::where(['ride_booking_schedules.id' => $ride->id])->first();
            if($driverjobs > $max_auto_reject_limit){
               $app_off = DriverCurrentLocation::where(['dcl_user_id'=>$ride->rbs_driver_id])->update(['dcl_app_active'=>0]);


                $user = User::find($BookAS->rbs_passenger_id);
                $driver = Driver::find($BookAS->rbs_driver_id);

                $tokensand = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "Android", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
                $tokensios = Device::where(['user_id' => $BookAS->rbs_driver_id, 'device_type' => "iOS", 'app_type' => 'Driver'])->pluck('device_token')->toArray();
                App::setLocale($driver->locale);
                $title = LanguageString::translated()->where('bls_name_key','off_line')->first()->name;
                $body = LanguageString::translated()->where('bls_name_key',"off_line_desc")->first()->name;
                App::setLocale('en');
                $sound = 'default';
                $action = 'DriversOffLine';
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
//            $driver1 = FireBase::updateDriver($driver->id,$node,$data);
                $message = 'Successfully Rejected';
                $user = User::find($BookAS->rbs_passenger_id);
//            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
                Log::info(json_encode(["response" => ['driver' => $driver, 'message' => $message], "statusCode" => 200, "URL" => "Cron Job", 'trxID' => $user->TransactionId->last()]));






            }



            $user = User::find($BookAS->rbs_passenger_id);
            $driver = Driver::find($BookAS->rbs_driver_id);

            $tokensand = Device::where(['user_id' => $BookAS->rbs_passenger_id, 'device_type' => "Android", 'app_type' => 'Passenger'])->pluck('device_token')->toArray();
            $tokensios = Device::where(['user_id' => $BookAS->rbs_passenger_id, 'device_type' => "iOS", 'app_type' => 'Passenger'])->pluck('device_token')->toArray();
            App::setLocale($user->locale);
            $title = LanguageString::translated()->where('bls_name_key','driver_rejected_ride')->first()->name;
            $body = LanguageString::translated()->where('bls_name_key',"driver_rejected_ride")->first()->name;
            App::setLocale('en');
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
//            $driver1 = FireBase::updateDriver($driver->id,$node,$data);
            $message = 'Successfully Rejected';
            $user = User::find($BookAS->rbs_passenger_id);
//            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
            Log::info(json_encode(["response" => ['driver' => $driver, 'message' => $message], "statusCode" => 200, "URL" => "Cron Job", 'trxID' => $user->TransactionId->last()]));



            $setfirebase = FireBase::delete($ride->id);
            $user_data = User::getuser($ride->rbs_passenger_id);
            $NodeUser = FireBase::storeuser($ride->rbs_passenger_id,$user_data);
            $driver_data = Driver::getdriverfull($ride->rbs_driver_id);
            $NodeUser = FireBase::storedriver($ride->rbs_driver_id,$driver_data);
            $driver_data = Driver::getdriverfull($ride->rbs_driver_id);
            $NodeUser = FireBase::storedriver($ride->rbs_driver_id,$driver_data);
        }

        $getrideTrackings = FireBase::showTracking();
        foreach ($getrideTrackings as $item) {
            if(isset($item['id'])) {
                $ridejob = RideBookingSchedule::where('id', $item['id'])->whereNotIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Accepted', 'Driving', 'Waiting'])->first();
               if(isset($ridejob->id)) {
                   $setfirebase = FireBase::delete($ridejob->id);
                   $user_data = User::getuser($ridejob->rbs_passenger_id);
                   $NodeUser = FireBase::storeuser($ridejob->rbs_passenger_id, $user_data);
                   $driver_data = Driver::getdriverfull($ridejob->rbs_driver_id);
                   $NodeUser = FireBase::storedriver($ridejob->rbs_driver_id, $driver_data);
                   $driver_data = Driver::getdriverfull($ridejob->rbs_driver_id);
                   $NodeUser = FireBase::storedriver($ridejob->rbs_driver_id, $driver_data);
               }
            }
        }

        return response()->json(['success' => true,  'message' => "success"], 200);
    }

    //this function converts string from UTC time zone to current user timezone
   public static function convertTimeToUSERzone($str, $userTimezone, $format = 'Y-m-d H:s:i'){
        if(empty($str)){
            return '';
        }

        $new_str = new \DateTime($str, new DateTimeZone('UTC') );
        $new_str->setTimeZone(new DateTimeZone( $userTimezone ));
        return $new_str->format( $format);
    }

    public static function getUserTimeZone($timezone_id){
         $timezone = TimeZone::where('id',$timezone_id)->first();
         if(!empty($timezone->time_zone)){

            return $timezone->time_zone;
         }else{
            return 'Asia/Kuwait';
         }

    }

    public static function restrictedArea($request){
        $latitude = $request->passenger['lat'];
        $longitude = $request->passenger['long'];
        $geofenceall = [];
        /*$geofenceall = GeoFencing::selectRaw(DB::raw('*, Contains(
          PolyFromText( POLYGON((31.41352261905941 73.07929489677582, -26.187020810321858 28.091354370117188, -26.199805575765794 28.125,-26.181937320958628  28.150405883789062, -26.160676690299308 28.13220977783203, -26.167918065075458 28.10680389404297)) ),
          PointFromText(concat("POINT('.$longitude.', '.$latitude.')"))
   ) as contains'))
            ->having('distance', '<', 1)
            ->orderBy('distance')
            ->get();*/
//        dd($geofenceall);
//       $geofenceall = GeoFencing::where(DB::raw("ST_CONTAINS(ST_GEOMFROMTEXT('POLYGON((geo_fencings.restricted_lat,geo_fencings.restricted_lng))'),POINT('.$longitude.', '.$latitude.')"))
//            ->get();
//
//        dd($geofenceall);
        $geofenceall = GeoFencing::selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( restricted_lat ) ) * cos( radians( restricted_lng ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( restricted_lat ) ) ) ) AS distance'))
            ->having('distance', '<', 1)
            ->orderBy('distance')
            ->get();
        if (isset($geofenceall) && count($geofenceall) > 0) {
               return ['success' => false, 'message' => 'Pick Up area is block'];
           }
        $latitudeD = $request->destination['lat'];
        $longitudeD = $request->destination['long'];
        $geofenceall_d = [];
        $geofenceall_d = GeoFencing::selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitudeD.') ) * cos( radians( restricted_lat ) ) * cos( radians( restricted_lng ) - radians('.$longitudeD.') ) + sin( radians('.$latitudeD.') ) * sin( radians( restricted_lat ) ) ) ) AS distance'))
            ->having('distance', '<', BaseAppControl::where('bac_meta_key','app_block_area_radius')->first()->bac_meta_value)
            ->orderBy('distance')
            ->get();
        if (isset($geofenceall_d) && count($geofenceall_d) > 0) {
               return ['success' => false, 'message' => 'Drop Off area is block'];
           }
//        $fencelats = "";
//        $fencellongs = "";
//        foreach ($geofenceall as $fen){
//
//            $fencelats = $fencelats . $fen->formatted_address.'|';
////            $fencelats = $fencelats . $fen->restricted_lat.',';
////            $fencellongs = $fencellongs . $fen->restricted_lng.',';
//
//
//        }
//        $strippedlat = str_replace(' ', '', $fencelats);
//        $stringlat = rtrim($fencelats, '|');
//        $vertices_x = explode("|",$stringlat);
//        $strippedlat = str_replace(' ', '', $fencelats);
//        $stringlat = rtrim($strippedlat, ',');
//        $vertices_x = explode(",",$stringlat);
//        $strippedlong = str_replace(' ', '', $fencellongs);
//        $stringlong = rtrim($strippedlong, ',');
//        $vertices_y = explode(",",$stringlong);

//        print_r($vertices_y);
//        print_r($vertices_x);
//        die();
//        dd($vertices_y);
//        $vertices_x = array(37.628134, 37.629867, 37.62324, 37.622424);    // x-coordinates of the vertices of the polygon
//        $vertices_y = array(-77.458334,-77.449021,-77.445416,-77.457819); // y-coordinates of the vertices of the polygon
//        $points_polygon = count($vertices_x) - 1;  // number vertices - zero-based array
//        $longitude_x = $request->passenger['long'];  // x-coordinate of the point to test
//        $latitude_y = $request->passenger['lat'];
//        $longitude_x_d = $request->destination['long'];  // x-coordinate of the point to test
//        $latitude_y_d = $request->destination['lat'];    // y-coordinate of the point to test
//        $res = app('geocoder')->reverse($latitude_y,$longitude_x)->get();
//        $res2 = app('geocoder')->reverse($latitude_y_d,$longitude_x_d)->get();
//       foreach ($res as $resitem) {
//           if (in_array($resitem->getFormattedAddress(), $vertices_x)) {
//               return ['success' => false, 'message' => 'Pick Up area is block'];
//           }
//       }
////        dd($res2->getFormattedAddress());
////        dd($vertices_x);
////dd(in_array( $res2->getFormattedAddress(), $vertices_x));
//        foreach ($res2 as $resitem2) {
//            if (in_array($resitem2->getFormattedAddress(), $vertices_x)) {
//                return ['success' => false, 'message' => 'Drop Off area is block'];
//            }
//        }

            return ['success'=>true, 'message'=> 'ride area is available'];
     }

    //this function converts string from UTC time zone to current user timezone
    public static function convertTimeToUTCzone($str, $userTimezone, $format = 'Y-m-d H:i:s'){
        if(empty($str)){
            return '';
        }

        $new_str = new \DateTime($str, new DateTimeZone($userTimezone) );
        $new_str->setTimeZone(new DateTimeZone( 'UTC' ));
        return $new_str->format($format);
    }

}



