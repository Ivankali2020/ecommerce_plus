<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model implements HasMedia
{
    use HasFactory,SoftDeletes,InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        (new MediaRegisterConversion('logos'))->registerMediaConversions();
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
