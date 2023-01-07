<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpcomingScheduleRides extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'upcoming_schedule_rides';


    // Drivers Table Relation
    public function driver()
    {
        return $this->belongsTo('App\Driver', 'usr_driver_id');
    }

    // Passenger (User) Table Relation
    public function passenger()
    {
        return $this->belongsTo('App\User', 'usr_passenger_id');
    }

    // Driver Rating Table Relation
    public function driverRating()
    {
        return $this->hasOne('App\DriverRating', 'dr_ride_id');
    }
}
