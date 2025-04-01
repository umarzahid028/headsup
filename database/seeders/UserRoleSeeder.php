<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles to create accounts for
        $roles = [
            'Admin',
            'Sales Manager',
            'Recon Manager',
            'Transporter',
            'Vendor',
            'Sales Team',
        ];

        foreach ($roles as $roleName) {
            // Convert role name for email (lowercase, replace spaces with dashes)
            $emailPrefix = strtolower(str_replace(' ', '-', $roleName));
            $email = "{$emailPrefix}@{$emailPrefix}.com";

            // Create user with email as password
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $roleName,
                    'email' => $email,
                    'password' => Hash::make($email),
                ]
            );

            // Find the role (use lowercase and hyphenated for spatie roles)
            $roleSlug = strtolower(str_replace(' ', '-', $roleName));
            
            // Check if role exists in system
            $role = Role::where('name', $roleSlug)->first();
            
            // If role doesn't exist, use appropriate mapping or fallback to staff
            if (!$role) {
                switch ($roleSlug) {
                    case 'admin':
                        $roleSlug = 'super-admin';
                        break;
                    case 'sales-manager':
                    case 'recon-manager':
                        $roleSlug = 'manager';
                        break;
                    case 'transporter':
                    case 'sales-team':
                        $roleSlug = 'staff';
                        break;
                    case 'vendor':
                        $roleSlug = 'vendor';
                        break;
                    default:
                        $roleSlug = 'staff';
                        break;
                }
                $role = Role::where('name', $roleSlug)->first();
            }

            // Assign role if it exists
            if ($role) {
                $user->assignRole($role);
            }

            $this->command->info("Created user for role {$roleName}: {$email}");
        }
    }
} 