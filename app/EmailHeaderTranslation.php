<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailHeaderTranslation extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'email_header_translations';
}
