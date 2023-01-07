<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RideRequestResource extends JsonResource
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
            'driver_id'=>$this->rbs_driver_id,
            'driver_lat'=>$this->rbs_driver_lat,
            'driver_long'=>$this->rbs_driver_long,
            'transport_id'=>$this->rbs_transport_id,
            'transport_type'=>$this->rbs_transport_type,
            'source_lat'=>$this->rbs_source_lat,
            'source_long'=>$this->rbs_source_long,
            'destination_lat'=>$this->rbs_destination_lat,
            'destination_long'=>$this->rbs_destination_long,
            'destination_distance'=>$this->rbs_destination_distance,
            'destination_time'=>$this->rbs_destination_time,
            'ride_status'=>$this->rbs_ride_status,
        ];
    }
}
