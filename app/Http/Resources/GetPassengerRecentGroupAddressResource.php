<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetPassengerRecentGroupAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        $sourceLat = floatval($this->rbs_source_lat);
        $sourceLong = floatval($this->rbs_source_long);
        $desLat = floatval($this->rbs_destination_lat);
        $desLong = floatval($this->rbs_destination_long);
        $pickup_location = app('geocoder')->reverse($sourceLat,$sourceLong)->get()->first();
        $drop_off = app('geocoder')->reverse($desLat,$desLong)->get()->first();

        $res = [];
        array_push($res,[
            'id'=>null,
            'user_id'=>null,
            'address_type'=>$this->pa_address_type,
            'lat'=>$this->rbs_source_lat,
            'long'=>$this->rbs_source_long,
            'group_name'=>'drop_off_location',
            'address_text'=>$drop_off->getFormattedAddress(),
            'group_slug'=>null
        ]);
        array_push($res, [
        'id'=>null,
        'user_id'=>null,
        'address_type'=>null,
        'lat'=>$this->rbs_source_lat,
        'long'=>$this->rbs_source_long,
        'group_name'=>'pickup_location',
        'address_text'=>$pickup_location->getFormattedAddress(),
        'group_slug'=>null
    ]);
        return $res;

    }
}
