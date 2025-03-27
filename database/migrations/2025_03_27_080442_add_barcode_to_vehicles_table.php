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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('barcode')->nullable()->after('vin');
            $table->timestamp('barcode_generated_at')->nullable()->after('barcode');
            $table->string('barcode_image_path')->nullable()->after('barcode_generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'barcode_generated_at', 'barcode_image_path']);
        });
    }
};
