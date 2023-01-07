<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PassengerGroupAdressesResource extends JsonResource
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
            'user_id'=>$this->pa_user_id,
            'address_type'=>$this->pa_address_type,
            'lat'=>$this->pa_lat,
            'long'=>$this->pa_long,
            'group_name'=>$this->pa_group_name,
            'address_text'=>$this->pa_address_text,
            'group_slug'=>$this->pa_group_slug
        ];
    }
}
