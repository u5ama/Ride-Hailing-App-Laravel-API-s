<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'time_zones';
}
