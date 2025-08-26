<?php

namespace App\Observers;

use App\Models\Exchange;
use App\Models\ExchangeHistory;
use App\Models\Product;
use App\Models\Variant;

class ExchangeObserver
{
    public function updated(Exchange $exchange)
    {
        // Save exchange rate history
        $history = new ExchangeHistory;
        $history->exchange_id = $exchange->id;
        $history->rate = $exchange->rate;
        $history->save();

        // Update MMK prices only if the currency is not MMK
        if ($exchange->currency->code !== 'MMK') {
            $this->updateProductsMmkPrices($exchange);
            $this->updateVariantsMmkPrices($exchange);
        }
    }

    private function updateProductsMmkPrices(Exchange $exchange): void
    {
        // Get all products using this exchange
        $products = Product::where('exchange_id', $exchange->id)->get();

        foreach ($products as $product) {
            // Skip if product has variants (variants handle their own pricing)
            if ($product->has_variant) {
                continue;
            }

            $mmkBuyPrice = null;
            $mmkPrice = null;
            $mmkDiscountPrice = null;

            // Calculate MMK buy price if exists
            if ($product->buy_price) {
                $mmkBuyPriceRaw = $product->buy_price * $exchange->rate;
                $mmkBuyPrice = $this->modifyPrice($mmkBuyPriceRaw);
            }

            // Calculate MMK price if exists
            if ($product->price) {
                $mmkPriceRaw = $product->price * $exchange->rate;
                $mmkPrice = $this->modifyPrice($mmkPriceRaw);

                // Calculate MMK discount price based on discount percentage
                if ($product->discount > 0) {
                    $discountCalculateForMMK = $mmkPriceRaw - ($product->discount / 100 * $mmkPriceRaw);
                    $mmkDiscountPrice = $this->modifyPrice($discountCalculateForMMK);
                } else {
                    $mmkDiscountPrice = 0;
                }
            }

            // Update the product
            $product->update([
                'mmk_buy_price' => $mmkBuyPrice,
                'mmk_price' => $mmkPrice,
                'mmk_discount_price' => $mmkDiscountPrice,
            ]);
        }
    }

    private function updateVariantsMmkPrices(Exchange $exchange): void
    {
        // Get all variants for products using this exchange
        $variants = Variant::whereHas('product', function ($q) use ($exchange) {
            $q->where('exchange_id', $exchange->id);
        })->get();

        foreach ($variants as $variant) {
            $mmkBuyPrice = null;
            $mmkPrice = null;
            $mmkDiscountPrice = null;

            // Calculate MMK buy price if exists
            if ($variant->buy_price) {
                $mmkBuyPriceRaw = $variant->buy_price * $exchange->rate;
                $mmkBuyPrice = $this->modifyPrice($mmkBuyPriceRaw);
            }

            // Calculate MMK price if exists
            if ($variant->price) {
                $mmkPriceRaw = $variant->price * $exchange->rate;
                $mmkPrice = $this->modifyPrice($mmkPriceRaw);

                // Calculate MMK discount price based on discount percentage
                if ($variant->discount > 0) {
                    $discountCalculateForMMK = $mmkPriceRaw - ($variant->discount / 100 * $mmkPriceRaw);
                    $mmkDiscountPrice = $this->modifyPrice($discountCalculateForMMK);
                } else {
                    $mmkDiscountPrice = 0;
                }
            }

            // Update the variant
            $variant->update([
                'mmk_buy_price' => $mmkBuyPrice,
                'mmk_price' => $mmkPrice,
                'mmk_discount_price' => $mmkDiscountPrice,
            ]);
        }
    }

    private function modifyPrice($price): float
    {
        // Round the price to the nearest hundred
        $roundedPrice = round($price, -2);

        // Ensure the last two digits are "00"
        $modifiedPrice = $roundedPrice - ($roundedPrice % 100);

        return $modifiedPrice;
    }
}
