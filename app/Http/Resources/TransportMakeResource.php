<?php

namespace App\Http\Resources;

use App\TransportModel;
use App\TransportModelColor;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportMakeResource extends JsonResource
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
          'makeName'=>$this->name,
          'models'=>TransportModelResource::collection(TransportModel::listsTranslations('name')->where('transport_models.tmo_tm_id_ref',$this->id)->get()),
        ];
    }
}
