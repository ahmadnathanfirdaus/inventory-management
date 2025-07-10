<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoodsReceived extends Model
{
    use HasFactory;

    protected $table = 'goods_received';

    protected $fillable = [
        'purchase_order_id',
        'order_item_id',
        'item_name',
        'quantity_ordered',
        'quantity_received',
        'quantity_shortage',
        'status',
        'notes',
        'received_by',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    /**
     * Purchase order this goods received belongs to
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Order item this goods received belongs to
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * User who received the goods
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Check if goods received is complete
     */
    public function isComplete(): bool
    {
        return $this->status === 'complete';
    }

    /**
     * Check if goods received is incomplete
     */
    public function isIncomplete(): bool
    {
        return $this->status === 'incomplete';
    }

    /**
     * Check if goods received is adjusted
     */
    public function isAdjusted(): bool
    {
        return $this->status === 'adjusted';
    }

    /**
     * Calculate quantity shortage
     */
    public function calculateShortage(): int
    {
        return $this->quantity_ordered - $this->quantity_received;
    }
}
