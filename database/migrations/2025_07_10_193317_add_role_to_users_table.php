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
        Schema::table('users', function (Blueprint $table) {
            // Add custom user code
            $table->string('user_code', 7)->unique()->after('id');
            $table->string('user_name', 20)->after('user_code');
            $table->string('username', 25)->unique()->after('user_name');
            $table->string('user_photo', 50)->nullable()->after('username');
            $table->enum('role', ['admin', 'cashier', 'manager'])->default('admin')->after('user_photo');

            // Keep original fields for Laravel compatibility
            $table->string('name')->change();
            $table->string('email')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_code', 'user_name', 'username', 'user_photo', 'role']);
        });
    }
};
