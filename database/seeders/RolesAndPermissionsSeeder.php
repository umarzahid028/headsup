<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        // Create permissions
        $permissions = [
            // Vehicle permissions
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            'archive vehicles',
            'bulk edit vehicles',
            'scan vehicle barcodes',
            
            // Tags permissions
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            'assign tags',
            
            // Timeline permissions
            'view timeline',
            'add timeline entries',
            
            // Alerts permissions
            'view alerts',
            'create alerts',
            'edit alerts',
            'delete alerts',
            
            // Photos permissions
            'view photos',
            'upload photos',
            'delete photos',
            
            // Ready-to-post checklist permissions
            'view checklists',
            'create checklists',
            'complete checklists',
            
            // Notifications permissions
            'view notifications',
            'manage notification settings',
            
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'super-admin' => $permissions,
            'admin' => array_diff($permissions, ['delete users', 'assign roles']),
            'manager' => [
                'view vehicles', 'create vehicles', 'edit vehicles', 'archive vehicles', 'bulk edit vehicles', 'scan vehicle barcodes',
                'view tags', 'create tags', 'edit tags', 'assign tags',
                'view timeline', 'add timeline entries',
                'view alerts', 'create alerts', 'edit alerts',
                'view photos', 'upload photos', 'delete photos',
                'view checklists', 'create checklists', 'complete checklists',
                'view notifications', 'manage notification settings',
                'view users',
            ],
            'staff' => [
                'view vehicles', 'edit vehicles', 'scan vehicle barcodes',
                'view tags', 'assign tags',
                'view timeline', 'add timeline entries',
                'view alerts',
                'view photos', 'upload photos',
                'view checklists', 'complete checklists',
                'view notifications',
            ],
            'vendor' => [
                'view vehicles',
                'view timeline',
                'view photos', 'upload photos',
                'view checklists', 'complete checklists',
                'view notifications',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            // Create role if it doesn't exist
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            // Get all the permissions objects
            $permissionsToSync = Permission::whereIn('name', $rolePermissions)->get();
            
            // Sync permissions to role
            $role->syncPermissions($permissionsToSync);
        }

        // Assign super-admin role to first user if exists
        $user = User::first();
        if ($user && !$user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }
    }
}
