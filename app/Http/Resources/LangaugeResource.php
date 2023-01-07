<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LangaugeResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'is_rtl' => $this->is_rtl,
            'language_code' => $this->language_code,
        ];
    }
}
