<?php

namespace Database\Seeders;

use App\Models\InspectionCategory;
use Illuminate\Database\Seeder;

class InspectionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pre-Inspection',
                'slug' => 'pre-inspection',
                'description' => 'Initial inspection items before detailed checks',
                'order' => 1,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => false,
                'requires_cost' => false,
                'icon' => 'clipboard-check',
                'color' => 'blue'
            ],
            [
                'name' => 'Test Drive',
                'slug' => 'test-drive',
                'description' => 'Performance evaluation during test drive',
                'order' => 2,
                'is_active' => true,
                'requires_photos' => false,
                'requires_vendor' => false,
                'requires_cost' => false,
                'icon' => 'car',
                'color' => 'green'
            ],
            [
                'name' => 'Exterior',
                'slug' => 'exterior',
                'description' => 'Exterior body condition checks',
                'order' => 3,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'car-side',
                'color' => 'indigo'
            ],
            [
                'name' => 'Interior',
                'slug' => 'interior',
                'description' => 'Interior condition checks',
                'order' => 4,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'chair',
                'color' => 'purple'
            ],
            [
                'name' => 'Mechanical',
                'slug' => 'mechanical',
                'description' => 'Engine, transmission, and mechanical systems',
                'order' => 5,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'wrench',
                'color' => 'red'
            ],
            [
                'name' => 'Tires & Brakes',
                'slug' => 'tires-brakes',
                'description' => 'Tire condition and brake system checks',
                'order' => 6,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'tire',
                'color' => 'gray'
            ],
            [
                'name' => 'Detail',
                'slug' => 'detail',
                'description' => 'Interior and exterior detailing',
                'order' => 7,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'sparkles',
                'color' => 'amber'
            ],
            [
                'name' => 'Sales Manager Walkaround',
                'slug' => 'manager-walkaround',
                'description' => 'Final quality control inspection by sales manager',
                'order' => 8,
                'is_active' => true,
                'requires_photos' => false,
                'requires_vendor' => false,
                'requires_cost' => false,
                'icon' => 'clipboard-check',
                'color' => 'blue'
            ],
            [
                'name' => 'Photos & Marketing',
                'slug' => 'photos-marketing',
                'description' => 'Vehicle photography and marketing preparation',
                'order' => 9,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => false,
                'requires_cost' => false,
                'icon' => 'camera',
                'color' => 'purple'
            ],
            [
                'name' => 'Final Checks',
                'slug' => 'final-checks',
                'description' => 'Final verification before frontline ready',
                'order' => 10,
                'is_active' => true,
                'requires_photos' => false,
                'requires_vendor' => false,
                'requires_cost' => false,
                'icon' => 'check-circle',
                'color' => 'emerald'
            ],
        ];

        foreach ($categories as $category) {
            InspectionCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
} 