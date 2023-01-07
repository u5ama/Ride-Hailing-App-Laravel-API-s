<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class EmailHeader extends Model
{
    use Translatable;

    public $translatedAttributes = ['emh_title','emh_description'];
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'email_headers';
}
