<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverRating extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'driver_ratings';
}
