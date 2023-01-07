<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppControlsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'meta_key' => $this->bac_meta_key,
            'meta_value' => $this->bac_meta_value,
            'control_message' => $this->bac_control_error_message
        ];
    }
}
