<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RideIgnoredBy extends Model
{
    protected $guarded = [];

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'ride_ignored_bies';

    // Driver Table Relation
    public function driver()
    {
        return $this->belongsTo('App\Driver', 'rib_driver_id');
    }
}
