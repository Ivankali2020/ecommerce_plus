<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use App\Helper\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Order extends Model implements HasMedia
{
    use BelongsToTenant, HasFactory, InteractsWithMedia, SoftDeletes;

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    public function registerMediaCollections(): void
    {
        (new MediaRegisterConversion('photos'))->registerMediaConversions();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id', 'id')->withTrashed();
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class)->withTrashed();
    }

    public function paymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class)->withTrashed();
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class, 'reference_id')
            ->where('reference_type', 'order');
    }

    public function scopeGetOnlyConfirm($q)
    {
        return $q->whereIn('status', [OrderStatus::Processing, OrderStatus::Delivered, OrderStatus::Shipped]);
    }

    /**
     * Check if this order can be returned
     */
    public function canDoRet(): bool
    {
        // Check if order is delivered
        if ($this->status->value !== OrderStatus::Delivered->value) {
            return false;
        }

        // Check if order return can be only 24 hours
        $hoursSinceDelivery = $this->updated_at->diffInHours(now());

        if ($hoursSinceDelivery > 24) {
            return false;
        }

        // Check if all products are returnable
        $returnableProductsCount = $this->products()->whereHas('product', function ($query) {
            $query->where('is_return', true);
        })->count();

        return $returnableProductsCount > 0;
    }

    /**
     * Check if this order has an active return request
     */
    public function hasActiveReturn(): bool
    {
        return $this->returns()->whereIn('status', [
            \App\Helper\ReturnStatus::Pending,
            \App\Helper\ReturnStatus::UnderReview,
            \App\Helper\ReturnStatus::Approved,
            \App\Helper\ReturnStatus::Processing,
        ])->exists();
    }
}
