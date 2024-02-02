<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Variant extends Model implements HasMedia
{
    use HasFactory,SoftDeletes,InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        (new MediaRegisterConversion('images'))->registerMediaConversions();
    }

    protected $casts = [
        'attributes' => 'json'
    ];

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
//
//    public function attributeValues()
//    {
//        return $this->belongsTo(AttributeValue::class)->withTrashed();
//    }
}
