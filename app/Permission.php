<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    // Roles Table Relation
    public function roles() {

        return $this->belongsToMany(Roles::class,'roles_permissions');

    }

    // Companies Table Relation
    public function companies() {

        return $this->belongsToMany(Company::class,'companies_permissions');

    }
}
