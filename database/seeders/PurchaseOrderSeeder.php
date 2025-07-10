<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderRequest;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceived;
use App\Models\User;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('role', 'admin')->first();

        // Get approved orders
        $approvedOrders = OrderRequest::where('status', 'approved')->get();

        foreach ($approvedOrders as $order) {
            // Create purchase order
            $po = PurchaseOrder::create([
                'order_id' => $order->id,
                'po_number' => 'PO-' . date('Ymd') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                'status' => 'completed',
                'total_amount' => $order->getTotalAmount(),
                'supplier_info' => $this->getRandomVendor() . "\nContact: " . $this->getRandomContact(),
                'expected_delivery_date' => now()->addDays(rand(1, 7)),
            ]);

            // Create goods received for each order item
            foreach ($order->items as $item) {
                $quantityReceived = $item->quantity;
                $status = 'complete';

                // Randomly make some items incomplete
                if (rand(1, 10) <= 2) { // 20% chance
                    $quantityReceived = $item->quantity - rand(1, 2);
                    $status = 'incomplete';
                }

                GoodsReceived::create([
                    'purchase_order_id' => $po->id,
                    'order_item_id' => $item->id,
                    'item_name' => $item->item_name,
                    'quantity_ordered' => $item->quantity,
                    'quantity_received' => $quantityReceived,
                    'quantity_shortage' => $item->quantity - $quantityReceived,
                    'status' => $status,
                    'notes' => $status === 'incomplete' ? 'Barang diterima tidak lengkap, akan diikuti pengiriman berikutnya' : 'Barang diterima dengan kondisi baik',
                    'received_by' => $admin->id,
                    'received_at' => now()->subDays(rand(0, 2)),
                ]);
            }
        }
    }

    private function getRandomVendor(): string
    {
        $vendors = [
            'PT Sumber Makmur',
            'CV Jaya Abadi',
            'PT Mitra Sejahtera',
            'Toko Elektronik Maju',
            'PT Furniture Indonesia',
            'CV Alat Tulis Kantor',
        ];

        return $vendors[array_rand($vendors)];
    }

    private function getRandomContact(): string
    {
        $contacts = [
            'Ahmad (0812-3456-7890)',
            'Siti (0813-4567-8901)',
            'Budi (0814-5678-9012)',
            'Dewi (0815-6789-0123)',
            'Eko (0816-7890-1234)',
        ];

        return $contacts[array_rand($contacts)];
    }
}
