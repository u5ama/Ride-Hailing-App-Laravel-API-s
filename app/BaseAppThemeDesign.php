<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseAppThemeDesign extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'base_app_themes_design';
}
