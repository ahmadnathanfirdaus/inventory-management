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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->string('po_code', 10)->primary();
            $table->string('order_code', 10);
            $table->string('po_number', 20)->unique();
            $table->date('po_date');
            $table->string('manager_code', 7);
            $table->enum('status', ['printed', 'completed', 'cancelled'])->default('printed');
            $table->bigInteger('total_estimated')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index(['order_code']);
            $table->index(['manager_code']);
            $table->index(['status']);
            $table->index(['po_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
