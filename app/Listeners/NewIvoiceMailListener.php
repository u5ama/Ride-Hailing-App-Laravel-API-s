<?php

namespace App\Listeners;

use App\BaseAppSocialLinks;
use App\EmailBodyTranslation;
use App\EmailFooterTranslation;
use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\Mail\RecieptEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NewIvoiceMailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $rideData = $event->rideData;
        $user = $event->user;
        $taxPlan = $event->taxPlan;
        $basefare = $event->basefare;
        $rate = $event->rate;
        $beforePickUp = $event->beforePickUp;
        $trx_id = $event->trx_id;
        $initial_wait_rate = $event->initial_wait_rate;
        $vatPlan = $event->vatPlan;
        $beforePickUp = $event->beforePickUp;
        if (!empty($rideData)){
            $sourceLat = floatval($rideData->rbs_source_lat);
            $sourceLong = floatval($rideData->rbs_source_long);
            $desLat = floatval($rideData->rbs_destination_lat);
            $desLong = floatval($rideData->rbs_destination_long);
            $pickup_location = app('geocoder')->reverse($sourceLat,$sourceLong)->get()->first();
            $drop_off = app('geocoder')->reverse($desLat,$desLong)->get()->first();
            $address = '<div class="box-style"><p style="text-align: center">Ride Details: </p><div class="locBox"><i class="fas fa-map-marker-alt"></i>&nbsp;<span>Pickup Location: </span><br><span>'.$pickup_location->getFormattedAddress().'</span>'.'</div><br><div class="locBox"><i class="fas fa-map-pin"></i>&nbsp;<span>Dropoff Location: </span><br><span>'.$drop_off->getFormattedAddress().'</span></div></div>';
            $socialLinks = BaseAppSocialLinks::all();
            $header = EmailHeader::where('id',1)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 1, 'locale' => $user->locale])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 1, 'locale' => $user->locale])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 1,'locale' => $user->locale])->first();
            $langtxt = $user->locale;
        }else{
            $address = 'No Address';
            $socialLinks = BaseAppSocialLinks::all();
            $header = EmailHeader::where('id',1)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 1, 'locale' => $user->locale])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 1, 'locale' => $user->locale])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 1,'locale' => $user->locale])->first();
            $langtxt = $user->locale;
        }

        Mail::to($user->email)->send(new RecieptEmail($user->name,$user->id,$basefare,$rate,$beforePickUp,$trx_id->trx_ID,$initial_wait_rate,$vatPlan,$taxPlan,$address,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt));

    }
}
