<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use App\Helper\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $with = ['products', 'address', 'user'];

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

    public function scopeGetOnlyConfirm($q)
    {
        return $q->whereIn('status', [OrderStatus::Processing, OrderStatus::Delivered, OrderStatus::Shipped]);
    }
}
