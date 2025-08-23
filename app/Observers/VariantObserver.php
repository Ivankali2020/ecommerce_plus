<?php

namespace App\Observers;

use App\Models\Variant;
use Illuminate\Support\Facades\Cache;

class VariantObserver
{
    public function created(Variant $variant): void
    {
        if ($variant->product->exchange->currency_id == 1) {
            $variant->update([
                'mmk_buy_price' => $variant->buy_price,
                'mmk_price' => $variant->price,
                'mmk_discount_price' => $variant->discount_price,
            ]);
        }

        $this->clearVariantCaches($variant);
    }

    public function updating(Variant $variant): void
    {
        if ($variant->preventObserver) {
            return;
        }

        if ($variant->product->exchange->currency_id == 1) {
            $variant->preventObserver = true;
            $variant->update([
                'mmk_buy_price' => $variant->buy_price,
                'mmk_price' => $variant->price,
                'mmk_discount_price' => $variant->discount_price,
            ]);
            $variant->preventObserver = false;
        }

        $this->clearVariantCaches($variant);
    }

    public function updated(Variant $variant): void
    {
        $this->clearVariantCaches($variant);
    }

    public function deleted(Variant $variant): void
    {
        $this->clearVariantCaches($variant);
    }

    protected function clearVariantCaches(Variant $variant): void
    {
        // Clear the variant's attribute cache
        if ($variant->attributes) {
            $attributeData = $variant->attributes;
            if ($attributeData) {
                $cacheKey = 'variant_attributes_'.md5(json_encode($attributeData));
                Cache::forget($cacheKey);
            }
        }

        // Clear product-related caches since variant changed
        if ($variant->product_id) {
            Cache::forget("related_products_{$variant->product_id}_{$variant->product->category_id}");
        }
    }
}
