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
        Schema::create('order_request_items', function (Blueprint $table) {
            $table->string('order_item_code', 10)->primary();
            $table->string('order_code', 10);
            $table->string('product_code', 7)->nullable();
            $table->string('custom_product_name', 100)->nullable();
            $table->string('brand_code', 7);
            $table->string('distributor_code', 7);
            $table->integer('order_quantity', false, true);
            $table->integer('estimated_price')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['order_code']);
            $table->index(['product_code']);
            $table->index(['brand_code']);
            $table->index(['distributor_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_request_items');
    }
};
