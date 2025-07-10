<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'transaction_code',
        'user_code',
        'total_quantity',
        'total_price',
        'purchase_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_quantity' => 'integer',
        'total_price' => 'integer',
    ];

    /**
     * Get the user that created the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_code', 'user_code');
    }

    /**
     * Get the transaction items for the transaction.
     */
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_code', 'transaction_code');
    }
}
