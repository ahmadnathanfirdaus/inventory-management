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
        Schema::create('order_requests', function (Blueprint $table) {
            $table->string('order_code', 10)->primary();
            $table->string('admin_code', 7);
            $table->date('order_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('manager_code', 7)->nullable();
            $table->date('approval_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['admin_code']);
            $table->index(['manager_code']);
            $table->index(['status']);
            $table->index(['order_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_requests');
    }
};
