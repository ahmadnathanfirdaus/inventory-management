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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->string('transaction_item_code', 7)->primary();
            $table->string('transaction_code', 7);
            $table->string('product_code', 11);
            $table->integer('quantity', false, true); // unsigned integer
            $table->integer('sub_total', false, true); // unsigned integer
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('transaction_code')->references('transaction_code')->on('transactions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_code')->references('product_code')->on('products')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
