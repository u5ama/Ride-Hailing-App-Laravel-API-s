<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewInvoiceHasCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rideData;
    public $user;
    public $basefare;
    public $rate;
    public $beforePickUp;
    public $trx_id;
    public $initial_wait_rate;
    public $vatPlan;
    public $taxPlan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($rideData,$user,$basefare,$rate,$beforePickUp,$trx_id,$initial_wait_rate,$vatPlan,$taxPlan)
    {
        $this->rideData = $rideData;
        $this->user = $user;
        $this->basefare = $basefare;
        $this->rate = $rate;
        $this->beforePickUp = $beforePickUp;
        $this->trx_id = $trx_id;
        $this->initial_wait_rate = $initial_wait_rate;
        $this->vatPlan = $vatPlan;
        $this->taxPlan = $taxPlan;
    }


}
