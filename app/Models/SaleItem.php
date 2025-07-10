<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'product_sku',
        'unit_price',
        'discount_price',
        'quantity',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Sale this item belongs to
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Product this item represents
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate total price
     */
    public function calculateTotalPrice(): float
    {
        $price = $this->discount_price ?: $this->unit_price;
        return $price * $this->quantity;
    }

    /**
     * Get effective price (with discount if applicable)
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->discount_price ?: $this->unit_price;
    }

    /**
     * Get discount amount per item
     */
    public function getDiscountAmountAttribute(): float
    {
        if ($this->discount_price) {
            return $this->unit_price - $this->discount_price;
        }
        return 0;
    }

    /**
     * Get total discount for this line item
     */
    public function getTotalDiscountAttribute(): float
    {
        return $this->discount_amount * $this->quantity;
    }
}
