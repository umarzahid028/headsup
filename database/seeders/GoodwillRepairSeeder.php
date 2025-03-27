<?php

namespace Database\Seeders;

use App\Models\GoodwillRepair;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodwillRepairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we have vehicles, users, and vendors
        $vehicles = Vehicle::all();
        $users = User::all();
        $vendors = Vendor::all();

        if ($vehicles->isEmpty()) {
            $this->command->info('No vehicles found. Skipping goodwill repair seeding.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->info('No users found. Skipping goodwill repair seeding.');
            return;
        }

        // Clear existing records for testing
        DB::table('goodwill_repairs')->truncate();

        // Sample goodwill repairs
        $repairs = [
            [
                'title' => 'Air Conditioning Repair',
                'description' => 'Customer complained about weak AC performance. Goodwill repair to replace compressor and recharge system.',
                'cost' => 750.00,
                'status' => 'pending',
                'customer_name' => 'John Smith',
                'customer_phone' => '555-123-4567',
                'customer_email' => 'john.smith@example.com',
                'due_date' => now()->addDays(5),
                'waiver_signed' => false,
            ],
            [
                'title' => 'Power Window Motor Replacement',
                'description' => 'Driver side window stopped working after purchase. Replacing motor as goodwill repair.',
                'cost' => 325.50,
                'status' => 'in_progress',
                'customer_name' => 'Sarah Johnson',
                'customer_phone' => '555-987-6543',
                'customer_email' => 'sarah.johnson@example.com',
                'due_date' => now()->addDays(2),
                'waiver_signed' => true,
                'waiver_signed_at' => now()->subDays(1),
                'signature_data' => 'data:image/png;base64,iVBORw0KGgo=',
                'signature_ip' => '192.168.1.1',
            ],
            [
                'title' => 'Squeaky Brake Repair',
                'description' => 'Front brakes making noise after recent purchase. Replacing brake pads and resurfacing rotors.',
                'cost' => 275.00,
                'status' => 'completed',
                'customer_name' => 'Michael Wilson',
                'customer_phone' => '555-456-7890',
                'customer_email' => 'michael.wilson@example.com',
                'due_date' => now()->subDays(3),
                'completed_at' => now()->subDays(1),
                'waiver_signed' => true,
                'waiver_signed_at' => now()->subDays(5),
                'signature_data' => 'data:image/png;base64,iVBORw0KGgo=',
                'signature_ip' => '192.168.1.2',
            ],
            [
                'title' => 'Infotainment System Update',
                'description' => 'Customer unable to connect phone to infotainment system. Software update and demonstration of proper usage.',
                'cost' => 0.00,
                'status' => 'pending',
                'customer_name' => 'Lisa Brown',
                'customer_phone' => '555-789-0123',
                'customer_email' => 'lisa.brown@example.com',
                'due_date' => now()->addDays(1),
                'waiver_signed' => false,
            ],
        ];

        foreach ($repairs as $repair) {
            // Get random vehicle, user, and vendor
            $vehicle = $vehicles->random();
            $user = $users->random();
            $vendor = $vendors->isNotEmpty() ? $vendors->random() : null;
            
            $goodwillRepair = new GoodwillRepair(array_merge($repair, [
                'vehicle_id' => $vehicle->id,
                'assigned_to' => $user->id,
                'vendor_id' => $vendor ? $vendor->id : null,
                'created_by' => $users->random()->id,
            ]));
            
            $goodwillRepair->save();
        }

        $this->command->info('Sample goodwill repairs created successfully!');
    }
}
