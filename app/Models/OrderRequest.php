<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class OrderRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'order_code',
        'admin_code',
        'order_date',
        'notes',
        'status',
        'manager_code',
        'approval_date',
        'rejection_reason',
    ];

    protected $casts = [
        'order_date' => 'date',
        'approval_date' => 'date',
    ];

    /**
     * Get the admin that created the order request.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_code', 'user_code');
    }

    /**
     * Get the manager that approved/rejected the order request.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_code', 'user_code');
    }

    /**
     * Get the order request items.
     */
    public function orderRequestItems()
    {
        return $this->hasMany(OrderRequestItem::class, 'order_code', 'order_code');
    }

    /**
     * Get the purchase order for this request.
     */
    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class, 'order_code', 'order_code');
    }

    /**
     * Generate next order code with pattern ORD0001
     */
    public static function generateNextCode()
    {
        return DB::transaction(function () {
            $lastOrder = static::lockForUpdate()->orderBy('order_code', 'desc')->first();

            if (!$lastOrder) {
                return 'ORD0001';
            }

            // Extract number from last code (e.g., ORD0001 -> 1)
            $lastNumber = (int) substr($lastOrder->order_code, 3);
            $nextNumber = $lastNumber + 1;

            // Format with leading zeros
            return 'ORD' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Get the total amount of all order request items.
     *
     * @return int
     */
    public function getTotalAmount()
    {
        return $this->orderRequestItems()
            ->selectRaw('SUM(order_quantity * estimated_price) as total')
            ->value('total') ?? 0;
    }

    /**
     * Check if this order request has any goods receipts
     */
    public function hasGoodsReceipts()
    {
        return $this->purchaseOrder && $this->purchaseOrder->hasGoodsReceipts();
    }

    /**
     * Check if this order request is fully received
     */
    public function isFullyReceived()
    {
        if (!$this->purchaseOrder) {
            return false;
        }

        $totalOrderQuantity = $this->orderRequestItems->sum('order_quantity');
        $totalReceivedQuantity = $this->purchaseOrder->goodsReceipts->sum('received_quantity');

        return $totalReceivedQuantity >= $totalOrderQuantity;
    }

    /**
     * Get remaining quantities for partially received orders
     */
    public function getRemainingQuantities()
    {
        if (!$this->purchaseOrder) {
            return [];
        }

        // Get all received quantities by product code
        $receivedQuantities = [];
        foreach ($this->purchaseOrder->goodsReceipts as $receipt) {
            $productCode = $receipt->product_code;
            if (!isset($receivedQuantities[$productCode])) {
                $receivedQuantities[$productCode] = 0;
            }
            $receivedQuantities[$productCode] += $receipt->received_quantity;
        }

        // Calculate remaining quantities
        $remainingQuantities = [];
        foreach ($this->orderRequestItems as $item) {
            $received = $receivedQuantities[$item->product_code] ?? 0;
            $remaining = $item->order_quantity - $received;

            // Always include the product in the result, even if remaining is 0
            $remainingQuantities[$item->product_code] = [
                'ordered' => $item->order_quantity,
                'received' => $received,
                'remaining' => max(0, $remaining), // Ensure remaining is never negative
            ];
        }

        return $remainingQuantities;
    }

    /**
     * Scope to get orders that are not fully received yet
     */
    public function scopeWithoutGoodsReceipts($query)
    {
        return $query->whereHas('purchaseOrder')
            ->where('status', 'approved')
            ->where(function ($subQuery) {
                $subQuery->whereDoesntHave('purchaseOrder.goodsReceipts')
                    ->orWhereHas('purchaseOrder', function ($poQuery) {
                        $poQuery->whereRaw('
                            (SELECT SUM(received_quantity) FROM goods_receipts WHERE po_code = purchase_orders.po_code) <
                            (SELECT SUM(order_quantity) FROM order_request_items WHERE order_code = purchase_orders.order_code)
                        ');
                    });
            });
    }
}
