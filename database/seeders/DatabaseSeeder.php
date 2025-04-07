<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\TransporterSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

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
