<?php

namespace Database\Seeders;

use App\Models\InspectionAssignment;
use App\Models\InspectionItem;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class InspectionAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only create sample assignments if we have inspection items
        $items = InspectionItem::where('status', 'repair')
            ->orWhere('status', 'replace')
            ->take(10)
            ->get();
            
        if ($items->isEmpty()) {
            return;
        }
        
        // Get vendors and users for assignment
        $vendors = Vendor::take(3)->get();
        $users = User::take(2)->get();
        
        if ($vendors->isEmpty() || $users->isEmpty()) {
            return;
        }
        
        $statuses = ['pending', 'in_progress', 'completed'];
        
        foreach ($items as $index => $item) {
            // Only assign to vendor if the item is in certain categories
            $categoryId = $item->category_id;
            $requiresVendor = in_array($categoryId, [3, 4, 5, 6, 7]); // Exterior, Interior, Mechanical, Tires & Brakes, Detail
            
            $status = $statuses[$index % count($statuses)];
            $cost = $status === 'completed' ? rand(50, 500) : 0;
            $assignedTo = $requiresVendor 
                ? $vendors[rand(0, count($vendors) - 1)]->id 
                : $users[rand(0, count($users) - 1)]->id;
            $assigneeType = $requiresVendor ? 'vendor' : 'user';
            
            InspectionAssignment::create([
                'inspection_item_id' => $item->id,
                'assigned_to' => $assignedTo,
                'assignee_type' => $assigneeType,
                'assigned_by' => $users[0]->id,
                'status' => $status,
                'cost' => $cost,
                'notes' => $this->getRandomNote($status, $item->name),
                'assigned_at' => now()->subDays(rand(1, 10)),
                'completed_at' => $status === 'completed' ? now()->subHours(rand(1, 72)) : null,
            ]);
            
            // Update the item status if the assignment is completed
            if ($status === 'completed') {
                $item->update([
                    'status' => rand(0, 1) ? 'pass' : 'repair',
                    'is_completed' => true,
                    'completed_by' => $users[0]->id,
                    'completed_at' => now()->subHours(rand(1, 24)),
                    'cost' => $cost,
                ]);
            }
        }
    }
    
    /**
     * Generate a random note based on status and item name
     */
    private function getRandomNote($status, $itemName)
    {
        $notes = [
            'pending' => [
                "Needs attention for {$itemName}",
                "Please evaluate {$itemName} as soon as possible",
                "Waiting for parts to repair {$itemName}",
                "Schedule inspection for {$itemName}",
            ],
            'in_progress' => [
                "Currently working on {$itemName}",
                "Parts ordered for {$itemName} repair",
                "{$itemName} repairs underway",
                "Diagnostic being performed on {$itemName}",
            ],
            'completed' => [
                "{$itemName} has been repaired successfully",
                "Completed replacement of {$itemName}",
                "Fixed issues with {$itemName}, tested and verified",
                "{$itemName} has been restored to factory specifications",
            ],
        ];
        
        $randomNotes = $notes[$status];
        return $randomNotes[array_rand($randomNotes)];
    }
}
