<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverCurrentLocation extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'driver_current_locations';

    // Driver Account Table Relation
    public function DriverAccount()
    {
        return $this->hasMany('App\DriverAccount', 'dc_target_id');
    }
}
