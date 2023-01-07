<?php

namespace App\Http\Resources;

use App\BaseAppNotification;
use App\CustomerCreditCard;
use App\PassengerAccount;
use App\PassengerRating;
use App\RideBookingSchedule;
use App\WebPage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $appType = 'Passenger';
        $webPages = WebPage::translated()->where(['page_status'=>1,'app_type'=>$appType])->orderBy('id','DESC')->first();
        if (!empty($webPages)){
            $webpages = new WebPageResource($webPages);
        }else{
            $webpages = null;
        }
        $ride_request = RideRequestResource::collection(RideBookingSchedule::where('rbs_passenger_id',$this->id)->whereIn('rbs_ride_status', ['Accepted','Driving','Waiting'])->orderBy('id','desc')->get());
        $cards = CustomerCreditCard::where('ccc_user_id',$this->id)->get();
        $cards = GetMyCreditCardsResource::collection($cards);
        $all_rating = $this->PassengerRating->sum('pr_rating');
        $total = $this->PassengerRating->count();
        $wallet = PassengerAccount::where('pc_target_id',$this->id)->orderBy('id','desc')->first();
        $notification = BaseAppNotification::where(['ban_recipient_id'=>$this->id,'ban_is_hidden'=>0,'ban_is_unread'=>1,'ban_recipient_type'=>'Passenger'])->get()->count();
        if ($notification > 0){
            $notification = true;
        }else{
            $notification = false;
        }
        return
        [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'country_code'=>$this->country_code,
            'mobile_no'=>$this->mobile_no,
            'locale'=>$this->locale,
            'panel_mode'=>$this->panel_mode,
            'user_type'=>$this->user_type,
            'profile_pic'=>$this->profile_pic,
            'mobile_number_verified'=>$this->mobile_number_verified,
            'email_verified'=>$this->email_verified,
            'status'=>$this->status,
            'notification_count'=>$notification,
            'is_user_requested_ride'=>(isset($ride_request[0]) && $ride_request[0] != null) ? $ride_request[0] : null,
            'ride_status'=>(isset($ride_request[0]) && $ride_request[0] != null) ? $ride_request[0]->rbs_ride_status : "Idle",
            'rating'=>(isset($all_rating) && $all_rating != null) ? number_format((float)$all_rating/$total , 2, '.', '') : '0.00',
            'credit_cards'=>$cards,
            'wallet'=>(isset($wallet) && $wallet != null) ? number_format((float)$wallet->pc_balance, 2, '.', '') : '0.00',
            'promotions' => $webpages
        ];
    }
}
