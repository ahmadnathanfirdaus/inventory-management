<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_code',
        'user_name',
        'username',
        'user_photo',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'hire_date' => 'date',
            'salary' => 'decimal:2',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is cashier
     */
    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    /**
     * Check if user can manage employees (admin or manager)
     */
    public function canManageEmployees(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    /**
     * Check if user can manage transactions (cashier or manager)
     */
    public function canManageTransactions(): bool
    {
        return $this->isCashier() || $this->isManager();
    }

    /**
     * Goods received by this user
     */
    public function goodsReceived()
    {
        return $this->hasMany(GoodsReceived::class, 'received_by');
    }

    /**
     * Audit logs for this user
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    /**
     * Sales processed by this cashier
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_code', 'user_code');
    }

    /**
     * Products created by this user
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    /**
     * Brands created by this user
     */
    public function brands()
    {
        return $this->hasMany(Brand::class, 'created_by');
    }

    /**
     * Distributors created by this user
     */
    public function distributors()
    {
        return $this->hasMany(Distributor::class, 'created_by');
    }

    /**
     * Stock movements made by this user
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for employees (cashiers)
     */
    public function scopeEmployees($query)
    {
        return $query->where('role', 'cashier');
    }

    /**
     * Get user's full name with employee ID
     */
    public function getDisplayNameAttribute()
    {
        return $this->employee_id ? "{$this->name} ({$this->employee_id})" : $this->name;
    }

    /**
     * Generate next user code with pattern USR001
     */
    public static function generateNextCode()
    {
        return DB::transaction(function () {
            $lastUser = static::lockForUpdate()->orderBy('user_code', 'desc')->first();

            if (!$lastUser) {
                return 'USR001';
            }

            // Extract number from last code (e.g., USR001 -> 1)
            $lastNumber = (int) substr($lastUser->user_code, 3);
            $nextNumber = $lastNumber + 1;

            // Format with leading zeros
            return 'USR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }
}
