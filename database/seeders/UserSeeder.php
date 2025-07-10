<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        \App\Models\User::create([
            'user_code' => 'USR001',
            'user_name' => 'Admin User',
            'username' => 'admin',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'user_photo' => 'default.jpg',
            'email_verified_at' => now(),
        ]);

        // Create Manager User
        \App\Models\User::create([
            'user_code' => 'USR002',
            'user_name' => 'Manager User',
            'username' => 'manager',
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'user_photo' => 'default.jpg',
            'email_verified_at' => now(),
        ]);

        // Create Cashier User
        \App\Models\User::create([
            'user_code' => 'USR003',
            'user_name' => 'Cashier User',
            'username' => 'cashier',
            'name' => 'Cashier User',
            'email' => 'cashier@example.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
            'user_photo' => 'default.jpg',
            'email_verified_at' => now(),
        ]);
    }
}
