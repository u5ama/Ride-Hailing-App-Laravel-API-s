<?php

namespace App\Http\Resources;

use App\Utility\Utility;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class GetMyCreditCardsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $card_number1 = Crypt::decryptString($this->ccc_card_number);
        $card_number = substr_replace($card_number1, '✱✱✱✱✱✱✱✱', 4, 8);
        $CVV = Crypt::decryptString($this->ccc_CVV);
        $CVV = substr_replace($CVV, '✱✱✱', 0, 8);
        $type_url = Utility::getCCType($card_number1);
        return [
            'id'=>$this->id,
            'user_id'=>$this->ccc_user_id,
            'card_number'=>$card_number,
            'expire_year'=> Carbon::parse($this->ccc_expire_year)->format('m-Y'),
            'CVV'=>$CVV,
            'card_holder_name'=>$this->ccc_card_holder_name,
            'card_type'=> $type_url['type'],
            'card_image'=> $type_url['image'],
        ];
    }
}
