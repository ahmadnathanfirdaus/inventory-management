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
        // Add foreign keys for order_requests
        Schema::table('order_requests', function (Blueprint $table) {
            $table->foreign('admin_code')->references('user_code')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('manager_code')->references('user_code')->on('users')->onUpdate('cascade')->onDelete('set null');
        });

        // Add foreign keys for order_request_items
        Schema::table('order_request_items', function (Blueprint $table) {
            $table->foreign('order_code')->references('order_code')->on('order_requests')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_code')->references('product_code')->on('products')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('brand_code')->references('brand_code')->on('brands')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('distributor_code')->references('distributor_code')->on('distributors')->onUpdate('cascade')->onDelete('restrict');
        });

        // Add foreign keys for purchase_orders
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreign('order_code')->references('order_code')->on('order_requests')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('manager_code')->references('user_code')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });

        // Add foreign keys for goods_receipts
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->foreign('po_code')->references('po_code')->on('purchase_orders')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('product_code')->references('product_code')->on('products')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('admin_code')->references('user_code')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys in reverse order
        Schema::table('goods_receipts', function (Blueprint $table) {
            $table->dropForeign(['po_code', 'product_code', 'admin_code']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['order_code', 'manager_code']);
        });

        Schema::table('order_request_items', function (Blueprint $table) {
            $table->dropForeign(['order_code', 'product_code', 'brand_code', 'distributor_code']);
        });

        Schema::table('order_requests', function (Blueprint $table) {
            $table->dropForeign(['admin_code', 'manager_code']);
        });
    }
};
