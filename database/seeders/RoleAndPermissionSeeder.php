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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Basic Permissions
            'view any',
            'view own',
            'create',
            'edit',
            'delete',
            'manage',

            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage users',

            // Role & Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',
            'view permissions',
            'manage permissions',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create or update roles with guard_name
        $roles = [
            'Admin' => $permissions, // Admin gets all permissions
            'Sales Manager' => [
                // Basic permissions
                'view any',
                'view own',
                'create',
                'edit',
                'delete',
                'manage',

                // Sales related permissions
            ],
            'Sales person' => [
                // Basic permissions
                'view any',
                'view own',
                'create',
                'edit',
                'delete',
                'manage',

            ],

        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);

            $this->command->info("\nRole '{$roleName}' created with " . count($rolePermissions) . " permissions:");
            foreach ($rolePermissions as $permission) {
                $this->command->info("- $permission");
            }
        }

        // Ensure admin users have the admin role
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Admin');
        })->get();

        foreach ($adminUsers as $admin) {
            if (!$admin->hasRole('Admin')) {
                $admin->assignRole('Admin');
            }
        }
    }
}
