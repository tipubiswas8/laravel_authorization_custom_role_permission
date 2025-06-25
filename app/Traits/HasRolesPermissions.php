<?php

namespace App\Traits;

use App\Models\SaPermission;
use App\Models\SaRole;
use Illuminate\Support\Facades\DB;

trait HasRolesPermissions
{
    // public function permissions()
    // {
    //     return $this->belongsToMany(
    //         SaPermission::class,
    //         'sa_model_has_sa_permissions',
    //         'sa_user_id',
    //         'sa_permission_id'
    //     )->withPivot('model_name');
    // }

    public function assignRole(...$roles)
    {
        $roles = collect($roles)->flatten()->map(function ($role) {
            return $this->getStoredRole($role)->id;
        })->unique();

        foreach ($roles as $roleId) {
            DB::table('sa_model_has_sa_roles')->updateOrInsert([
                'sa_user_id' => $this->getKey(),
                'sa_role_id' => $roleId,
                'model_name' => get_class($this),
            ]);
        }
    }

    public function syncRoles(...$roles)
    {
        $roleIds = collect($roles)->flatten()->map(fn($r) => $this->getStoredRole($r)->id)->all();

        DB::table('sa_model_has_sa_roles')
            ->where('sa_user_id', $this->getKey())
            ->where('model_name', get_class($this))
            ->delete();

        foreach ($roleIds as $roleId) {
            DB::table('sa_model_has_sa_roles')->insert([
                'sa_user_id' => $this->getKey(),
                'sa_role_id' => $roleId,
                'model_name' => get_class($this),
            ]);
        }
    }

    public function givePermissionTo(...$permissions)
    {
        $permissions = collect($permissions)->flatten()->map(function ($permission) {
            return $this->getStoredPermission($permission)->id;
        })->unique();

        foreach ($permissions as $permissionId) {
            DB::table('sa_model_has_sa_permissions')->updateOrInsert([
                'sa_user_id' => $this->getKey(),
                'sa_permission_id' => $permissionId,
                'model_name' => get_class($this),
            ]);
        }
    }

    public function syncPermissions(...$permissions)
    {
        $permissionIds = collect($permissions)->flatten()->map(function ($perm) {
            return $this->getStoredPermission($perm)->id;
        })->all();

        // Delete old permissions
        DB::table('sa_model_has_sa_permissions')
            ->where('sa_user_id', $this->getKey())
            ->where('model_name', get_class($this))
            ->delete();

        // Insert new permissions
        foreach ($permissionIds as $permId) {
            DB::table('sa_model_has_sa_permissions')->insert([
                'sa_user_id' => $this->getKey(),
                'sa_permission_id' => $permId,
                'model_name' => get_class($this),
            ]);
        }
    }

    protected function getStoredRole($role)
    {
        if (is_numeric($role)) {
            return SaRole::find($role);
        }

        if (is_string($role)) {
            return SaRole::where('name', $role)->first();
        }

        if ($role instanceof SaRole) {
            return $role;
        }

        return null;
    }

    public function getRoleNames(): \Illuminate\Support\Collection
    {
        return DB::table('sa_model_has_sa_roles')
            ->join('sa_roles', 'sa_model_has_sa_roles.sa_role_id', '=', 'sa_roles.id')
            ->where('sa_user_id', $this->getKey())
            ->where('model_name', get_class($this))
            ->pluck('sa_roles.name');
    }

    protected function getStoredPermission($permission): SaPermission
    {
        if (is_numeric($permission)) {
            return SaPermission::findOrFail($permission);
        }

        if ($permission instanceof SaPermission) {
            return $permission;
        }

        return SaPermission::where('name', $permission)->firstOrFail();
    }

    public function getPermissionNames(): \Illuminate\Support\Collection
    {
        $directPermissions = DB::table('sa_model_has_sa_permissions')
            ->join('sa_permissions', 'sa_model_has_sa_permissions.sa_permission_id', '=', 'sa_permissions.id')
            ->where('sa_user_id', $this->getKey())
            ->where('model_name', get_class($this))
            ->pluck('sa_permissions.name');

        $rolePermissions = DB::table('sa_model_has_sa_roles')
            ->join('sa_role_has_sa_permissions', 'sa_model_has_sa_roles.sa_role_id', '=', 'sa_role_has_sa_permissions.sa_role_id')
            ->join('sa_permissions', 'sa_role_has_sa_permissions.sa_permission_id', '=', 'sa_permissions.id')
            ->where('sa_model_has_sa_roles.sa_user_id', $this->getKey())
            ->where('sa_model_has_sa_roles.model_name', get_class($this))
            ->pluck('sa_permissions.name');

        return $directPermissions->merge($rolePermissions)->unique();
    }

    public function hasPermission($permissions): bool
    {
        $permissions = is_array($permissions) ? $permissions : explode('|', $permissions);

        foreach ($permissions as $permission) {
            $perm = $this->getStoredPermission($permission);

            $hasDirect = DB::table('sa_model_has_sa_permissions')
                ->where('sa_user_id', $this->getKey())
                ->where('sa_permission_id', $perm->id)
                ->where('model_name', self::class)
                ->exists();

            $hasThroughRole = DB::table('sa_model_has_sa_roles as smr')
                ->join('sa_role_has_sa_permissions as srp', 'smr.sa_role_id', '=', 'srp.sa_role_id')
                ->where('smr.sa_user_id', $this->getKey())
                ->where('smr.model_name', self::class)
                ->where('srp.sa_permission_id', $perm->id)
                ->exists();

            if ($hasDirect || $hasThroughRole) {
                return true;
            }
        }

        return false;
    }
}
