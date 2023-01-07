<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FarePlanDetail extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'fare_plan_details';

    // Extra Fare Charge Table Relation
    public function extrafare(){
        return $this->belongsTo('App\ExrtaFareCharge', 'efc_plan_detail_id');
    }

    // Transport Type Table Relation
    public function transportType(){
        return $this->belongsTo('App\TransportType','fpd_transport_type_id');
    }
}
