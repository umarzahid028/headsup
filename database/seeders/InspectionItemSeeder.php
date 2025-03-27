<?php

namespace Database\Seeders;

use App\Models\InspectionCategory;
use App\Models\InspectionItem;
use Illuminate\Database\Seeder;

class InspectionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = InspectionCategory::all()->keyBy('slug');
        
        $items = [
            // Pre-Inspection Items
            [
                'category_slug' => 'pre-inspection',
                'items' => [
                    [
                        'name' => 'VIN Verification',
                        'description' => 'Verify VIN matches vehicle documentation',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Odometer Check',
                        'description' => 'Verify odometer reading and check for signs of tampering',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Title Verification',
                        'description' => 'Verify clean title status',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Key Fob Check',
                        'description' => 'Verify key fobs function properly',
                        'status' => 'pending',
                    ],
                ]
            ],
            
            // Test Drive Items
            [
                'category_slug' => 'test-drive',
                'items' => [
                    [
                        'name' => 'Engine Performance',
                        'description' => 'Check for smooth acceleration, no hesitation, proper idle',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Transmission Operation',
                        'description' => 'Check for smooth shifting, no slipping or harsh engagement',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Steering Response',
                        'description' => 'Check for proper steering feel, alignment, and no pulling',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Suspension & Ride',
                        'description' => 'Check for abnormal noises, bouncing, or excessive body roll',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Brake Performance',
                        'description' => 'Check for proper stopping power, no pulsation or pulling',
                        'status' => 'pending',
                    ],
                ]
            ],
            
            // Exterior Items
            [
                'category_slug' => 'exterior',
                'items' => [
                    [
                        'name' => 'Front Bumper',
                        'description' => 'Check for damage, scratches, dents, and proper alignment',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Rear Bumper',
                        'description' => 'Check for damage, scratches, dents, and proper alignment',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Hood',
                        'description' => 'Check for damage, rust, paint condition',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Roof',
                        'description' => 'Check for dents, rust, and paint condition',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Doors',
                        'description' => 'Check all doors for damage, rust, paint condition, and proper operation',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Windows & Glass',
                        'description' => 'Check for cracks, chips, and proper operation',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Lights',
                        'description' => 'Check headlights, taillights, turn signals, and brake lights',
                        'status' => 'pending',
                    ],
                ]
            ],
            
            // Interior Items
            [
                'category_slug' => 'interior',
                'items' => [
                    [
                        'name' => 'Seats',
                        'description' => 'Check for tears, stains, wear, and proper adjustment',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Dashboard',
                        'description' => 'Check for cracks, wear, and all warning lights',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Headliner',
                        'description' => 'Check for stains, sagging, and tears',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Carpet & Floor Mats',
                        'description' => 'Check for stains, tears, and excessive wear',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Climate Control',
                        'description' => 'Check AC and heating operation, temperature regulation',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Electronics',
                        'description' => 'Check radio, navigation, Bluetooth, USB ports, power outlets',
                        'status' => 'pending',
                    ],
                ]
            ],
            
            // Mechanical Items
            [
                'category_slug' => 'mechanical',
                'items' => [
                    [
                        'name' => 'Engine Oil',
                        'description' => 'Check condition, level, and signs of leaks',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Transmission Fluid',
                        'description' => 'Check condition, level, and signs of leaks',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Coolant',
                        'description' => 'Check condition, level, and signs of leaks',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Battery',
                        'description' => 'Check condition, terminals, and load test',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Belts & Hoses',
                        'description' => 'Check for cracks, wear, and proper tension',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Exhaust System',
                        'description' => 'Check for leaks, damage, and unusual noise',
                        'status' => 'pending',
                    ],
                ]
            ],
            
            // Tires & Brakes Items
            [
                'category_slug' => 'tires-brakes',
                'items' => [
                    [
                        'name' => 'Front Tires',
                        'description' => 'Check tread depth, wear pattern, sidewall condition',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Rear Tires',
                        'description' => 'Check tread depth, wear pattern, sidewall condition',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Spare Tire',
                        'description' => 'Check condition and verify presence of spare',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Front Brake Pads',
                        'description' => 'Check pad thickness and rotor condition',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Rear Brake Pads',
                        'description' => 'Check pad thickness and rotor condition',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Brake Fluid',
                        'description' => 'Check level and condition',
                        'status' => 'pending',
                    ],
                ]
            ],
            
            // Detail Items
            [
                'category_slug' => 'detail',
                'items' => [
                    [
                        'name' => 'Exterior Wash',
                        'description' => 'Full exterior wash and dry',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Paint Correction',
                        'description' => 'Minor scratch removal and polishing',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Wax/Sealant',
                        'description' => 'Apply protective coating',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Interior Vacuum',
                        'description' => 'Full interior vacuum including trunk',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Interior Wipe Down',
                        'description' => 'Clean all interior surfaces',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Window Cleaning',
                        'description' => 'Clean all windows inside and out',
                        'status' => 'pending',
                    ],
                ]
            ],
            
            // Final Checks Items
            [
                'category_slug' => 'final-checks',
                'items' => [
                    [
                        'name' => 'Frontline Readiness',
                        'description' => 'Verify vehicle is ready for frontline display',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Pricing Verification',
                        'description' => 'Verify pricing is set correctly',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Documentation Check',
                        'description' => 'Verify all vehicle documentation is complete',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Photo Verification',
                        'description' => 'Verify all required photos have been taken',
                        'status' => 'pending',
                    ],
                ]
            ],
            
            // Sales Manager Walkaround Items
            [
                'category_slug' => 'manager-walkaround',
                'items' => [
                    [
                        'name' => 'Exterior Inspection',
                        'description' => 'Final quality check of exterior condition and repairs',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Interior Inspection',
                        'description' => 'Final quality check of interior condition and repairs',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Mechanical Verification',
                        'description' => 'Confirm all mechanical issues have been addressed',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Detail Quality Check',
                        'description' => 'Verify detailing meets dealership standards',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Test Drive Verification',
                        'description' => 'Final brief test drive to verify repairs',
                        'status' => 'pending',
                    ],
                ]
            ],
            
            // Photos & Marketing Items
            [
                'category_slug' => 'photos-marketing',
                'items' => [
                    [
                        'name' => 'Exterior Photos',
                        'description' => 'Take all required exterior photos for listing',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Interior Photos',
                        'description' => 'Take all required interior photos for listing',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Feature Photos',
                        'description' => 'Take photos of special features and selling points',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Buyer\'s Guide',
                        'description' => 'Install buyer\'s guide with correct information',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Window Sticker',
                        'description' => 'Apply window sticker with pricing and features',
                        'status' => 'pending',
                    ],
                    [
                        'name' => 'Online Listing',
                        'description' => 'Verify vehicle is properly listed online with correct details',
                        'status' => 'pending',
                    ],
                ]
            ],
        ];

        foreach ($items as $categoryItems) {
            $categoryId = $categories[$categoryItems['category_slug']]->id ?? null;
            
            if ($categoryId) {
                foreach ($categoryItems['items'] as $item) {
                    InspectionItem::firstOrCreate(
                        [
                            'category_id' => $categoryId,
                            'name' => $item['name']
                        ],
                        [
                            'category_id' => $categoryId,
                            'name' => $item['name'],
                            'description' => $item['description'],
                            'status' => $item['status'],
                            'is_vendor_visible' => true,
                            'is_completed' => false,
                            'vehicle_id' => null,
                            'recon_workflow_id' => null
                        ]
                    );
                }
            }
        }
    }
}
