<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class CustomerCreditCard extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'customer_credit_cards';

    // Passenger (User) Table Relation
    public function passenger()
    {
        return $this->belongsTo('App\User', 'ccc_user_id');
    }
}
