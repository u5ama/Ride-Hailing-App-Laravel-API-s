<?php

namespace App\Http\Resources;

use App\PassengerAddress;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class GroupAdressesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $token=JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        return [
            'id'=>$this->id,
            'group_name'=>$this->pa_group_name,
            'addresses'=>PassengerGroupAdressesResource::collection(PassengerAddress::where(['pa_address_type'=>$this->pa_address_type,'pa_group_slug'=>$this->pa_group_slug,'pa_user_id'=>$user->id])->get()->unique('address_text')),

        ];
    }
}
