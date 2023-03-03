<?php

namespace App\Models\boilerplate\RolePermission;

use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model
{
    protected $table = 'role_has_permissions';

	protected $fillable = ['permission_id', 'role_id'];
	
}
