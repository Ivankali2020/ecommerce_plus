<?php

namespace App\Models;

use App\Helper\MediaRegisterConversion;
use App\Observers\VariantObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel;

class Variant extends Model implements HasMedia
{
    use BelongsToPrimaryModel,HasFactory,InteractsWithMedia,SoftDeletes;

    public $preventObserver = false;

    public function getRelationshipToPrimaryModel(): string
    {
        return 'product';
    }

    public function registerMediaCollections(): void
    {
        (new MediaRegisterConversion('images'))->registerMediaConversions();
    }

    protected $casts = [
        'attributes' => 'json',
    ];

    protected $appends = ['attribute_values', 'product_img'];

    public function getProductImgAttribute()
    {
        // Return product's own image
        return $this->getFirstMediaUrl('images');
    }

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->with('exchange.currency');
    }

    // public function getAttributeValuesAttribute()
    // {
    //     if (! $this->attributes) {
    //         return [];
    //     }

    //     $formattedAttributes = [];
    //     $attributeData = json_decode($this->attributes['attributes'], true);

    //     foreach ($attributeData as $item) {
    //         $attribute = Attribute::find($item['attribute_id']);
    //         if ($attribute) {
    //             $values = AttributeValue::whereIn('id', $item['attribute_value_id'])
    //                 ->pluck('value')
    //                 ->toArray();

    //             $formattedAttributes[] = [
    //                 'attribute' => $attribute->name,
    //                 'value' => $values,
    //             ];
    //         }
    //     }

    //     return $formattedAttributes;
    // }

    // Cache expensive attribute values computation
    protected $cachedAttributeValues = null;

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
        $cacheKey = 'variant_attributes_'.md5(json_encode($attributeData));

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

    protected static function booted()
    {
        self::observe(VariantObserver::class);
    }
    //
    //    public function attributeValues()
    //    {
    //        return $this->belongsTo(AttributeValue::class)->withTrashed();
    //    }
}
