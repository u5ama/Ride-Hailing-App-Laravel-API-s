<?php

namespace App\Http\Resources;

use App\Country;
use App\CustomerInvoice;
use App\Driver;
use App\DriverCurrentLocation;
use App\User;
use App\Utility\Utility;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use JamesMills\LaravelTimezone\Timezone;

class GetDriverBookedRidesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = app('geocoder')->reverse($this->rbs_source_lat,$this->rbs_source_long)->get()->first();

        $des = app('geocoder')->reverse($this->rbs_destination_lat,$this->rbs_destination_long)->get()->first();
        $rating = $this->passengerRating;
        $totalTime = Carbon::parse($this->rbs_driving_start_time)->diffInMinutes($this->rbs_driving_end_time);

        $invoice = CustomerInvoice::where(['ci_ride_id' => $this->id])->first();

        if (isset($invoice) && $invoice != null){
            $rideCost = "KWD " . number_format((float)$invoice->ci_customer_invoice_amount, 2, '.', '');
        }else{
            $rideCost =  "KWD " . number_format((float)$this->rbs_estimated_cost, 2, '.', '');
        }
        if (!empty($rating)){
            $is_rated = true;
            $ride_rating = $this->passengerRating->pr_rating;
        }else{
            $is_rated = false;
            $ride_rating = 0;
        }
        $pay_image = 'assets/creditCard/wallet.png';
        if ($this->rbs_payment_method == 'wallet'){
            $pay_image = 'assets/creditCard/Wallet.png';
        }elseif ($this->rbs_payment_method == 'cash'){
            $pay_image = 'assets/creditCard/Cash.png';
        }elseif ($this->rbs_payment_method == 'creditcard'){
            $pay_image = 'assets/creditCard/Visa.png';
        }
        App::setLocale('en');
        $driverlocation = DriverCurrentLocation::where('dcl_user_id',$this->rbs_driver_id)->first();
        $country = $driverlocation->dcl_country;
        $country = Country::listsTranslations('name')->select('countries.country_code')->where('country_translations.name', $country)->first();

        if(isset($country) && $country != null) {

            //timezone for one ALL co-ordinate
            $timezone = (new \App\Utility\Utility)->get_nearest_timezone($driverlocation->dcl_lat,$driverlocation->dcl_long,$country->country_code);
        }else{
            $pickup_location = app('geocoder')->reverse($driverlocation->dcl_lat,$driverlocation->dcl_long)->get()->first();
            $country = Country::listsTranslations('name')->select('countries.country_code')->where('country_translations.name', $pickup_location->getCountry()->getName())->first();
            $timezone = (new \App\Utility\Utility)->get_nearest_timezone($driverlocation->dcl_lat,$driverlocation->dcl_long,$country->country_code);
        }
    $customer_txdID = CustomerInvoice::where(['ci_driver_id'=>$this->rbs_driver_id,'ci_ride_id'=>$this->id])->first();

        return [
            'user'=> User::getuser($this->rbs_passenger_id),
            'ride_location'=>[
                'from'=> (isset($res) && $res != null) ? $res->getFormattedAddress() : null,
                'from_lat' => $this->rbs_source_lat,
                'from_long' => $this->rbs_source_long,
                'to'=> (isset($des) && $des != null) ? $des->getFormattedAddress() : null,
                'to_lat' => $this->rbs_destination_lat,
                'to_long' => $this->rbs_destination_long,
            ],
            'ride_id'=>$this->id,
            'is_rated'=>$is_rated,
            'ride_rating'=>$ride_rating,
            'trx_id'=>(isset($customer_txdID) && $customer_txdID != null) ? $customer_txdID->ci_Trx_id : null ,
            'ride_status'=>$this->rbs_ride_status,
            'ride_polyline'=>$this->rbs_polyline,
            'ride_total_distance'=>number_format($this->rbs_destination_distance,2,".",","),
            'ride_total_time'=>$totalTime,
            'transport_type'=>$this->rbs_transport_type,
            'estimated_cost'=>  $rideCost,
            'payment_image'=> $pay_image,
            'date'=> Utility::convertTimeToUSERzone($this->rbs_created_at, $timezone, $format = 'Y-m-d H:i:s')
        ];
    }
}
