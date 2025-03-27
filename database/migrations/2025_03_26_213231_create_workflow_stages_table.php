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
        // First check if the table already exists
        if (Schema::hasTable('workflow_stages')) {
            return; // Table already exists, so skip this migration
        }
        
        Schema::create('workflow_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(false);
            $table->integer('target_days')->default(1);
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });
        
        // Add initial workflow stages
        $stages = [
            [
                'name' => 'Intake',
                'slug' => 'intake',
                'description' => 'Vehicle intake and initial processing',
                'order' => 1,
                'is_active' => true,
                'is_required' => true,
                'target_days' => 1,
                'icon' => 'inbox',
                'color' => 'gray'
            ],
            [
                'name' => 'Test Drive',
                'slug' => 'test_drive',
                'description' => 'Test drive and initial mechanical inspection',
                'order' => 2,
                'is_active' => true,
                'is_required' => true,
                'target_days' => 1,
                'icon' => 'truck',
                'color' => 'blue'
            ],
            [
                'name' => 'Mechanical',
                'slug' => 'mechanical',
                'description' => 'Mechanical repairs and service',
                'order' => 3,
                'is_active' => true,
                'is_required' => true,
                'target_days' => 3,
                'icon' => 'wrench',
                'color' => 'red'
            ],
            [
                'name' => 'Detail',
                'slug' => 'detail',
                'description' => 'Cleaning and detailing',
                'order' => 4,
                'is_active' => true,
                'is_required' => true,
                'target_days' => 1,
                'icon' => 'sparkles',
                'color' => 'green'
            ],
            [
                'name' => 'Photos',
                'slug' => 'photos',
                'description' => 'Take photos for website listing',
                'order' => 5,
                'is_active' => true,
                'is_required' => true,
                'target_days' => 1,
                'icon' => 'camera',
                'color' => 'purple'
            ],
            [
                'name' => 'Frontline',
                'slug' => 'frontline',
                'description' => 'Ready for sale',
                'order' => 6,
                'is_active' => true,
                'is_required' => true,
                'target_days' => 1,
                'icon' => 'check',
                'color' => 'green'
            ],
        ];
        
        DB::table('workflow_stages')->insert($stages);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_stages');
    }
};
