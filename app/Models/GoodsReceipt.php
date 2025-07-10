<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $primaryKey = 'receipt_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'receipt_code',
        'po_code',
        'product_code',
        'received_quantity',
        'actual_price',
        'received_date',
        'admin_code',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'date',
        'received_quantity' => 'integer',
        'actual_price' => 'integer',
    ];

    /**
     * Get the purchase order that owns the goods receipt.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_code', 'po_code');
    }

    /**
     * Get the product that was received.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }

    /**
     * Get the admin that received the goods.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_code', 'user_code');
    }

    /**
     * Generate next receipt code with pattern GR0001
     */
    public static function generateNextCode()
    {
        return DB::transaction(function () {
            $lastReceipt = static::lockForUpdate()->orderBy('receipt_code', 'desc')->first();

            if (!$lastReceipt) {
                return 'GR0001';
            }

            // Extract number from last code (e.g., GR0001 -> 1)
            $lastNumber = (int) substr($lastReceipt->receipt_code, 2);
            $nextNumber = $lastNumber + 1;

            // Format with leading zeros
            return 'GR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Get the product name for this goods receipt
     */
    public function getProductNameAttribute()
    {
        // Try direct product relationship first
        if ($this->product && $this->product->product_name) {
            return $this->product->product_name;
        }

        return 'Product Code: ' . $this->product_code;
    }
}
