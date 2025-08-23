<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Product extends Model implements HasMedia
{
    use BelongsToTenant, HasFactory, InteractsWithMedia, SoftDeletes;

    public $preventObserver = false;

    protected $casts = [
        'attributes' => 'json',
        'expired_date' => 'datetime',
    ];

    protected $appends = ['productImg'];

    // Cache expensive attribute values computation
    protected $cachedAttributeValues = null;

    public function getProductImgAttribute()
    {
        // Use relationLoaded to avoid additional queries when media is already loaded
        if ($this->has_variant && $this->relationLoaded('variants')) {
            $firstVariant = $this->variants->first();
            if ($firstVariant && $firstVariant->relationLoaded('media') && $firstVariant->media->isNotEmpty()) {
                return $firstVariant->getFirstMediaUrl('images');
            }
        }

        // Use loaded media if available, otherwise fallback to query
        if ($this->relationLoaded('media') && $this->media->isNotEmpty()) {
            return $this->getFirstMediaUrl('images');
        }

        return $this->getFirstMediaUrl('images');
    }

    public function getAttributeValuesAttribute()
    {
        // Return cached result if available
        if ($this->cachedAttributeValues !== null) {
            return $this->cachedAttributeValues;
        }

        // Check if the attributes column exists in the model
        if (! array_key_exists('attributes', $this->attributes)) {
            return $this->cachedAttributeValues = [];
        }

        // If attributes is null or empty, return empty array
        if (empty($this->attributes['attributes'])) {
            return $this->cachedAttributeValues = [];
        }

        $formattedAttributes = [];
        $attributeData = json_decode($this->attributes['attributes'], true);

        if (! $attributeData) {
            return $this->cachedAttributeValues = [];
        }

        // Cache the expensive queries
        $cacheKey = 'product_attributes_'.md5(json_encode($attributeData));

        return $this->cachedAttributeValues = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($attributeData) {
            // Optimize by batching queries
            $attributeIds = collect($attributeData)->pluck('attribute_id')->unique();
            $attributes = Attribute::whereIn('id', $attributeIds)->get()->keyBy('id');

            $allValueIds = collect($attributeData)->pluck('attribute_value_id')->flatten()->unique();
            $attributeValues = AttributeValue::whereIn('id', $allValueIds)->get()->keyBy('id');

            $formattedAttributes = [];
            foreach ($attributeData as $item) {
                $attribute = $attributes->get($item['attribute_id']);
                if ($attribute) {
                    $values = collect($item['attribute_value_id'])
                        ->map(fn ($id) => $attributeValues->get($id))
                        ->filter() // Remove null values
                        ->pluck('value')
                        ->toArray();

                    $formattedAttributes[] = [
                        'attribute' => $attribute->name,
                        'value' => $values,
                    ];
                }
            }

            return $formattedAttributes;
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl(asset('assets/img.jpg'))
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
        return $this->belongsTo(Category::class)->withTrashed(); // ->whereNull('parent_id')
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id', 'id')->withTrashed();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class)->withTrashed();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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

    /**
     * Get the average rating for this product
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->avg('stars') ?: 0;
    }

    /**
     * Get the total count of reviews for this product
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Get a formatted average rating (e.g., "4.5")
     */
    public function getFormattedAverageRatingAttribute(): string
    {
        return number_format($this->average_rating, 1);
    }

    protected static function booted()
    {
        self::observe(ProductObserver::class);
    }
}
