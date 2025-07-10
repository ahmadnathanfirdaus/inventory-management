<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::create([
            'brand_code' => 'BR0001',
            'brand_name' => 'Samsung',
            'brand_photo' => 'samsung.jpg'
        ]);

        Brand::create([
            'brand_code' => 'BR0002',
            'brand_name' => 'Apple',
            'brand_photo' => 'apple.jpg'
        ]);

        Brand::create([
            'brand_code' => 'BR0003',
            'brand_name' => 'Xiaomi',
            'brand_photo' => 'xiaomi.jpg'
        ]);
    }
}
