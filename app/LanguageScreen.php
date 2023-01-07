<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LanguageScreen extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'base_language_screens';
}
