<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseAppReferenceResource extends JsonResource
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
            'icon'=>$this->bar_icon,
            'image'=>$this->bar_image,
            'system_flag'=>$this->bar_system_flag,
            'order_by'=>$this->bar_order_by,

        ];
    }
}
