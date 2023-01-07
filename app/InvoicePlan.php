<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoicePlan extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'invoice_plans';
}
