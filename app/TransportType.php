<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
class TransportType extends Model
{
    use Translatable;

    public $translatedAttributes = ['name','ttt_description'];
    public $timestamps = false;
    protected $guarded = [];

    // Fare Plan Detail Table Relation
    public function FarePlanDetail(){
        return $this->belongsTo('App\FarePlanDetail', 'fpd_transport_type_id');
    }
}
