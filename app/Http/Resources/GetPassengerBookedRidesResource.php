<?php

namespace App\Http\Resources;

use App\Country;
use App\CustomerInvoice;
use App\Driver;
use App\DriverCurrentLocation;
use App\PassengerCurrentLocation;
use App\TransportType;
use App\Utility\Utility;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use JamesMills\LaravelTimezone\Timezone;

class GetPassengerBookedRidesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = \Auth::user();
       $res = app('geocoder')->reverse($this->rbs_source_lat,$this->rbs_source_long)->get()->first();
       $des = app('geocoder')->reverse($this->rbs_destination_lat,$this->rbs_destination_long)->get()->first();
        $invoice = CustomerInvoice::where(['ci_ride_id' => $this->id])->first();

        if (isset($invoice) && $invoice != null){
            $rideCost = "KWD " . number_format((float)$invoice->ci_customer_invoice_amount, 2, '.', '');
        }else{
            $rideCost =  "KWD " . number_format((float)$this->rbs_estimated_cost, 2, '.', '');
        }

        App::setLocale('en');
        $driverlocation = PassengerCurrentLocation::where('pcl_passenger_id',$this->rbs_passenger_id)->first();
        $country = $driverlocation->pcl_country;
        $country = Country::listsTranslations('name')->select('countries.country_code')->where('country_translations.name', $country)->first();

        if(isset($country) && $country != null) {
            //timezone for one ALL co-ordinate
            $timezone = (new \App\Utility\Utility)->get_nearest_timezone($driverlocation->pcl_lat,$driverlocation->pcl_long,$country->country_code);
        }else{
            $pickup_location = app('geocoder')->reverse($driverlocation->pcl_lat,$driverlocation->pcl_long)->get()->first();
            $country = Country::listsTranslations('name')->select('countries.country_code')->where('country_translations.name', $pickup_location->getCountry()->getName())->first();
            $timezone = (new \App\Utility\Utility)->get_nearest_timezone($driverlocation->pcl_lat,$driverlocation->pcl_long,$country->country_code);
        }

       $rating = $this->driverRating;
       $totalTime = Carbon::parse($this->rbs_driving_start_time)->diffInMinutes($this->rbs_driving_end_time);
        App::setLocale($user->locale);
        $transport_type = TransportType::listsTranslations('name')->where('transport_types.id', $this->rbs_transport_id)->first();
        App::setLocale($request->header('Accept-Language'));

       if (!empty($rating) && $rating !=  null){
           $is_rated = true;
           $ride_rating = $this->driverRating->dr_rating;
       }else{
           $is_rated = false;
           $ride_rating = 0;
       }
        $pay_image = 'assets/creditCard/Wallet.png';
        if ($this->rbs_payment_method == 'Wallet'){
            $pay_image = 'assets/creditCard/Wallet.png';
        }elseif ($this->rbs_payment_method == 'Cash'){
            $pay_image = 'assets/creditCard/Cash.png';
        }elseif ($this->rbs_payment_method == 'creditcard'){
            $pay_image = 'assets/creditCard/Visa.png';
        }

        $driver = Driver::getdriver($this->rbs_driver_id);
        if (!empty($driver) && $driver !== null){
            $driverData = $driver;
        }else{
            $driverData = null;
        }
        $customer_txdID = CustomerInvoice::where(['ci_passenger_id'=>$this->rbs_passenger_id,'ci_ride_id'=>$this->id])->first();

        return [
            'driver'=>$driverData,
            'ride_location'=>[
            'from'=> $res->getFormattedAddress(),
                'from_lat' => $this->rbs_source_lat,
                'from_long' => $this->rbs_source_long,
            'to'=> $des->getFormattedAddress(),
                'to_lat' => $this->rbs_destination_lat,
                'to_long' => $this->rbs_destination_long,
            ],
            'ride_id'=>$this->id,
            'tracking_url'=>(isset($this->rbs_tracking_url) && $this->rbs_tracking_url != null ) ? $this->rbs_tracking_url:"",
            'is_rated'=>$is_rated,
            'trx_id'=>(isset($customer_txdID) && $customer_txdID != null) ? $customer_txdID->ci_Trx_id : null ,
            'ride_rating'=>$ride_rating,
            'ride_status'=>$this->rbs_ride_status,
            'ride_polyline'=>$this->rbs_polyline,
            'ride_total_distance'=>number_format($this->rbs_destination_distance,2,".",","),
            'ride_total_time'=>$totalTime,
            'transport_type'=> $transport_type->name,
            'estimated_cost'=>  $rideCost,
            'payment_image'=> $pay_image,
            'date'=> Utility::convertTimeToUSERzone($this->rbs_created_at, $timezone, $format = 'Y-m-d H:i:s')
        ];
    }
}
