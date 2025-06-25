<?php

namespace App\Models;

use App\Traits\HasRolesPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaPermission extends Model
{
    use HasFactory, HasRolesPermissions;
    protected $table = 'sa_permissions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name'
    ];

    public function roles()
    {
        return $this->belongsToMany(
            SaRole::class,
            'sa_role_has_sa_permissions',
            'sa_permission_id',
            'sa_role_id'
        );
    }

    // optional
    public function getPermissionRoles()
    {
        return $this->belongsToMany(SaRole::class, 'sa_role_has_sa_permissions', 'sa_permission_id', 'sa_role_id');
    }
}
