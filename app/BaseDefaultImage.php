<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseDefaultImage extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'base_default_images';
}
