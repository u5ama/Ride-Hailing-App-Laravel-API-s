<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PassengerContactList extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'passenger_contact_lists';
}
