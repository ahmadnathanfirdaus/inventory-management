<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * User who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Log an action
     */
    public static function logAction(string $action, Model $model, ?array $oldValues = null, ?array $newValues = null): void
    {
        static::create([
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(), // Use getKey() instead of id to get correct primary key
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
