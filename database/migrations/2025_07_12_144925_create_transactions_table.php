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
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('transaction_code', 7)->primary();
            $table->string('user_code', 7);
            $table->integer('total_quantity', false, true); // unsigned integer
            $table->integer('total_price', false, true); // unsigned integer
            $table->date('purchase_date');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_code')->references('user_code')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
