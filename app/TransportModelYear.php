<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportModelYear extends Model
{

        protected $guarded = [];
        public $timestamps = false;

    // Transport Make Table Relation
    public function transportMake()
    {
        return $this->belongsTo('App\TransportMake', 'tmy_tm_ref_id');
    }

    // Transport Type Table Relation
     public function transportType()
    {
        return $this->belongsTo('App\TransportType', 'tmy_tt_ref_id');
    }

    // Transport Model Table Relation
    public function transportModel()
    {
        return $this->belongsTo('App\TransportModel', 'tmy_tmo_ref_id');
    }

    // Transport Model Color Table Relation
    public function transportModelColor()
    {
        return $this->belongsTo('App\TransportModelColor', 'tmc_tmo_id_ref');
    }
}
