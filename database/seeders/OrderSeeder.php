<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderRequest;
use App\Models\OrderRequestItem;
use App\Models\User;
use App\Models\Product;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users
        $admin = User::where('role', 'admin')->first();
        $manager = User::where('role', 'manager')->first();

        if (!$admin || !$manager) {
            $this->command->warn('Admin or manager user not found. Please run UserSeeder first.');
            return;
        }

        // Get some products for the items
        $products = Product::take(3)->get();
        if ($products->count() < 3) {
            $this->command->warn('Not enough products found. Please run ProductSeeder first.');
            return;
        }

        // Create approved order 1
        $approvedOrder1 = OrderRequest::create([
            'order_code' => 'ORD0001',
            'admin_code' => $admin->user_code,
            'order_date' => now()->subDays(5)->format('Y-m-d'),
            'notes' => 'Pembelian alat tulis kantor dan supplies',
            'status' => 'approved',
            'manager_code' => $manager->user_code,
            'approval_date' => now()->subDays(4)->format('Y-m-d'),
        ]);

        // Add items for approved order 1
        OrderRequestItem::create([
            'order_item_code' => 'ORI0001',
            'order_code' => $approvedOrder1->order_code,
            'product_code' => $products[0]->product_code,
            'brand_code' => $products[0]->brand_code,
            'distributor_code' => $products[0]->distributor_code,
            'order_quantity' => 10,
            'estimated_price' => 150000,
            'notes' => 'Untuk kebutuhan kantor',
        ]);

        OrderRequestItem::create([
            'order_item_code' => 'ORI0002',
            'order_code' => $approvedOrder1->order_code,
            'product_code' => $products[1]->product_code,
            'brand_code' => $products[1]->brand_code,
            'distributor_code' => $products[1]->distributor_code,
            'order_quantity' => 5,
            'estimated_price' => 250000,
        ]);

        // Create approved order 2
        $approvedOrder2 = OrderRequest::create([
            'order_code' => 'ORD0002',
            'admin_code' => $admin->user_code,
            'order_date' => now()->subDays(3)->format('Y-m-d'),
            'notes' => 'Pembelian peralatan elektronik',
            'status' => 'approved',
            'manager_code' => $manager->user_code,
            'approval_date' => now()->subDays(2)->format('Y-m-d'),
        ]);

        // Add items for approved order 2
        OrderRequestItem::create([
            'order_item_code' => 'ORI0003',
            'order_code' => $approvedOrder2->order_code,
            'product_code' => $products[2]->product_code,
            'brand_code' => $products[2]->brand_code,
            'distributor_code' => $products[2]->distributor_code,
            'order_quantity' => 2,
            'estimated_price' => 500000,
        ]);

        // Create pending order 1
        $pendingOrder1 = OrderRequest::create([
            'order_code' => 'ORD0003',
            'admin_code' => $admin->user_code,
            'order_date' => now()->subDays(1)->format('Y-m-d'),
            'notes' => 'Pembelian furniture kantor',
            'status' => 'pending',
        ]);

        // Add items for pending order 1
        OrderRequestItem::create([
            'order_item_code' => 'ORI0004',
            'order_code' => $pendingOrder1->order_code,
            'product_code' => $products[0]->product_code,
            'brand_code' => $products[0]->brand_code,
            'distributor_code' => $products[0]->distributor_code,
            'order_quantity' => 3,
            'estimated_price' => 750000,
        ]);

        // Create pending order 2
        $pendingOrder2 = OrderRequest::create([
            'order_code' => 'ORD0004',
            'admin_code' => $admin->user_code,
            'order_date' => now()->format('Y-m-d'),
            'notes' => 'Pembelian peralatan maintenance',
            'status' => 'pending',
        ]);

        // Add items for pending order 2
        OrderRequestItem::create([
            'order_item_code' => 'ORI0005',
            'order_code' => $pendingOrder2->order_code,
            'product_code' => $products[1]->product_code,
            'brand_code' => $products[1]->brand_code,
            'distributor_code' => $products[1]->distributor_code,
            'order_quantity' => 1,
            'estimated_price' => 300000,
        ]);

        // Create rejected order
        $rejectedOrder = OrderRequest::create([
            'order_code' => 'ORD0005',
            'admin_code' => $admin->user_code,
            'order_date' => now()->subDays(7)->format('Y-m-d'),
            'notes' => 'Pembelian equipment baru',
            'status' => 'rejected',
            'manager_code' => $manager->user_code,
            'rejection_reason' => 'Budget tidak mencukupi untuk periode ini',
        ]);

        // Add items for rejected order
        OrderRequestItem::create([
            'order_item_code' => 'ORI0006',
            'order_code' => $rejectedOrder->order_code,
            'product_code' => $products[2]->product_code,
            'brand_code' => $products[2]->brand_code,
            'distributor_code' => $products[2]->distributor_code,
            'order_quantity' => 1,
            'estimated_price' => 1000000,
        ]);

        $this->command->info('Order requests seeded successfully!');
    }
}
