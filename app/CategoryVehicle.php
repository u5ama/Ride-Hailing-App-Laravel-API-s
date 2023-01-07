<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryVehicle extends Model
{
    protected $guarded = [];

    // Category Table Relation
    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }
}
