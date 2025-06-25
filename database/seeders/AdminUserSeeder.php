<?php

namespace Database\Seeders;

use App\Models\SaPermission;
use App\Models\SaRole;
use Illuminate\Database\Seeder;
use App\Models\SaUser;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = SaUser::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456')
        ]);

        $role = SaRole::create(['name' => 'Admin', 'guard_name' => 'web']);

        $permissions = SaPermission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
