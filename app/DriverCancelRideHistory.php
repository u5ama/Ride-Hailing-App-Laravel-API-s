<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverCancelRideHistory extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'driver_cancel_ride_histories';

    // Driver Table Relation
    public function driver()
    {
        return $this->belongsTo('App\Driver', 'dcrh_driver_id');
    }

    // Passenger (User) Table Relation
    public function passenger()
    {
        return $this->belongsTo('App\User', 'dcrh_passenger_id');
    }

    // App Reference Table Relation
    public function reasonReference()
    {
        return $this->belongsTo('App\AppReference', 'dcrh_reason_id');
    }
}
