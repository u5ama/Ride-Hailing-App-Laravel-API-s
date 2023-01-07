<?php

namespace App;

use App\Http\Resources\DriverResource;
use App\Http\Resources\GetMyDriverProfile;
use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Faker\Provider\Company;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Driver extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'drivers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'du_email_verified_at' => 'datetime',
    ];

    // Company Table Relation
    public function company()
    {
        return $this->belongsTo('App\Company', 'du_com_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Driver Profile Table Relation
    public function DriverProfile()
    {
        return $this->hasOne('App\DriverProfile', 'dp_user_id');
    }

    // Driver Profile Table Relation
    public function driverProf()
    {
        return $this->hasOne('App\DriverProfile', 'dp_user_id');
    }

    public function transportType(){
        return $this->belongsTo('App\TransportType','fpd_transport_type_id');
    }

    // Driver Current Location Table Relation
    public function DriverCurrentLocation()
    {
        return $this->hasOne('App\DriverCurrentLocation', 'dcl_user_id');
    }

    // Driver Account Table Relation
    public function DriverAccount()
    {
        return $this->hasMany('App\DriverAccount', 'dc_target_id');
    }

    // Driver Rating Table Relation
    public function DriverRating()
    {
        return $this->hasMany('App\DriverRating', 'dr_driver_id');
    }

    // Drivers with ID Table Relation
    public static function getdriver($id){
        $user = DriverResource::collection(Driver::with('driverProf')->where('id',$id)->get());
       if(isset($user[0])){
           $user = $user[0];
       }else{
           $user = [];
       }
        return $user;
    }

    // Driver Resource Collection Relation
    public static function getdriverfull($id){
        $user = GetMyDriverProfile::collection(Driver::with('driverProf')->where('id',$id)->get());
        return $user[0];
    }
}
