<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $socialLinks;
    private $name;
    private $id;
    private $headerTrans;
    private $header;
    private $bodyTrans;
    private $footerTrans;
    private $langtxt;
    private $user_type;

    /**
     * Create a new message instance.
     *
     * @param $name
     * @param $id
     * @param $socialLinks
     * @param $header
     * @param $headerTrans
     * @param $bodyTrans
     * @param $footerTrans
     * @param $langtxt
     */
    public function __construct($name,$id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type)
    {
        $this->id = $id;
        $this->langtxt = $langtxt;
        $this->name = $name;
        $this->socialLinks = $socialLinks;
        $this->header = $header;
        $this->headerTrans = $headerTrans;
        $this->bodyTrans = $bodyTrans;
        $this->footerTrans = $footerTrans;
        $this->user_type = $user_type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        if($this->user_type == "driver"){
           $url = route('verifyEmailDriver',[$this->id]);
       }else{
           $url = route('verifyEmailPassenger',[$this->id]);

       }
        $subject = $this->headerTrans->emh_subject;
        return $this->subject($subject)->view('emails.CompanyWelcomeEmail',
            ['name'=>$this->name,'id'=>$this->id,"socialLinks"=>$this->socialLinks, 'header' => $this->header, 'headerTrans' => $this->headerTrans,'url' => $url, 'bodyTrans' => $this->bodyTrans, 'footerTrans' => $this->footerTrans,'locale'=>$this->langtxt]);
    }
}
