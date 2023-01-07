<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppSmtpSetting extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'app_smtp_settings';
}
