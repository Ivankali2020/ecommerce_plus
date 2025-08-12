<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use HasFactory;

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function township(): BelongsTo
    {
        return $this->belongsTo(Township::class);
    }
}
