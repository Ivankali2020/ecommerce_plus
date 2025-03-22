<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use App\Observers\SellerObserver;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([SellerObserver::class])]
class Seller extends Authenticatable implements HasMedia, FilamentUser, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, SoftDeletes, HasRoles, HasPanelShield;

    public function registerMediaCollections(): void
    {
        (new MediaRegisterConversion('logo'))->registerMediaConversions();
        $this->addMediaCollection('subscription_slips');
    }

    protected $guarded = [];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'parent_id',
        'subscription_plan_id',
        'subscription_started_at',
        'subscription_expired_at',
        'subscription_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'subscription_started_at' => 'datetime',
        'subscription_expired_at' => 'datetime',
    ];

    // Check if subscription is active
    public function hasActiveSubscription(): bool
    {
        if (!$this->subscription_expired_at) {
            return false;
        }
        
        return $this->subscription_expired_at->gt(Carbon::now());
    }
    
    // Check if subscription is expired
    public function isSubscriptionExpired(): bool
    {
        if (!$this->subscription_expired_at) {
            return true;
        }
        
        return $this->subscription_expired_at->lt(Carbon::now());
    }
    
    // Get days remaining in subscription
    public function daysRemainingInSubscription(): int
    {
        if (!$this->subscription_expired_at) {
            return 0;
        }
        
        return max(0, Carbon::now()->diffInDays($this->subscription_expired_at, false));
    }
    
    // Get parent seller for staff accounts
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'parent_id');
    }

    // Get subscription plan
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    // Get subscription history
    public function subscriptionHistory()
    {
        return $this->hasMany(SellerSubscription::class);
    }

    public function staffs()
    {
        return $this->hasMany(Seller::class, 'parent_id', 'id');
    }

    public function brands()
    {
        return $this->hasMany(Brand::class)->withTrashed();
    }

    public function categories()
    {
        return $this->hasMany(Category::class)->withTrashed();
    }

    public function products()
    {
        return $this->hasMany(Product::class)->withTrashed();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->with('accounts')->withTrashed();
    }

    public function attributes()
    {
        return $this->hasMany(Attribute::class)->withTrashed();
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class)->withTrashed();
    }

    public function regions()
    {
        return $this->hasManyThrough(Region::class, Delivery::class, 'seller_id', 'id', 'id', 'region_id');
    }

    public function townships()
    {
        return $this->hasManyThrough(Township::class, Delivery::class, 'seller_id', 'id', 'id', 'township_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function exchanges()
    {
        return $this->hasMany(Exchange::class);
    }

    public function addresses()
    {
        return $this->hasMany(SellerAddress::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // For staff accounts, check parent's subscription
        if ($this->parent_id) {
            $parent = $this->parent;
            if (!$parent || $parent->isSubscriptionExpired()) {
                // Store the redirect info in session
                session(['subscription_expired' => true]);
                return true; // Allow access but will be redirected by middleware
            }
            return true;
        }
        
        // For main seller accounts, check own subscription
        if ($this->isSubscriptionExpired()) {
            // Store the redirect info in session
            session(['subscription_expired' => true]);
            return true; // Allow access but will be redirected by middleware
        }
        
        return true;
    }

    

}
