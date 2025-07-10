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
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_code', 7)->primary();
            $table->string('product_name', 40);
            $table->string('brand_code', 7);
            $table->string('distributor_code', 7);
            $table->date('entry_date');
            $table->integer('product_price', false, true); // unsigned integer
            $table->integer('stock_quantity', false, true); // unsigned integer
            $table->string('image', 255);
            $table->string('description', 200);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('brand_code')->references('brand_code')->on('brands')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('distributor_code')->references('distributor_code')->on('distributors')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
