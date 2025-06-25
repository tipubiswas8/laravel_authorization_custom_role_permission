<?php

namespace Database\Seeders;

use App\Models\SaPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'role-list',
            'role-show',
            'role-create',
            'role-edit',
            'role-delete',

            'permission-list',
            'permission-show',
            'permission-create',
            'permission-edit',
            'permission-delete',

            'user-list',
            'user-show',
            'user-create',
            'user-edit',
            'user-delete',

            'product-list',
            'product-show',
            'product-create',
            'product-edit',
            'product-delete'
        ];

        foreach ($permissions as $permission) {
            SaPermission::create(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
