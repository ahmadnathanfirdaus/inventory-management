<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class OrderRequestItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_item_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'order_item_code',
        'order_code',
        'product_code',
        'custom_product_name',
        'brand_code',
        'distributor_code',
        'order_quantity',
        'estimated_price',
        'notes',
    ];

    protected $casts = [
        'order_quantity' => 'integer',
        'estimated_price' => 'integer',
    ];

    /**
     * Accessors to append to JSON serialization
     */
    protected $appends = [
        'item_name',
        'quantity',
        'unit_price',
        'total_price',
    ];

    /**
     * Get the order request that owns the item.
     */
    public function orderRequest()
    {
        return $this->belongsTo(OrderRequest::class, 'order_code', 'order_code');
    }

    /**
     * Get the product (if existing product).
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }

    /**
     * Get the brand.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_code', 'brand_code');
    }

    /**
     * Get the distributor.
     */
    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_code', 'distributor_code');
    }

    /**
     * Generate the next order item code.
     *
     * @return string
     */
    public static function generateNextCode()
    {
        return DB::transaction(function () {
            $latestItem = static::lockForUpdate()->orderBy('order_item_code', 'desc')->first();

            if (!$latestItem) {
                return 'ORI0001';
            }

            // Extract number from last code (e.g., ORI0001 -> 1)
            $lastNumber = (int) substr($latestItem->order_item_code, 3);
            $nextNumber = $lastNumber + 1;

            // Format with leading zeros
            return 'ORI' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Accessor for item_name - uses custom_product_name or product name
     */
    public function getItemNameAttribute()
    {
        return $this->custom_product_name ?? $this->product?->product_name ?? 'Unknown Product';
    }

    /**
     * Accessor for quantity - maps to order_quantity
     */
    public function getQuantityAttribute()
    {
        return $this->order_quantity;
    }

    /**
     * Accessor for unit_price - maps to estimated_price
     */
    public function getUnitPriceAttribute()
    {
        return $this->estimated_price;
    }

    /**
     * Accessor for total_price - calculates quantity * unit_price
     */
    public function getTotalPriceAttribute()
    {
        return $this->order_quantity * $this->estimated_price;
    }
}
