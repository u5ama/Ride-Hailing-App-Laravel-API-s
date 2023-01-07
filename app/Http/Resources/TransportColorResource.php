<?php

namespace App\Http\Resources;

use App\ModelYear;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportColorResource extends JsonResource
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
                'colorName'=>$this->name,
                'years'=>TransportYearResource::collection(ModelYear::where('transport_model_years.tmc_tmo_id_ref',$this->tmc_tmo_id_ref)->get()),
            ];
    }
}
