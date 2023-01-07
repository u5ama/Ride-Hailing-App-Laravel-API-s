<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GEtVoucherCodeResource extends JsonResource
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
            'voucher_code'=>$this->vc_voucher_code,
            'amount'=>$this->vc_amount . " KWD",
            'issue_date'=>$this->vc_issue_date,
            'expiry_date'=>$this->expiry_date,
            'voucher_used_status'=>$this->vc_voucher_used_status,
            'user_id'=>$this->vc_user_id,
            'redeemed_at'=>$this->vc_redeemed_at,
            'status'=>$this->vc_status,

        ];
    }
}
