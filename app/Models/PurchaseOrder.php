<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'po_number',
        'status',
        'total_amount',
        'supplier_info',
        'expected_delivery_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'expected_delivery_date' => 'date',
    ];

    /**
     * Generate unique PO number
     */
    public static function generatePONumber(): string
    {
        return 'PO-' . date('Ymd') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Order this PO belongs to
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Goods received for this PO
     */
    public function goodsReceived()
    {
        return $this->hasMany(GoodsReceived::class);
    }

    /**
     * Check if PO is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if PO is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if PO is partial
     */
    public function isPartial(): bool
    {
        return $this->status === 'partial';
    }
}
