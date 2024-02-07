<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\File;

class Brand extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logos')
            ->singleFile()
            ->useFallbackUrl('https://fallbackurl.png')
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
                    ->width(140)
                    ->height(140);
            });
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
