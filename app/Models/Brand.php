<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Brand extends Model
{
    use HasFactory;

    protected $primaryKey = 'brand_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'brand_code',
        'brand_name',
        'brand_photo',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'brand_code';
    }

    /**
     * Get the products for the brand.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_code', 'brand_code');
    }

    /**
     * Generate next brand code with pattern BR0001
     */
    public static function generateNextCode()
    {
        return DB::transaction(function () {
            $lastBrand = static::lockForUpdate()->orderBy('brand_code', 'desc')->first();

            if (!$lastBrand) {
                return 'BR0001';
            }

            // Extract number from last code (e.g., BR0001 -> 1)
            $lastNumber = (int) substr($lastBrand->brand_code, 2);
            $nextNumber = $lastNumber + 1;

            // Format with leading zeros
            return 'BR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}
