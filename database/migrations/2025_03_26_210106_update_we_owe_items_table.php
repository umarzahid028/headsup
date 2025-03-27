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
        Schema::table('we_owe_items', function (Blueprint $table) {
            // Add new columns instead of trying to rename
            if (!Schema::hasColumn('we_owe_items', 'details')) {
                $table->text('details')->nullable();
            }
            
            if (!Schema::hasColumn('we_owe_items', 'notes')) {
                $table->text('notes')->nullable();
            }
            
            if (!Schema::hasColumn('we_owe_items', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users');
            }
            
            if (!Schema::hasColumn('we_owe_items', 'status')) {
                $table->string('status')->default('pending');
            }
            
            if (!Schema::hasColumn('we_owe_items', 'due_date')) {
                $table->date('due_date')->nullable();
            }
            
            if (!Schema::hasColumn('we_owe_items', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('we_owe_items', function (Blueprint $table) {
            $table->dropColumn([
                'details',
                'notes',
                'status',
                'due_date',
                'completed_at'
            ]);
            
            if (Schema::hasColumn('we_owe_items', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
