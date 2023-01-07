<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PassengerCancelRideHistory extends Model
{

    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'passenger_cancel_ride_histories';

    // Driver Table Relation
    public function driver()
    {
        return $this->belongsTo('App\Driver', 'pcrh_driver_id');
    }

    // Passenger (User) Table Relation
    public function passenger()
    {
        return $this->belongsTo('App\User', 'pcrh_passenger_id');
    }

    // App Reference Table Relation
    public function reasonReference()
    {
        return $this->belongsTo('App\AppReference', 'pcrh_reason_id');
    }

}
