<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SellerSubscription extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'seller_id',
        'subscription_plan_id',
        'started_at',
        'expired_at',
        'price_paid',
        'payment_status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('payment_slips');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
