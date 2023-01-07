<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PassengerAddress extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'passenger_addresses';
}
