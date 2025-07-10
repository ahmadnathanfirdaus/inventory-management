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
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->string('receipt_code', 10)->primary();
            $table->string('po_code', 10);
            $table->string('product_code', 7);
            $table->integer('received_quantity', false, true);
            $table->integer('actual_price', false, true);
            $table->date('received_date');
            $table->string('admin_code', 7);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['po_code']);
            $table->index(['product_code']);
            $table->index(['admin_code']);
            $table->index(['received_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
