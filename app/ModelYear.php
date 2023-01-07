<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelYear extends Model
{


    protected $guarded = [];

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'transport_model_years';

}

