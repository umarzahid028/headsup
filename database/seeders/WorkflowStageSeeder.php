<?php

namespace Database\Seeders;

use App\Models\WorkflowStage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Intake',
                'slug' => 'intake',
                'description' => 'Initial vehicle intake and assignment',
                'order' => 1,
                'target_days' => 1,
                'icon' => 'clipboard-list',
                'color' => 'gray',
            ],
            [
                'name' => 'Performance Test Drive',
                'slug' => 'test_drive',
                'description' => 'Test drive to assess transmission, engine, suspension, etc.',
                'order' => 2,
                'target_days' => 1,
                'icon' => 'car',
                'color' => 'blue',
            ],
            [
                'name' => 'Arbitration',
                'slug' => 'arbitration',
                'description' => 'Issues flagged that are eligible for arbitration',
                'order' => 3,
                'target_days' => 3,
                'icon' => 'exclamation-triangle',
                'color' => 'red',
                'is_required' => false,
            ],
            [
                'name' => 'Diagnostic & Mechanical',
                'slug' => 'mechanical',
                'description' => 'Mechanical repairs and diagnostics',
                'order' => 4,
                'target_days' => 3,
                'icon' => 'wrench',
                'color' => 'orange',
            ],
            [
                'name' => 'Exterior Work',
                'slug' => 'exterior',
                'description' => 'PDR, paint, or touch-up work',
                'order' => 5,
                'target_days' => 2,
                'icon' => 'spray-can',
                'color' => 'yellow',
            ],
            [
                'name' => 'Interior Work',
                'slug' => 'interior',
                'description' => 'Upholstery, radio, dash, steering wheel repairs',
                'order' => 6,
                'target_days' => 2,
                'icon' => 'couch',
                'color' => 'indigo',
            ],
            [
                'name' => 'Idle & Feature Check',
                'slug' => 'features',
                'description' => 'Checking lights, wipers, AC, horn, windows, locks, etc.',
                'order' => 7,
                'target_days' => 1,
                'icon' => 'search',
                'color' => 'purple',
            ],
            [
                'name' => 'Tires, Brakes & Fluids',
                'slug' => 'tires_brakes',
                'description' => 'Tires, brakes, and fluids inspection and service',
                'order' => 8,
                'target_days' => 2,
                'icon' => 'tachometer-alt',
                'color' => 'green',
            ],
            [
                'name' => 'Detail',
                'slug' => 'detail',
                'description' => 'Vehicle detailing',
                'order' => 9,
                'target_days' => 1,
                'icon' => 'shower',
                'color' => 'cyan',
            ],
            [
                'name' => 'Manager Walkaround',
                'slug' => 'walkaround',
                'description' => 'Final quality control check by sales manager',
                'order' => 10,
                'target_days' => 1,
                'icon' => 'clipboard-check',
                'color' => 'teal',
            ],
            [
                'name' => 'Photos & Marketing',
                'slug' => 'photos',
                'description' => 'Photos, buyer\'s guide, and sticker installation',
                'order' => 11,
                'target_days' => 1,
                'icon' => 'camera',
                'color' => 'pink',
            ],
            [
                'name' => 'Frontline Ready',
                'slug' => 'frontline',
                'description' => 'Vehicle is frontline ready',
                'order' => 12,
                'target_days' => 0,
                'icon' => 'flag-checkered',
                'color' => 'green',
            ],
        ];
        
        foreach ($stages as $stage) {
            WorkflowStage::create($stage);
        }
    }
}
