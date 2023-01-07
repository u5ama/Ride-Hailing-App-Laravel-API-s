<?php

namespace App\Http\Resources;

use App\Driver;
use App\Utility\Utility;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class GetPassengerBookedRideDetailResource extends JsonResource
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
       $rating = $this->driverRating;

       $totalTime = Carbon::parse($this->rbs_driving_start_time)->diffInMinutes($this->rbs_driving_end_time);

       if (!empty($rating)){
           $is_rated = true;
           $ride_rating = $this->driverRating->dr_rating;
       }else{
           $is_rated = false;
           $ride_rating = 0;
       }
        if ($this->rbs_payment_method == 'wallet'){
            $pay_image = 'assets/creditCard/Wallet.png';
        }elseif ($this->rbs_payment_method == 'cash'){
            $pay_image = 'assets/creditCard/Cash.png';
        }elseif ($this->rbs_payment_method == 'creditcard'){
            $pay_image = 'assets/creditCard/Visa.png';
        }
        return [
            'driver'=>Driver::getdriver($this->driver->id),
            'ride_location'=>[
            'from'=> $res->getFormattedAddress(),
                'from_lat' => $this->rbs_source_lat,
                'from_long' => $this->rbs_source_long,
            'to'=> $des->getFormattedAddress(),
                'to_lat' => $this->rbs_destination_lat,
                'to_long' => $this->rbs_destination_long,
            ],
            'is_rated'=>$is_rated,
            'ride_rating'=>$ride_rating,
            'ride_status'=>$this->rbs_ride_status,
            'ride_polyline'=>$this->rbs_polyline,
            'ride_total_distance'=>number_format($this->rbs_destination_distance,2,".",","),
            'ride_total_time'=>$totalTime,
            'transport_type'=>$this->rbs_transport_type,
            'estimated_cost'=>  "KWD" . number_format((float)$this->rbs_estimated_cost, 2, '.', ''),
            'payment_image'=> $pay_image,
            'date'=> date('d-m-Y H:i', strtotime($this->rbs_created_at))
        ];
    }
}
