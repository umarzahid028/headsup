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
        $roles = [
            'Admin' => [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => 'admin@admin.com'
            ],
            'Sales Manager' => [
                'name' => 'Sales Manager',
                'email' => 'salesmanager@salesmanager.com',
                'password' => 'salesmanager@salesmanager.com'
            ],
            'Sales person' => [
                'name' => 'Sales person',
                'email' => 'salesperson@salesperson.com',
                'password' => 'salesperson@salesperson.com',
                'counter_number' => '1',
            ]
        ];

        foreach ($roles as $roleName => $userData) {
            // Ensure role exists
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            // Create or fetch user
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now()
                ]
            );

            // Assign role if not already assigned
            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }

            $this->command->info("✅ Created user and assigned role '{$roleName}': {$userData['email']}");
        }
    }
}
