<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
class TransportMake extends Model
{
    use Translatable;

    public $translatedAttributes = ['name'];
    public $timestamps = false;
    protected $guarded = [];

    // Transport Type Table Relation
    public function transportType()
    {
        return $this->belongsTo('App\TransportType', 'tm_type_ref_id');
    }
}
