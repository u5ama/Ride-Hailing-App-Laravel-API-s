<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class LanguageString extends Model
{
    use Translatable;

    public $translatedAttributes = ['name'];

    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'base_language_strings';
}
