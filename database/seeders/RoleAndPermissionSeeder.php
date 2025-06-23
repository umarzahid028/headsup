<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::query()->delete();
        Permission::query()->delete();

        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',

            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',

            'view any',
            'view own',
            'create',
            'edit',
            'delete',
            'manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'Admin' => $permissions,

            'Sales Manager' => [
                'view users',
                'edit users',
                'view any',
                'view own',
                'create',
                'manage'
            ],

            'Sales person' => [
                'view any',
                'view own',
                'create',
                'edit',
                'delete'
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        $adminUser = User::first();
        if ($adminUser && !$adminUser->hasRole('Admin')) {
            $adminUser->assignRole('Admin');
        }
    }
}
