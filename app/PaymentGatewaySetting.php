<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewaySetting extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'payment_gateway_settings';
}
