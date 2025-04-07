<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\TransporterSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Use firstOrCreate instead of create to avoid duplicates
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ]
        );

        // Seed roles and permissions
        $this->call(RoleAndPermissionSeeder::class);
        
        // Seed users with specific roles
        $this->call(UserRoleSeeder::class);

        $this->call([        
            PermissionSeeder::class,
            TransporterSeeder::class,
        ]);

        // Inspection & Repair Seeders
        $this->call([
            VendorTypeSeeder::class,
            VendorSeeder::class,
            InspectionStageSeeder::class,
            InspectionItemSeeder::class,
        ]);
    }
}
