<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Facades\Cache;

class WholeSaleObserver
{
    public function created(Product|Variant $model): void
    {
        $this->clearWholeSaleCache($model);
    }

    public function updated(Product|Variant $model): void
    {
        $this->clearWholeSaleCache($model);
    }

    public function deleted(Product|Variant $model): void
    {
        $this->clearWholeSaleCache($model);
    }

    private function clearWholeSaleCache(Product|Variant $model): void
    {
        $sellerId = $model instanceof Product ? $model->seller_id : $model->product->seller_id;

        // Clear wholesale cache
        Cache::forget("wholesale_categories_{$sellerId}");
        Cache::forget("wholesale_brands_{$sellerId}");

        // Clear total counts cache (wildcard pattern)
        $patterns = [
            "wholesale_total_products_{$sellerId}_*",
            "wholesale_total_variants_{$sellerId}_*",
        ];

        foreach ($patterns as $pattern) {
            $keys = Cache::get($pattern);
            if (! empty($keys)) {
                Cache::forget($keys);
            }
        }
    }
}
