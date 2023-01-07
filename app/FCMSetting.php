<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FCMSetting extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'f_c_m_settings';
}
