<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseMediaResource extends JsonResource
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
            'fileName'=>$this->bm_file_name,
            'filePath'=>$this->bm_file_path,
            'type'=>$this->bm_mime_type,
            'order'=>$this->bm_section_order,
            'screen'=>$this->bm_activity_category,
            'fileSize'=>$this->bm_file_size

        ];
    }
}
