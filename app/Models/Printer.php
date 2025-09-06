<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Printer extends Model
{
    protected $fillable = [
        'tenant_id',
        'seller_id',
        'name',
        'connection_type',
        'ip_address',
        'port',
        'usb_path',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'port' => 'integer',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getConnectionStringAttribute(): string
    {
        return match ($this->connection_type) {
            'network' => "{$this->ip_address}:{$this->port}",
            'usb' => $this->usb_path,
            default => 'Unknown connection',
        };
    }

    protected static function boot(): void
    {
        parent::boot();

        // Ensure only one default printer per seller
        static::saving(function ($printer) {
            if ($printer->is_default) {
                static::where('seller_id', $printer->seller_id)
                    ->where('id', '!=', $printer->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
