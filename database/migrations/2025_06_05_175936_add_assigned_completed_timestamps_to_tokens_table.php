<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('tokens', function (Blueprint $table) {
        $table->timestamp('assigned_at')->nullable()->after('status');
        $table->timestamp('completed_at')->nullable()->after('assigned_at');
    });
}

public function down()
{
    Schema::table('tokens', function (Blueprint $table) {
        $table->dropColumn(['assigned_at', 'completed_at']);
    });
}

};
