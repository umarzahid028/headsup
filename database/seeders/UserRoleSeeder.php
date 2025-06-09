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
        [
            'name' => 'Sales Person 1',
            'email' => 'salesperson1@sales.com',
            'password' => 'salesperson1@sales.com',
            'counter_number' => '1',
        ],
        [
            'name' => 'Sales Person 2',
            'email' => 'salesperson2@sales.com',
            'password' => 'salesperson2@sales.com',
            'counter_number' => '2',
        ],
        [
            'name' => 'Sales Person 3',
            'email' => 'salesperson3@sales.com',
            'password' => 'salesperson3@sales.com',
            'counter_number' => '3',
        ],
        [
            'name' => 'Sales Person 4',
            'email' => 'salesperson4@sales.com',
            'password' => 'salesperson4@sales.com',
            'counter_number' => '4',
        ],
    ]
];

       foreach ($roles as $roleName => $roleData) {
    $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

    if ($roleName === 'Sales person') {
        foreach ($roleData as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'counter_number' => $userData['counter_number'] ?? null,
                    'email_verified_at' => now()
                ]
            );
            $user->assignRole($roleName);
        }
    } else {
        $user = User::firstOrCreate(
            ['email' => $roleData['email']],
            [
                'name' => $roleData['name'],
                'password' => Hash::make($roleData['password']),
                'email_verified_at' => now()
            ]
        );
        $user->assignRole($roleName);
    }
}

    }
}
