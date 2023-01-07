<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
class TransportModel extends Model
{
    use Translatable;

    public $translatedAttributes = ['name'];
    public $timestamps = false;
    protected $guarded = [];

    // Transport Make Table Relation
    public function transportMake()
    {
        return $this->belongsTo('App\TransportMake', 'tmo_tm_id_ref');
    }

    // Transport Type Table Relation
     public function transportType()
    {
        return $this->belongsTo('App\TransportType', 'tmo_tt_ref_id');
    }
}
