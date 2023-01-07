<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PassengerPaymentDetail extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'passenger_payment_details';
}
