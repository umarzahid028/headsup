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
    Schema::create('customer_sales', function (Blueprint $table) {
        $table->id();
         $table->foreignId('user_id')
          ->nullable()         
          ->constrained()       
          ->nullOnDelete(); 
        $table->string('name');
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->string('interest')->nullable();
        $table->text('notes')->nullable();
        $table->json('process')->nullable();
        $table->string('disposition')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sales');
    }
};
