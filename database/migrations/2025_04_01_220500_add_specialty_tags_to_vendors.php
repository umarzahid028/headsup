<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add the new specialty_tags column
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('specialty_tags')->nullable()->after('address');
        });
        
        // Step 2: Copy data from type to specialty_tags
        DB::statement('UPDATE vendors SET specialty_tags = type');
        
        // Step 3: Drop the old type column and rename vendor_type_id
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->renameColumn('vendor_type_id', 'type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add back the type column
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('type')->nullable()->after('address');
        });
        
        // Step 2: Copy data back from specialty_tags to type
        DB::statement('UPDATE vendors SET type = specialty_tags');
        
        // Step 3: Drop specialty_tags and rename type_id back
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('specialty_tags');
            $table->renameColumn('type_id', 'vendor_type_id');
        });
    }
}; 