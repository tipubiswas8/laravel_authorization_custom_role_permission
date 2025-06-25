<?php

namespace App\Models;

use App\Traits\HasRolesPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaRole extends Model
{
    use HasFactory, HasRolesPermissions;
    protected $table = 'sa_roles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name'
    ];

    public function permissions()
    {
        return $this->belongsToMany(
            SaPermission::class,
            'sa_role_has_sa_permissions',
            'sa_role_id',
            'sa_permission_id'
        );
    }

    // optional
    public function getRolePermissions()
    {
        return $this->belongsToMany(SaPermission::class, 'sa_role_has_sa_permissions', 'sa_role_id', 'sa_permission_id');
    }
}
