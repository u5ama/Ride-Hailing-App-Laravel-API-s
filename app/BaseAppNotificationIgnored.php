<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseAppNotificationIgnored extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'base_app_notificaton_ignored';
}
