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
        'name' => 'Alex',
        'email' => 'alex@alex.com',
        'password' => 'alex@alex.com'
    ],
    'Sales person' => [
        [
            'name' => 'Asad',
            'email' => 'asad@asad.com',
            'password' => 'asad@asad.com',
        ],
        [
            'name' => 'Awais',
            'email' => 'awais@awais.com',
            'password' => 'awais@awais.com',
        ],
        [
            'name' => 'Umar',
            'email' => 'umar@umar.com',
            'password' => 'umar@umar.com',
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
