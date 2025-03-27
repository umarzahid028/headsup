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
        Schema::create('we_owe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['we_owe', 'goodwill'])->default('we_owe');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('promised_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('has_waiver')->default(false); // For goodwill repairs
            $table->boolean('waiver_signed')->default(false);
            $table->timestamp('waiver_signed_at')->nullable();
            $table->boolean('sms_sent')->default(false);
            $table->timestamp('sms_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('we_owe_items');
    }
};
