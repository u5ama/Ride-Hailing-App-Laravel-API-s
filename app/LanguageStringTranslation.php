<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LanguageStringTranslation extends Model
{

    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'base_language_string_translations';
}
