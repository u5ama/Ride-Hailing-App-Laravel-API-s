<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetJobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'passenger_id'=>$this->rbs_passenger_id,
            'driver_id'=>$this->rbs_driver_id,
            'transport_id'=>$this->rbs_transport_id,
            'transport_type'=>$this->rbs_transport_type,
            'passenger_lat'=>$this->rbs_source_lat,
            'passenger_long'=>$this->rbs_source_long,
            'destination_lat'=>$this->rbs_destination_lat,
            'destination_long'=>$this->rbs_destination_long,
            'destination_distance'=>$this->rbs_destination_distance,
            'destination_time'=>$this->rbs_destination_time,
            'payment_method'=>$this->rbs_payment_method,

        ];
    }
}
