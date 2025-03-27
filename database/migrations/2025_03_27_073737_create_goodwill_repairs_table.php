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
        Schema::create('goodwill_repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            
            // Legal waiver information
            $table->boolean('waiver_signed')->default(false);
            $table->timestamp('waiver_signed_at')->nullable();
            $table->string('signature_data')->nullable(); // JSON data for signature
            $table->string('signature_ip')->nullable();
            $table->string('waiver_pdf_path')->nullable();
            
            // Communication tracking
            $table->boolean('sms_sent')->default(false);
            $table->timestamp('sms_sent_at')->nullable();
            $table->string('sms_status')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            
            // Notes and creation tracking
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goodwill_repairs');
    }
};
