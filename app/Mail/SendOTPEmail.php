<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOTPEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $socialLinks;

    /**
     * Create a new message instance.
     *
     * @param $details
     * @param $socialLinks
     */
    public function __construct($details,$socialLinks)
    {
        $this->details = $details;
        $this->socialLinks = $socialLinks;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->with(['details'=> $this->details, "socialLinks"=>$this->socialLinks])
            ->subject('New OTP Verification')
            ->markdown('emails.sendOTP');

    }
}
