<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
class TransportModelColor extends Model
{
    use Translatable;

    public $translatedAttributes = ['name'];
    public $timestamps = false;
    protected $guarded = [];

    // Transport Make Table Relation
    public function transportMake()
    {
        return $this->belongsTo('App\TransportMake', 'tmc_tm_ref_id');
    }

    // Transport Type Table Relation
     public function transportType()
    {
        return $this->belongsTo('App\TransportType', 'tmc_tt_ref_id');
    }

    // Transport Model Table Relation
    public function transportModel()
    {
        return $this->belongsTo('App\TransportModel', 'tmc_tmo_id_ref');
    }
}
