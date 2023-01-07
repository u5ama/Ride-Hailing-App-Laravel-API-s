<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppThemeDesignResource extends JsonResource
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
            'screen_info' => $this->batd_screen_info,
            'design_key_field' => $this->batd_design_key_field,
            'design_value' => $this->batd_design_value,
            'color_code' => $this->batd_color_code,
            'description' => $this->batd_description,
        ];
    }
}
