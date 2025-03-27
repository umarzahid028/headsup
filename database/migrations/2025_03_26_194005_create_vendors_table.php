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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // transporter, mechanic, detailer, etc.
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->json('service_areas')->nullable(); // JSON array of locations
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->boolean('is_transporter')->default(false);
            $table->boolean('is_default_for_location')->default(false);
            $table->string('default_location')->nullable(); // If this vendor is default for a specific location
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
