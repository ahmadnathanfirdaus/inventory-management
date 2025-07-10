<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $primaryKey = 'po_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'po_code',
        'order_code',
        'po_number',
        'po_date',
        'manager_code',
        'status',
        'total_estimated',
    ];

    protected $casts = [
        'po_date' => 'date',
        'total_estimated' => 'integer',
    ];

    /**
     * Get the order request that owns the purchase order.
     */
    public function orderRequest()
    {
        return $this->belongsTo(OrderRequest::class, 'order_code', 'order_code');
    }

    /**
     * Get the manager that created the purchase order.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_code', 'user_code');
    }

    /**
     * Get the goods receipts for this purchase order.
     */
    public function goodsReceipts()
    {
        return $this->hasMany(GoodsReceipt::class, 'po_code', 'po_code');
    }

    /**
     * Check if this purchase order has any goods receipts
     */
    public function hasGoodsReceipts()
    {
        return $this->goodsReceipts()->exists();
    }

    /**
     * Generate next purchase order code with pattern PO0001
     */
    public static function generateNextCode()
    {
        return DB::transaction(function () {
            $lastPO = static::lockForUpdate()->orderBy('po_code', 'desc')->first();

            if (!$lastPO) {
                return 'PO0001';
            }

            // Extract number from last code (e.g., PO0001 -> 1)
            $lastNumber = (int) substr($lastPO->po_code, 2);
            $nextNumber = $lastNumber + 1;

            // Format with leading zeros
            return 'PO' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}
