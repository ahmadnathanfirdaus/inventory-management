<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'transactions'; // Use existing transactions table
    protected $primaryKey = 'transaction_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'transaction_code',
        'user_code', // cashier
        'total_quantity',
        'total_price',
        'purchase_date',
    ];

    protected $casts = [
        'total_quantity' => 'integer',
        'total_price' => 'integer',
        'purchase_date' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->transaction_code)) {
                $sale->transaction_code = static::generateTransactionCode();
            }
        });
    }

    /**
     * Generate unique transaction code
     */
    public static function generateTransactionCode(): string
    {
        return 'TRX' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate next transaction code (alias for generateTransactionCode)
     */
    public static function generateNextCode(): string
    {
        return static::generateTransactionCode();
    }

    /**
     * Cashier who processed this sale
     */
    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_code', 'user_code');
    }

    /**
     * Items in this sale
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_code', 'transaction_code');
    }

    /**
     * Scope for completed sales (all transactions are considered completed)
     */
    public function scopeCompleted($query)
    {
        return $query; // All transactions are completed by default
    }

    /**
     * Scope for today's sales
     */
    public function scopeToday($query)
    {
        return $query->whereDate('purchase_date', today());
    }

    /**
     * Scope for sales in date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('purchase_date', [$startDate, $endDate]);
    }

    /**
     * Check if sale is completed (always true for transactions)
     */
    public function isCompleted(): bool
    {
        return true;
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute()
    {
        return $this->total_quantity;
    }

    /**
     * Get customer display name (for compatibility)
     */
    public function getCustomerDisplayNameAttribute()
    {
        return 'Walk-in Customer';
    }

    /**
     * Get total amount (alias for total_price)
     */
    public function getTotalAmountAttribute()
    {
        return $this->total_price;
    }

    /**
     * Get sale date (alias for purchase_date)
     */
    public function getSaleDateAttribute()
    {
        return $this->purchase_date;
    }

    /**
     * Get sale number (alias for transaction_code)
     */
    public function getSaleNumberAttribute()
    {
        return $this->transaction_code;
    }

    /**
     * Get status (always completed for transactions)
     */
    public function getStatusAttribute()
    {
        return 'completed';
    }
}
