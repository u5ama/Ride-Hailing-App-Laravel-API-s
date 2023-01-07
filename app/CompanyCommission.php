<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyCommission extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'company_commission';
}
