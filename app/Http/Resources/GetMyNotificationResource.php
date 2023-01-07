<?php

namespace App\Http\Resources;

use App\Utility\Utility;
use Illuminate\Http\Resources\Json\JsonResource;

class GetMyNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $stamp = Utility::lagTime();
        $timestamp = Utility::create_at_time($this->ban_created_at,$stamp);
        return [
            'id'=>$this->id,
            'sender_id'=>$this->ban_sender_id,
            'type'=>$this->ban_type_of_notification,
            'title'=>$this->ban_title_text,
            'body'=>$this->ban_body_text,
            'activity'=>$this->ban_activity,
            'is_unread'=>$this->ban_is_unread,
            'time'=>$timestamp,
        ];
    }
}
