<?php

namespace App\Observers;

use App\Models\Variant;

class VariantObserver
{
    public function created(Variant $variant): void
        {
            if ($variant->mmk_buy_price == null) {
                $variant->update([
                    'mmk_buy_price' => $variant->buy_price,
                    'mmk_price' => $variant->price,
                    'mmk_discount_price' => $variant->discount_price,
                ]);
            }
        }

        public function updating(Variant $variant): void
        {
            if ($variant->preventObserver) {
                return;
            }

            if ($variant->mmk_buy_price == null) {
                $variant->preventObserver = true;
                $variant->update([
                    'mmk_buy_price' => $variant->buy_price,
                    'mmk_price' => $variant->price,
                    'mmk_discount_price' => $variant->discount_price,
                ]);
                $variant->preventObserver = false;
            }
        }
}
