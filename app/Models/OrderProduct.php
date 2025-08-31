<?php

namespace App\Models;

use App\Observers\OrderProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

// #[ObservedBy(\App\Observers\OrderProductObserver::class)]
class OrderProduct extends Model
{
    use BelongsToPrimaryModel,HasFactory;

    public function getRelationshipToPrimaryModel(): string
    {
        return 'order';
    }

    protected $casts = [
        'attributes' => 'array',
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

    public function returnProducts()
    {
        return $this->hasMany(OrderReturnProduct::class);
    }

    public function getAvailableReturnQuantity(): int
    {
        return $this->quantity - $this->returned_quantity;
    }

    public function isFullyReturned(): bool
    {
        return $this->returned_quantity >= $this->quantity;
    }

    public function canDoRet(): bool
    {
        return $this->getAvailableReturnQuantity() > 0 &&
               $this->product->is_return &&
               ! $this->isFullyReturned();
    }

    public function hasActiveReturn(): bool
    {
        return $this->returnProducts()->whereHas('orderReturn', function ($query) {
            $query->whereIn('status', [
                \App\Helper\ReturnStatus::Pending,
                \App\Helper\ReturnStatus::UnderReview,
                \App\Helper\ReturnStatus::Approved,
                \App\Helper\ReturnStatus::Processing,
            ]);
        })->exists();
    }

    protected static function booted()
    {
        self::observe(OrderProductObserver::class);
    }
}
