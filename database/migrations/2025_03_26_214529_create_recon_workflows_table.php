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
        if (Schema::hasTable('recon_workflows')) {
            return;
        }
        
        Schema::create('recon_workflows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->string('status')->default('in_progress'); // in_progress, completed, on_hold
            $table->unsignedBigInteger('started_by');
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->integer('completed_items')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('has_arbitration_issues')->default(false);
            $table->json('diagrams')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Only add foreign key constraints if tables exist
            if (Schema::hasTable('vehicles')) {
                $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
            }
            
            if (Schema::hasTable('users')) {
                $table->foreign('started_by')->references('id')->on('users');
                $table->foreign('completed_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recon_workflows');
    }
};
