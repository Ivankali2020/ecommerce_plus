<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function created(Product $product): void
    {
        if ($product->exchange->currency_id == 1) {
            $product->update([
                'mmk_buy_price' => $product->buy_price,
                'mmk_price' => $product->price,
                'mmk_discount_price' => $product->discount > 0 ?  $product->discount_price : $product->price,
            ]);
        }

        $this->clearRelatedCaches($product);
    }

    public function updated(Product $product): void
    {
        if ($product->preventObserver) {
            return;
        }

        if ($product->exchange->currency_id == 1) {
            $product->preventObserver = true;
            $product->update([
                'mmk_buy_price' => $product->buy_price,
                'mmk_price' => $product->price,
                'mmk_discount_price' => $product->discount > 0 ?  $product->discount_price : $product->price,
            ]);
            $product->preventObserver = false;
        }

        $this->clearRelatedCaches($product);
    }

    public function deleted(Product $product): void
    {
        $this->clearRelatedCaches($product);
    }

    protected function clearRelatedCaches(Product $product): void
    {
        // Clear AllProduct component caches
        Cache::forget("categories_seller_{$product->seller_id}");
        Cache::forget("brands_seller_{$product->seller_id}");

        // Clear ProductDetail related products cache
        Cache::forget("related_products_{$product->id}_{$product->category_id}");

        // Clear product's attribute cache
        if ($product->attributes) {
            $attributeData = $product->attributes;
            if ($attributeData) {
                $cacheKey = 'product_attributes_'.md5(json_encode($attributeData));
                Cache::forget($cacheKey);
            }
        }

        // Clear related products for products in the same category
        if ($product->category_id) {
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->pluck('id');

            foreach ($relatedProducts as $relatedId) {
                Cache::forget("related_products_{$relatedId}_{$product->category_id}");
            }
        }

        // Clear tenant-specific caches if tenant exists
        if (tenant()) {
            Cache::forget('recommended_products_'.tenant('id'));
            Cache::forget('wholesale_products_'.tenant('id'));
        }

        // Note: Product-specific caches are handled by the component's clearProductsCache method
    }
}
