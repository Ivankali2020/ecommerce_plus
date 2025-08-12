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
    use HasFactory,InteractsWithMedia,SoftDeletes,BelongsToPrimaryModel;

    public $preventObserver = false;

    protected $with = ['product.exchange'];

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

    public function getAttributeValuesAttribute()
    {
        if (! $this->attributes) {
            return [];
        }

        $formattedAttributes = [];
        $attributeData = json_decode($this->attributes['attributes'], true);

        foreach ($attributeData as $item) {
            $attribute = Attribute::find($item['attribute_id']);
            if ($attribute) {
                $values = AttributeValue::whereIn('id', $item['attribute_value_id'])
                    ->pluck('value')
                    ->toArray();

                $formattedAttributes[] = [
                    'attribute' => $attribute->name,
                    'value' => $values,
                ];
            }
        }

        return $formattedAttributes;
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
