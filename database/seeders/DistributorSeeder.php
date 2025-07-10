<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Distributor;

class DistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Distributor::create([
            'distributor_code' => 'DS0001',
            'distributor_name' => 'PT. Erafone Artha Retailindo',
            'phone' => '021-1234567',
            'address' => 'Jakarta, Indonesia'
        ]);

        Distributor::create([
            'distributor_code' => 'DS0002',
            'distributor_name' => 'PT. Global Teleshop',
            'phone' => '021-2345678',
            'address' => 'Surabaya, Indonesia'
        ]);

        Distributor::create([
            'distributor_code' => 'DS0003',
            'distributor_name' => 'CV. Techno Gadget',
            'phone' => '022-3456789',
            'address' => 'Bandung, Indonesia'
        ]);
    }
}
