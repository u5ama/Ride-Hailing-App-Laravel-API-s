<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionId extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'transaction_ids';
}
