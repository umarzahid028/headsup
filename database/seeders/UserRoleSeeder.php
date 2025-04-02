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
                'name' => 'Admin User',
                'email' => 'admin@admin.com',
                'password' => 'admin@admin.com'
            ],
            'Sales Manager' => [
                'name' => 'Sales Manager',
                'email' => 'sales-manager@sales-manager.com',
                'password' => 'password'
            ],
            'Recon Manager' => [
                'name' => 'Recon Manager',
                'email' => 'recon-manager@recon-manager.com',
                'password' => 'password'
            ],
            'Transporter' => [
                'name' => 'Transporter',
                'email' => 'transporter@transporter.com',
                'password' => 'password'
            ],
            'Vendor' => [
                'name' => 'Vendor',
                'email' => 'vendor@vendor.com',
                'password' => 'password'
            ],
            'Sales Team' => [
                'name' => 'Sales Team',
                'email' => 'sales-team@sales-team.com',
                'password' => 'password'
            ]
        ];

        foreach ($roles as $role => $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now()
                ]
            );

            $user->assignRole($role);
            $this->command->info("Created user for role {$role}: {$userData['email']}");
        }
    }
} 