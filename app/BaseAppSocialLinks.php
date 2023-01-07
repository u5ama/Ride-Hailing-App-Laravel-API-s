<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseAppSocialLinks extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'base_app_social_links';
}
