<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('inspection_categories')) {
            return;
        }
        
        Schema::create('inspection_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_photos')->default(false);
            $table->boolean('requires_vendor')->default(false);
            $table->boolean('requires_cost')->default(false);
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // Insert default inspection categories
        DB::table('inspection_categories')->insert([
            [
                'name' => 'Performance Test Drive',
                'slug' => 'test-drive',
                'description' => 'Transmission, engine, suspension, 4x4, steering',
                'order' => 1,
                'is_active' => true,
                'requires_photos' => false,
                'requires_vendor' => false,
                'requires_cost' => false,
                'icon' => 'truck',
                'color' => 'blue',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Arbitration Bucket',
                'slug' => 'arbitration',
                'description' => 'Issues that are flagged and eligible for arbitration',
                'order' => 2,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => false,
                'requires_cost' => true,
                'icon' => 'exclamation-circle',
                'color' => 'red',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Diagnostic/Mechanical Repair',
                'slug' => 'mechanical',
                'description' => 'Mechanical diagnosis and repair',
                'order' => 3,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'wrench',
                'color' => 'orange',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Exterior Work',
                'slug' => 'exterior',
                'description' => 'PDR, paint, or touch-up',
                'order' => 4,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'car',
                'color' => 'green',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Interior Work',
                'slug' => 'interior',
                'description' => 'Upholstery, radio, dash/steering wheel',
                'order' => 5,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'chair',
                'color' => 'indigo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Idle/Feature Check',
                'slug' => 'feature-check',
                'description' => 'Lights, wipers, AC, horn, windows/locks, etc.',
                'order' => 6,
                'is_active' => true,
                'requires_photos' => false,
                'requires_vendor' => false,
                'requires_cost' => false,
                'icon' => 'clipboard-check',
                'color' => 'teal',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tires',
                'slug' => 'tires',
                'description' => 'Tire inspection and replacement',
                'order' => 7,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'cog',
                'color' => 'purple',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Brakes',
                'slug' => 'brakes',
                'description' => 'Brake inspection and repair',
                'order' => 8,
                'is_active' => true,
                'requires_photos' => true,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'shield',
                'color' => 'red',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Fluids',
                'slug' => 'fluids',
                'description' => 'Oil, coolant, brake fluid, etc.',
                'order' => 9,
                'is_active' => true,
                'requires_photos' => false,
                'requires_vendor' => true,
                'requires_cost' => true,
                'icon' => 'droplet',
                'color' => 'blue',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_categories');
    }
};
