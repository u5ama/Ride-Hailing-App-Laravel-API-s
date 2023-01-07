<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class WebPage extends Model
{
    use Translatable;

    public $translatedAttributes = ['name','description'];
    protected $table = "web_pages";
    protected $guarded = [];
}
