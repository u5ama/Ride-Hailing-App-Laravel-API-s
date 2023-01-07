<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'permission_roles';

    // Permission Table Relation
    public function permissions() {
        return $this->belongsToMany(Permission::class,'roles_permissions');
    }

    // Company Table Relation
    public function companies() {
        return $this->belongsToMany(Company::class,'companies_roles');
    }
}
