<?php

namespace Database\Seeders;

use App\Models\ReconWorkflow;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReconWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create sample workflows if we have vehicles in the system
        $vehicles = Vehicle::take(5)->get();
        $user = User::first();
        
        if ($vehicles->isEmpty() || !$user) {
            return;
        }
        
        // Sample workflow statuses
        $statuses = ['pending', 'in_progress', 'on_hold', 'completed'];
        
        foreach ($vehicles as $index => $vehicle) {
            // Create a workflow for each vehicle with different statuses
            $status = $statuses[$index % count($statuses)];
            $totalItems = rand(20, 45);
            $completedItems = $status === 'completed' ? $totalItems : rand(0, $totalItems);
            $hasArbitration = rand(0, 1) === 1;
            
            ReconWorkflow::firstOrCreate(
                ['vehicle_id' => $vehicle->id],
                [
                    'vehicle_id' => $vehicle->id,
                    'status' => $status,
                    'started_by' => $user->id,
                    'completed_by' => $status === 'completed' ? $user->id : null,
                    'total_cost' => $status === 'completed' ? rand(500, 3000) : 0,
                    'total_items' => $totalItems,
                    'completed_items' => $completedItems,
                    'started_at' => now()->subDays(rand(1, 14)),
                    'completed_at' => $status === 'completed' ? now()->subDays(rand(0, 3)) : null,
                    'notes' => $hasArbitration ? 'Vehicle has arbitration issues that need to be addressed.' : null,
                    'has_arbitration_issues' => $hasArbitration,
                ]
            );
        }
    }
}
