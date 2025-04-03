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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type')->default('staff')->after('email'); // staff, transporter, vendor
            $table->foreignId('transporter_id')->nullable()->after('user_type')
                  ->constrained()->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->after('transporter_id')
                  ->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['transporter_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['user_type', 'transporter_id', 'vendor_id', 'is_active']);
        });
    }
};
