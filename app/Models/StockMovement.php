<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
    ];

    /**
     * Product this movement belongs to
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * User who made this movement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Reference model (morphed)
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Scope for stock in movements
     */
    public function scopeStockIn($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope for stock out movements
     */
    public function scopeStockOut($query)
    {
        return $query->where('type', 'out');
    }

    /**
     * Scope for adjustments
     */
    public function scopeAdjustments($query)
    {
        return $query->where('type', 'adjustment');
    }

    /**
     * Get movement type label
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'adjustment' => 'Adjustment',
            default => 'Unknown'
        };
    }

    /**
     * Get movement direction (+ or -)
     */
    public function getDirectionAttribute(): string
    {
        return $this->type === 'out' ? '-' : '+';
    }
}
