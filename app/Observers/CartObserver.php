<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Cart;
use Illuminate\Support\Facades\Cache;

class CartObserver
{
    public function created(Cart $cart): void
    {
        $this->clearCartCache($cart);
    }

    public function updated(Cart $cart): void
    {
        $this->clearCartCache($cart);
    }

    public function deleted(Cart $cart): void
    {
        $this->clearCartCache($cart);
    }

    private function clearCartCache(Cart $cart): void
    {
        $cartCountKey = "cart_count_{$cart->user_id}_{$cart->seller_id}";
        $cartItemsKey = "cart_items_{$cart->user_id}_{$cart->seller_id}";

        Cache::forget($cartCountKey);
        Cache::forget($cartItemsKey);
    }
}
