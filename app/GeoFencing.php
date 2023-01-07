<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoFencing extends Model
{


    protected $guarded = [];

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'geo_fencings';

}

