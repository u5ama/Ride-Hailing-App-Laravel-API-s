<?php

namespace App\Http\Resources;

use App\BaseMedia;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageDataResource extends JsonResource
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
                'screen'=>$this->bm_activity_category,
                'image_list'=>BaseMediaResource::collection(BaseMedia::where(['bm_user_id'=>$this->bm_user_id,'bm_user_type'=>$this->bm_user_type,'bm_activity_category'=>$this->bm_activity_category])->get()),


            ];
    }
}
