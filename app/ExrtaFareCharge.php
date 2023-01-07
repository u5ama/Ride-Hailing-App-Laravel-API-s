<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExrtaFareCharge extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'extra_fare_charges';
}
