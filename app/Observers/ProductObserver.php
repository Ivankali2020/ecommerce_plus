<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        if ($product->exchange->currency_id == 1) {
            $product->update([
                'mmk_buy_price' => $product->buy_price,
                'mmk_price' => $product->price,
                'mmk_discount_price' => $product->discount_price,
            ]);
        }
    }

    public function updating(Product $product): void
    {
        if ($product->preventObserver) {
            return;
        }

        if ($product->exchange->currency_id == 1) {
            $product->preventObserver = true;
            $product->update([
                'mmk_buy_price' => $product->buy_price,
                'mmk_price' => $product->price,
                'mmk_discount_price' => $product->discount_price,
            ]);
            $product->preventObserver = false;
        }
    }
}
