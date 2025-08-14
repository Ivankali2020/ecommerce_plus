<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_id',
        'tenant_id',
        'type',
        'points',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'metadata',
        'expires_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the seller associated with the transaction.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Get the tenant associated with the transaction.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the reference model (order, gift, etc.)
     */
    public function reference(): MorphTo
    {
        return $this->morphTo('reference');
    }

    /**
     * Scope for earned points transactions.
     */
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    /**
     * Scope for spent points transactions.
     */
    public function scopeSpent($query)
    {
        return $query->where('type', 'spent');
    }

    /**
     * Scope for expired points transactions.
     */
    public function scopeExpired($query)
    {
        return $query->where('type', 'expired');
    }

    /**
     * Scope for refunded points transactions.
     */
    public function scopeRefunded($query)
    {
        return $query->where('type', 'refunded');
    }
}
