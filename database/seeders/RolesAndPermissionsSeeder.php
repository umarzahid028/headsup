<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // All possible permissions used in the system
        $permissions = [
            // Role and Permission Management
            'view roles', 'create roles', 'edit roles', 'delete roles', 'assign roles',
            'view permissions', 'create permissions', 'edit permissions', 'delete permissions', 'assign permissions',
            'manage roles and permissions',

            // User permissions
            'view users', 'create users', 'edit users', 'delete users', 'assign roles',

            // Sales Manager permissions
            'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'archive vehicles', 'bulk edit vehicles', 'scan vehicle barcodes',

            'view transports', 'create transports', 'edit transports', 'delete transports', 'manage transports',

            'view sales issues', 'create sales issues', 'edit sales issues', 'delete sales issues',
            'review sales issues', 'manage sales issues', 'assign sales issues', 'resolve sales issues', 'export sales issues',

            'view goodwill claims', 'create goodwill claims', 'edit goodwill claims', 'delete goodwill claims',
            'approve goodwill claims', 'reject goodwill claims', 'update goodwill claims',

            'view tags', 'create tags', 'edit tags', 'delete tags', 'assign tags',

            'view timeline', 'add timeline entries',

            'view alerts', 'create alerts', 'edit alerts', 'delete alerts',

            'view photos', 'upload photos', 'delete photos',

            'view notifications', 'manage notification settings',

            'view sales team', 'add sales team members', 'edit sales team members', 'remove sales team members', 'manage sales team',
        ];

        // Create permissions in DB if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and assign appropriate permissions
        $roles = [
            'Admin' => $permissions,

            'Sales Manager' => [
                'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'archive vehicles', 'bulk edit vehicles', 'scan vehicle barcodes',

                'view transports', 'create transports', 'edit transports', 'delete transports', 'manage transports',

                'view sales issues', 'create sales issues', 'edit sales issues', 'delete sales issues',
                'review sales issues', 'manage sales issues', 'assign sales issues', 'resolve sales issues', 'export sales issues',

                'view goodwill claims', 'create goodwill claims', 'edit goodwill claims', 'delete goodwill claims',
                'approve goodwill claims', 'reject goodwill claims', 'update goodwill claims',

                'view tags', 'create tags', 'edit tags', 'delete tags', 'assign tags',

                'view timeline', 'add timeline entries',

                'view alerts', 'create alerts', 'edit alerts', 'delete alerts',

                'view photos', 'upload photos', 'delete photos',

                'view notifications', 'manage notification settings',

                'view users', 'edit users',

                'view sales team', 'add sales team members', 'edit sales team members', 'remove sales team members', 'manage sales team',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions(Permission::whereIn('name', $rolePermissions)->get());
        }

        // Assign Admin role to first user
        $user = User::first();
        if ($user && !$user->hasRole('Admin')) {
            $user->assignRole('Admin');
        }
    }
}
