<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Payment extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia,SoftDeletes,BelongsToTenant;

    public function registerMediaCollections(): void
    {
        (new MediaRegisterConversion('profile_photo'))->registerMediaConversions();
    }

    public function accounts()
    {
        return $this->hasMany(PaymentAccount::class)->withTrashed();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
