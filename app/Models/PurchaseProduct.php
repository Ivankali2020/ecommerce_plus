<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseProduct extends Model
{

    protected $casts = [
        'attributes' => 'json'
    ];


    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant():BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}
