<?php

namespace App\Models;

use App\Observers\OrderProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// #[ObservedBy(\App\Observers\OrderProductObserver::class)]
class OrderProduct extends Model
{
    use HasFactory;

    protected $casts = [
        'attributes' => 'json',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class)->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withTrashed();
    }

    protected static function booted()
    {
        self::observe(OrderProductObserver::class);
    }
}
