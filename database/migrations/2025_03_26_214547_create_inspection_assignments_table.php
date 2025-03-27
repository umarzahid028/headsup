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
        if (Schema::hasTable('inspection_assignments')) {
            return;
        }
        
        Schema::create('inspection_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspection_item_id');
            $table->unsignedBigInteger('vendor_id');
            $table->string('status')->default('pending'); // pending, in_progress, completed, rejected
            $table->decimal('quoted_cost', 10, 2)->nullable();
            $table->decimal('final_cost', 10, 2)->nullable();
            $table->dateTime('due_date')->nullable();
            $table->unsignedBigInteger('assigned_by');
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamp('assigned_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('vendor_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
            
            // Only add foreign key constraints if tables exist
            if (Schema::hasTable('inspection_items')) {
                $table->foreign('inspection_item_id')->references('id')->on('inspection_items')->cascadeOnDelete();
            }
            
            if (Schema::hasTable('vendors')) {
                $table->foreign('vendor_id')->references('id')->on('vendors');
            }
            
            if (Schema::hasTable('users')) {
                $table->foreign('assigned_by')->references('id')->on('users');
                $table->foreign('completed_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_assignments');
    }
};
