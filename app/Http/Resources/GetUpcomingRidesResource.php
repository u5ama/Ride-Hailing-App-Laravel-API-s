<?php

namespace App\Http\Resources;

use App\Country;
use App\Driver;
use App\User;
use App\Utility\Utility;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class GetUpcomingRidesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = app('geocoder')->reverse($this->usr_source_lat,$this->usr_source_long)->get()->first();
        $des = app('geocoder')->reverse($this->usr_destination_lat,$this->usr_destination_long)->get()->first();
        $totalTime = (float)$this->usr_destination_time;
        if ($this->usr_ride_status == 'Pending'){
            $status = 'Scheduled';
        }

        if ($this->usr_payment_method == 'wallet'){
            $pay_image = 'assets/creditCard/Wallet.png';
        }elseif ($this->usr_payment_method == 'cash'){
            $pay_image = 'assets/creditCard/Cash.png';
        }elseif ($this->usr_payment_method == 'credit_card'){
            $pay_image = 'assets/creditCard/Visa.png';
        }

        return [
            'driver'=> null,
            'ride_location'=>[
                'from'=> $res->getFormattedAddress(),
                'from_lat' => $this->usr_source_lat,
                'from_long' => $this->usr_source_long,
                'to'=> $des->getFormattedAddress(),
                'to_lat' => $this->usr_destination_lat,
                'to_long' => $this->usr_destination_long,
            ],
            'ride_id'=>$this->id,
            'schedule_start_time'=>$this->usr_schedule_start_time,
            'schedule_start_date'=>$this->usr_schedule_start_date,
            'ride_total_distance'=>number_format($this->usr_destination_distance,2,".",","),
            'ride_total_time'=>number_format($totalTime,2,".",":")*1,
            'ride_status'=>$status,
            'transport_type'=>$this->usr_transport_type,
            'estimated_cost'=>  "KW " . number_format((float)$this->usr_estimated_cost, 2, '.', ''),
            'payment_image'=> $pay_image,
            'date'=> $this->usr_schedule_start_date .' '.$this->usr_schedule_start_time
        ];
    }
}
