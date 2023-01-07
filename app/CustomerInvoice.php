<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerInvoice extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'customer_invoices';

    // Passenger (User) Table Relation
    public function passenger()
    {
        return $this->belongsTo('App\User', 'ci_passenger_id');
    }

    // Drivers Table Relation
    public function driver()
    {
        return $this->belongsTo('App\Driver', 'ci_driver_id');
    }

    // Ride Booking Schedule Table Relation
    public function ride()
    {
        return $this->belongsTo('App\RideBookingSchedule', 'ci_ride_id');
    }
}
