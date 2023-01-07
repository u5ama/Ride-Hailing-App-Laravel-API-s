<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppReferenceTypeTranslation extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'base_app_reference_type_translations';
}
