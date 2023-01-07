<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RideBookingSchedule extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'ride_booking_schedules';


    // Driver Table Relation
    public function driver()
    {
        return $this->belongsTo('App\Driver', 'rbs_driver_id');
    }

    // Passenger (User) Table Relation
    public function passenger()
    {
        return $this->belongsTo('App\User', 'rbs_passenger_id');

    }

    // Driver Rating Table Relation
    public function driverRating()
    {
        return $this->hasOne('App\DriverRating', 'dr_ride_id');

    }

    // Passenger Rating Table Relation
    public function passengerRating()
    {
        return $this->hasOne('App\PassengerRating', 'pr_ride_id');

    }
}
