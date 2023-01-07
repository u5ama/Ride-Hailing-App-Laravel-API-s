<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppReferenceTranslation extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'base_app_reference_translations';
}
