<?php

namespace App;


use Astrotomic\Translatable\Translatable;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Translatable;

    public $translatedAttributes = ['name'];

    protected $guarded = [];

    // Category Vehicle Table Relation
    public function CategoryVehicle()
    {
        return $this->hasMany(CategoryVehicle::class, 'category_id');
    }

    public function scopeCloseTo(Builder $query, $latitude, $longitude)
    {
        return $query->whereRaw("ST_Distance_Sphere(
            point(longitude, latitude),
            point(?, ?)
             ) * .000621371192 < delivery_max_range
         ", [
            $longitude,
            $latitude,
        ]);
    }

}
