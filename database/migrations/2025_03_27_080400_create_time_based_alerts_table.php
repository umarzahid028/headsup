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
        Schema::create('time_based_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alert_type'); // 'vehicle_stage', 'task_deadline', etc.
            $table->morphs('alertable'); // Can be a vehicle, task, etc.
            
            // Time thresholds in hours
            $table->integer('warning_threshold')->default(24); // yellow
            $table->integer('critical_threshold')->default(48); // red
            
            $table->boolean('is_active')->default(true);
            $table->timestamp('triggered_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_based_alerts');
    }
};
