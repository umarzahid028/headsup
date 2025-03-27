<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            
            // Vehicle Information
            $table->string('vin')->unique()->index();
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('stock_number')->nullable()->unique();
            $table->string('color')->nullable();
            $table->string('trim')->nullable();
            $table->integer('mileage')->nullable();
            
            // Purchase Information
            $table->string('purchased_from')->nullable();
            $table->string('purchase_location')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->timestamp('purchase_date')->nullable();
            $table->boolean('is_arbitrable')->default(false);
            
            // Status Tracking
            $table->string('current_stage')->default('intake');
            $table->timestamp('stage_updated_at')->nullable();
            $table->boolean('is_frontline_ready')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_sold')->default(false);
            
            // Transport Information
            $table->foreignId('transporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('transport_assigned_at')->nullable();
            $table->timestamp('transport_expected_at')->nullable();
            $table->timestamp('check_in_date')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
