<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Distributor extends Model
{
    use HasFactory;

    protected $primaryKey = 'distributor_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'distributor_code',
        'distributor_name',
        'address',
        'phone',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'distributor_code';
    }

    /**
     * Get the products for the distributor.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'distributor_code', 'distributor_code');
    }

    /**
     * Generate next distributor code with pattern DS0001
     */
    public static function generateNextCode()
    {
        return DB::transaction(function () {
            $lastDistributor = static::lockForUpdate()->orderBy('distributor_code', 'desc')->first();

            if (!$lastDistributor) {
                return 'DS0001';
            }

            // Extract number from last code (e.g., DS0001 -> 1)
            $lastNumber = (int) substr($lastDistributor->distributor_code, 2);
            $nextNumber = $lastNumber + 1;

            // Format with leading zeros
            return 'DS' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}
