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
        if (Schema::hasTable('inspection_items')) {
            return;
        }
        
        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, pass, repair, replace
            $table->decimal('cost', 10, 2)->nullable();
            $table->boolean('is_vendor_visible')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('recon_workflow_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Only add foreign key constraints if tables exist
            if (Schema::hasTable('inspection_categories')) {
                $table->foreign('category_id')->references('id')->on('inspection_categories')->cascadeOnDelete();
            }
            
            if (Schema::hasTable('vehicles')) {
                $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
            }
            
            if (Schema::hasTable('recon_workflows')) {
                $table->foreign('recon_workflow_id')->references('id')->on('recon_workflows')->cascadeOnDelete();
            }
            
            if (Schema::hasTable('vendors')) {
                $table->foreign('assigned_to')->references('id')->on('vendors')->nullOnDelete();
            }
            
            if (Schema::hasTable('users')) {
                $table->foreign('completed_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
    }
};
