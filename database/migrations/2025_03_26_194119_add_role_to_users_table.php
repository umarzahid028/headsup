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
            $table->enum('role', ['admin', 'sales_manager', 'staff', 'vendor', 'transporter'])->default('staff')->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('phone');
            $table->foreignId('vendor_id')->nullable()->after('is_active')->constrained()->nullOnDelete();
            $table->json('notification_preferences')->nullable()->after('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_id');
            $table->dropColumn(['role', 'phone', 'is_active', 'notification_preferences']);
        });
    }
};
