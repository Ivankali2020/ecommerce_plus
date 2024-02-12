<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\File;


class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile()
            ->useFallbackUrl('https://default_image_url.png') //TODO::URL အမှန်နဲ့ပြန်ပြောင်းထည့်ရန်
            ->acceptsFile(function (File $file) {
                $allowedMimeTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/svg+xml',
                    'image/webp',
                ];
                return in_array($file->mimeType, $allowedMimeTypes);
            })
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(200)
                    ->height(200);
            });
    }

    protected $casts = [
        'attributes' => 'json'
    ];

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->whereNull('parent_id')->withTrashed();
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id', 'id')->withTrashed();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class)->withTrashed();
    }

    public function variants()
    {
        return $this->hasMany(Variant::class)->withTrashed();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
