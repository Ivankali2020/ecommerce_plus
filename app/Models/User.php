<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helper\MediaRegisterConversion;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use BelongsToTenant, HasApiTokens, HasFactory, InteractsWithMedia, Notifiable;

    public function registerMediaCollections(): void
    {
        (new MediaRegisterConversion('profile_photo'))->registerMediaConversions();
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'firebase_uid',
        'google_id',
        'fmc_token',
        'tenant_id',
        'seller_id',
        'email_verified_at',
        'remember_token',
        'points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function giftExchanges()
    {
        return $this->hasMany(GiftExchange::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Check if user has enough points
     */
    public function hasPoints(int $requiredPoints): bool
    {
        return $this->points >= $requiredPoints;
    }

    /**
     * Get points earned from orders
     */
    public function getEarnedPoints()
    {
        return $this->pointTransactions()->where('type', 'earned')->sum('points');
    }

    /**
     * Get points spent on gifts
     */
    public function getSpentPoints()
    {
        return abs($this->pointTransactions()->where('type', 'spent')->sum('points'));
    }
}
