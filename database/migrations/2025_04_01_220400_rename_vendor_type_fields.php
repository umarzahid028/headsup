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
        Schema::table('vendors', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['vendor_type_id']);
            
            // Rename type to specialty_tags
            $table->renameColumn('type', 'specialty_tags');
            
            // Rename vendor_type_id to type_id
            $table->renameColumn('vendor_type_id', 'type_id');
            
            // Re-add the foreign key constraint with the new column name
            $table->foreign('type_id')
                  ->references('id')
                  ->on('vendor_types')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['type_id']);
            
            $table->renameColumn('specialty_tags', 'type');
            $table->renameColumn('type_id', 'vendor_type_id');
            
            // Re-add the foreign key constraint with the old column name
            $table->foreign('vendor_type_id')
                  ->references('id')
                  ->on('vendor_types')
                  ->nullOnDelete();
        });
    }
}; 