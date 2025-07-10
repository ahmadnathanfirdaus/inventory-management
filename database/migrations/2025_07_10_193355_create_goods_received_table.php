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
        Schema::create('goods_received', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders');
            $table->foreignId('order_item_id')->constrained('order_items');
            $table->string('item_name');
            $table->integer('quantity_ordered');
            $table->integer('quantity_received');
            $table->integer('quantity_shortage')->default(0);
            $table->enum('status', ['complete', 'incomplete', 'adjusted'])->default('complete');
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->datetime('received_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_received');
    }
};
