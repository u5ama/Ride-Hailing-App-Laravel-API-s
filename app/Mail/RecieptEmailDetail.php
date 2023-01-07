<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecieptEmailDetail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $id;
    public $basefare;
    public $rate;
    public $beforePickUp;
    public $trx_ID;
    public $initial_wait_rate;
    public $vatPlan;
    public $taxPlan;
    public $address;
    public $socialLinks;
    private $headerTrans;
    private $header;
    private $bodyTrans;
    private $footerTrans;
    private $langtxt;
 private $rate_DATA_Email;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$id,$basefare,$rate,$beforePickUp,$trx_ID,$initial_wait_rate,$vatPlan,$taxPlan,$address,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$rate_DATA_Email)
    {
        $this->name = $name;
        $this->id = $id;
        $this->basefare = $basefare;
        $this->rate = $rate;
        $this->beforePickUp = $beforePickUp;
        $this->trx_ID = $trx_ID;
        $this->initial_wait_rate = $initial_wait_rate;
        $this->vatPlan = $vatPlan;
        $this->taxPlan = $taxPlan;
        $this->address = $address;
        $this->socialLinks = $socialLinks;
        $this->header = $header;
        $this->headerTrans = $headerTrans;
        $this->bodyTrans = $bodyTrans;
        $this->footerTrans = $footerTrans;
        $this->langtxt = $langtxt;
        $this->rate_DATA_Email = $rate_DATA_Email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

//        $subject = $this->headerTrans->emh_subject;
        return $this->subject($this->name.' ')->view('emails.reciept-detail.blade',[

            "name"=>$this->name,
            "id"=>$this->id,
            "basefare"=>$this->basefare,
            "rate"=>$this->rate,
            "beforPickUp"=>$this->beforePickUp,
            "trx_ID"=>$this->trx_ID,
            "initial_wait_rate"=>$this->initial_wait_rate,
            "vatPlan"=>$this->vatPlan,
            "taxPlan"=>$this->taxPlan,
            "address"=>$this->address,
            "socialLinks"=>$this->socialLinks,
            "header"=>$this->header,
            "headerTrans"=>$this->headerTrans,
            "bodyTrans"=>$this->bodyTrans,
            "footerTrans"=>$this->footerTrans,
            "langtxt"=>$this->langtxt,
            "rate_DATA_Email"=>$this->rate_DATA_Email,
        ]);
//        return $this->from(env('MAIL_FROM_ADDRESS'))
//            ->with('details', $this->details)
//            ->subject('Your Last Ride Billing')
//            ->markdown('emails.reciept');

    }
}
