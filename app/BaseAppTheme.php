<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseAppTheme extends Model
{
    //base_app_themes
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'base_app_themes';
}
