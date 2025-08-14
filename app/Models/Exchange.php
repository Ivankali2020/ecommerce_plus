<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Exchange extends Model
{
    use HasFactory,SoftDeletes,BelongsToTenant;

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    public function exchange_histories()
    {
        return $this->hasMany(ExchangeHistory::class);
    }
}
