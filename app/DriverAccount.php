<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverAccount extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'driver_accounts';

    // Ride Booking Schedule Table Relation
    public function rides()
    {
        return $this->hasOne('App\RideBookingSchedule', 'dc_source_id');
    }
}
