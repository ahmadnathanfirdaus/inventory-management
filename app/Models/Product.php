<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_code',
        'product_name',
        'brand_code',
        'distributor_code',
        'entry_date',
        'product_price',
        'stock_quantity',
        'image',
        'description',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'product_code';
    }

    protected $casts = [
        'entry_date' => 'date',
        'product_price' => 'integer',
        'stock_quantity' => 'integer',
    ];

    /**
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_code', 'brand_code');
    }

    /**
     * Get the distributor that owns the product.
     */
    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_code', 'distributor_code');
    }

    /**
     * Get the transaction items for the product.
     */
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class, 'product_code', 'product_code');
    }

    /**
     * Scope a query to only include products with low stock.
     */
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('stock_quantity', '<', $threshold);
    }

    /**
     * Scope a query to only include products that are out of stock.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '=', 0);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Generate next product code with pattern PR0001
     */
    public static function generateNextCode()
    {
        return DB::transaction(function () {
            $lastProduct = static::lockForUpdate()->orderBy('product_code', 'desc')->first();

            if (!$lastProduct) {
                return 'PR0001';
            }

            // Extract number from last code (e.g., PR0001 -> 1)
            $lastNumber = (int) substr($lastProduct->product_code, 2);
            $nextNumber = $lastNumber + 1;

            // Format with leading zeros
            return 'PR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Accessor for name (maps to product_name column)
     */
    public function getNameAttribute()
    {
        return $this->product_name;
    }

    /**
     * Accessor for selling_price (maps to product_price column)
     */
    public function getSellingPriceAttribute()
    {
        return $this->product_price;
    }
}
