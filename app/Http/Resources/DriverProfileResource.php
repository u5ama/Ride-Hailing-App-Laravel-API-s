<?php

namespace App\Http\Resources;

use App\BaseMedia;
use App\TransportType;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(isset($this->dp_transport_type_id_ref) && $this->dp_transport_type_id_ref != null) {
            $transport = TransportType::where('transport_types.id', $this->dp_transport_type_id_ref)->first();
            $transporttypemaler = $transport->tt_marker;
        }else{
            $transporttypemaler = 'assets/transport_type_marker/1609852664-economy@3x.png';
        }
        return [
            'license_number'=>$this->dp_license_number,
            'personal_id'=>$this->dp_personal_id,
            'transport_type_id_ref'=>$this->dp_transport_type_id_ref,
            'transport_marker'=>$transporttypemaler,
            'fuel_id_ref'=>$this->dp_fuel_id_ref,
            'car_registration'=>$this->car_registration,
            'date_manufacture'=>date('d-m-Y', strtotime($this->dp_date_manufacture)),
            'date_registration'=>date('d-m-Y', strtotime($this->dp_date_registration)),
            'transport_make_id'=>$this->dp_transport_make_id,
            'transport_model_id'=>$this->dp_transport_model_id,
            'transport_color_id'=>$this->dp_transport_color_id,
            'transport_year_id'=>$this->dp_transport_year_id,
            'driver_id'=>$this->dp_user_id,
            'image_data'=>ImageDataResource::collection(BaseMedia::where(['bm_user_id'=>$this->dp_user_id,'bm_user_type'=>'driver'])->get()->unique('bm_activity_category')),

        ];
    }
}
