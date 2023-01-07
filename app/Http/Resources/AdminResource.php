<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
        [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'country_code'=>$this->country_code,
            'mobile_no'=>$this->mobile_no,
            'locale'=>$this->locale,
            'panel_mode'=>$this->panel_mode,
            'user_type'=>$this->user_type,
            'profile_pic'=>$this->profile_pic,
            'mobile_number_verified'=>$this->mobile_number_verified,
            'email_verified'=>$this->email_verified,
            'status'=>$this->status,
            

        ];
    }
}
