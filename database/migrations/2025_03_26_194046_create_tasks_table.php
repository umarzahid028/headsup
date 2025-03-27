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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_stage_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'pass', 'repair', 'replace', 'completed', 'failed'])->default('pending');
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_internal')->default(true); // Whether this is done in-house or by vendor
            $table->boolean('is_vendor_visible')->default(false); // Whether this is visible to vendors
            
            // Timing
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('due_date')->nullable();
            
            // Cost tracking
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('cost_type')->nullable(); // parts, labor, etc.
            $table->text('cost_notes')->nullable();
            
            // Metadata
            $table->boolean('has_photos')->default(false);
            $table->integer('photo_count')->default(0);
            $table->boolean('requires_approval')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
