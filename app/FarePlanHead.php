<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FarePlanHead extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'fare_plan_head';

    // Countries Table Relation
    public function country()
    {
        return $this->belongsTo('App\Country', 'fph_country_id');
    }
}
