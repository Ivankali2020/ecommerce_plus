<?php

namespace App\Models;

use App\Helper\PurchaseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $casts = [
        'status' => PurchaseStatus::class,
    ];

    public function products(): HasMany
    {
        return $this->hasMany(PurchaseProduct::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
