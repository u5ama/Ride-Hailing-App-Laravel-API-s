<?php

namespace App\Http\Resources;

use App\Driver;
use App\Utility\Utility;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class GetDriverAccountResource extends JsonResource
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

        if ($this->rbs_payment_method == 'wallet'){
            $pay_image = 'assets/creditCard/Wallet.png';
        }elseif ($this->rbs_payment_method == 'cash'){
            $pay_image = 'assets/creditCard/Cash.png';
        }elseif ($this->rbs_payment_method == 'creditCard'){
            $pay_image = 'assets/creditCard/Visa.png';
        }
        return [
            'ride_location'=>[
                'from'=> $res->getFormattedAddress(),
                'to'=> $des->getFormattedAddress(),
            ],
            'driver_income'=>$this->dc_amount,
            'driver_source'=>$this->dc_source_type,
            'ride_status'=>$this->rbs_ride_status,
            'payment_image'=> $pay_image,
            'date'=> date('d-m-Y H:i', strtotime($this->rbs_created_at))
        ];
    }
}
