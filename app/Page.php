<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use Translatable;

    public $translatedAttributes = ['name','description'];
    protected $table = "pages";
    protected $guarded = [];
}
