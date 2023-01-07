<?php

namespace App\Http\Resources;

use App\TransportMake;
use App\TransportType;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportTypeResource extends JsonResource
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
            'description'=>$this->ttt_description,
            'makes'=>TransportMakeResource::collection(TransportMake::listsTranslations('name')->where('transport_makes.tm_type_ref_id',$this->id)->get())


        ];
    }
}
