<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'product_code' => 'PROD001',
            'product_name' => 'Samsung Galaxy S24',
            'brand_code' => 'BR0001',
            'distributor_code' => 'DS0001',
            'entry_date' => '2025-07-01',
            'product_price' => 15000000,
            'stock_quantity' => 100,
            'image' => 'samsung-s24.jpg',
            'description' => 'Samsung Galaxy S24 128GB'
        ]);

        Product::create([
            'product_code' => 'PROD002',
            'product_name' => 'iPhone 15 Pro',
            'brand_code' => 'BR0002',
            'distributor_code' => 'DS0002',
            'entry_date' => '2025-07-02',
            'product_price' => 18000000,
            'stock_quantity' => 75,
            'image' => 'iphone-15-pro.jpg',
            'description' => 'iPhone 15 Pro 256GB'
        ]);

        Product::create([
            'product_code' => 'PROD003',
            'product_name' => 'Xiaomi 14 Ultra',
            'brand_code' => 'BR0003',
            'distributor_code' => 'DS0003',
            'entry_date' => '2025-07-03',
            'product_price' => 12000000,
            'stock_quantity' => 50,
            'image' => 'xiaomi-14-ultra.jpg',
            'description' => 'Xiaomi 14 Ultra 512GB'
        ]);
    }
}
