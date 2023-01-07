<?php

namespace App\Http\Resources;

use App\TransportMake;
use App\TransportModel;
use App\TransportModelColor;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportModelResource extends JsonResource
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
            'name'=>$this->name,
            'colors'=>TransportColorResource::collection(TransportModelColor::listsTranslations('name')->select('transport_model_colors.id','transport_model_colors.tmc_tm_ref_id','transport_model_colors.tmc_tmo_id_ref')->where('transport_model_colors.tmc_tmo_id_ref',$this->id)->get())

        ];
    }
}
