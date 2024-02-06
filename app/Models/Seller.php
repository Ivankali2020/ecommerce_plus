<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class Seller extends Authenticatable implements HasMedia,FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable,InteractsWithMedia,SoftDeletes,HasRoles,HasPanelShield;

    public function registerMediaCollections(): void
    {
       (new MediaRegisterConversion('logo'))->registerMediaConversions();
    }

    protected $guarded = 'seller';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];


    public function staffs()
    {
        return $this->hasMany(Seller::class,'parent_id','id');
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
       return  true;
    }
}
