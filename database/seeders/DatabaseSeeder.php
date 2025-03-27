<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed roles and permissions first
        $this->call(RolesAndPermissionsSeeder::class);

        // Seed workflow stages
        $this->call(WorkflowStageSeeder::class);

        // Seed inspection categories and items
        $this->call([
            InspectionCategorySeeder::class,
            InspectionItemSeeder::class,
        ]);
        
        // Create some sample vehicles if none exist
        if (Vehicle::count() === 0) {
            for ($i = 0; $i < 10; $i++) {
                Vehicle::create([
                    'stock_number' => 'S' . str_pad(rand(1, 999), 5, '0', STR_PAD_LEFT),
                    'year' => rand(2018, 2024),
                    'make' => ['Honda', 'Toyota', 'Ford', 'Chevrolet', 'Nissan'][rand(0, 4)],
                    'model' => ['Civic', 'Corolla', 'F-150', 'Silverado', 'Altima'][rand(0, 4)],
                    'trim' => ['LX', 'LE', 'XLT', 'LT', 'S'][rand(0, 4)],
                    'vin' => strtoupper(substr(md5(rand()), 0, 17)),
                    'status' => 'in_recon',
                    'color' => ['Black', 'White', 'Silver', 'Red', 'Blue'][rand(0, 4)],
                    'mileage' => rand(10000, 100000),
                    'purchase_date' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                    'purchase_price' => rand(10000, 40000),
                    'retail_price' => rand(15000, 50000),
                ]);
            }
        }
        
        // Seed workflows and assignments
        $this->call([
            ReconWorkflowSeeder::class,
            InspectionAssignmentSeeder::class,
        ]);

        // Create post-sale data
        $this->call(GoodwillRepairSeeder::class);
    }
}
