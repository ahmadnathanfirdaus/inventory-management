<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_item_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'transaction_item_code',
        'transaction_code',
        'product_code',
        'quantity',
        'sub_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'sub_total' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->transaction_item_code)) {
                $item->transaction_item_code = static::generateNextCode();
            }
        });
    }

    /**
     * Generate next transaction item code with pattern TI0001
     */
    public static function generateNextCode()
    {
        $lastItem = static::orderBy('transaction_item_code', 'desc')->first();

        if (!$lastItem) {
            return 'TI0001';
        }

        // Extract number from last code (e.g., TI0001 -> 1)
        $lastNumber = (int) substr($lastItem->transaction_item_code, 2);
        $nextNumber = $lastNumber + 1;

        // Format with leading zeros
        return 'TI' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the transaction that owns the transaction item.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_code', 'transaction_code');
    }

    /**
     * Get the product that owns the transaction item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }

    /**
     * Accessor for unit_price (maps to calculated value from product)
     */
    public function getUnitPriceAttribute()
    {
        if ($this->attributes['sub_total'] && $this->quantity > 0) {
            return $this->attributes['sub_total'] / $this->quantity;
        }
        return $this->product->product_price ?? 0;
    }

    /**
     * Accessor for subtotal (maps to sub_total column)
     */
    public function getSubtotalAttribute()
    {
        return $this->attributes['sub_total'] ?? 0;
    }
}
