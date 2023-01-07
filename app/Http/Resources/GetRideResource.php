<?php

namespace App\Http\Resources;

use App\DriverCurrentLocation;
use App\Utility\Utility;
use Illuminate\Http\Resources\Json\JsonResource;

class GetRideResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $lat1= $request->passenger['lat'];
        $long1 = $request->passenger['long'];

//        $lat2 = $request->destination['lat'];
//        $long2 = $request->destination['long'];
        $locationDR = DriverCurrentLocation::where(['dcl_user_id'=>$this->dp_user_id])->first();
        $lat2 = $locationDR->dcl_lat;
        $long2 = $locationDR->dcl_long;
        $distance = Utility::timeAndDistance($lat1,$long1,$lat2,$long2);
        $disDri = $distance->routes[0]->legs[0]->distance->value;
        $durDri = $distance->routes[0]->legs[0]->duration->value;

        return
            [
                'id'=>$this->id,
                'transport_type'=>$this->name,
                'transpost_rating'=>4.5,
                'transport_image'=>$this->tt_image,
                'transport_marker'=>$this->tt_marker,
                'total_rate'=> number_format((float)$this->TotalRate, 2, '.', ''),
                'total_max_rate'=>number_format((float)$this->TotalRateMax, 2, '.', ''),
                'fare_rate'=>'KWD '.number_format((float)$this->TotalRate, 2, '.', '').'-'.number_format((float)$this->TotalRateMax, 2, '.', ''),
                'duration' => (isset($durDri) && $durDri != null) ? number_format((float)$durDri/60 , 0, '.', '') : '1',
                'distance' => (isset($disDri) && $disDri != null) ? number_format((float)$disDri/1000 , 3, '.', '') : "0.000",
                'request_body' => $request->all()
        ];
    }
}
