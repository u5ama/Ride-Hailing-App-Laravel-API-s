<?php

namespace App\Http\Resources;

use App\AppReference;
use App\PassengerAddress;
use Illuminate\Http\Resources\Json\JsonResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class PassengerAdressesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

          $keyvalu = $this->translate('en')->name;
          $keyvalu1 = str_replace(' ', '', $keyvalu);
        $token=JWTAuth::getToken();
        $user = JWTAuth::toUser($token);
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'key_value'=>$keyvalu1,
            'icon'=>$this->bar_icon,
//            'image'=>$this->bar_image,
            'icon_unselected'=>$this->bar_icon_unselected,
//            'image_unselected'=>$this->bar_image_unselected,
            'address_group'=>GroupAdressesResource::collection(PassengerAddress::where(['pa_user_id'=>$user->id,'pa_address_type'=>$this->id])->get()->unique('pa_group_slug')),


        ];
    }
}
