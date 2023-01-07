<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
class TransportFuel extends Model
{
    use Translatable;

    public $translatedAttributes = ['name'];
    public $timestamps = false;
    protected $guarded = [];

    
}
