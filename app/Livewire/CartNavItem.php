<?php

namespace App\Livewire;

use App\Helper\UserHelper;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CartNavItem extends Component
{
    #[Computed]
    public function cartCount()
    {
        if (! Auth::check()) {
            return 0;
        }

        $userId = Auth::id();
        $sellerId = UserHelper::getUserSellerId();
        $cacheKey = "cart_count_{$userId}_{$sellerId}";

        return Cache::remember($cacheKey, 300, function () use ($userId, $sellerId) {
            return Cart::where('seller_id', $sellerId)
                ->where('user_id', $userId)
                ->sum('quantity');
        });
    }

    #[On('cartUpdated')]
    public function updateCartCount()
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $sellerId = UserHelper::getUserSellerId();
            $cacheKey = "cart_count_{$userId}_{$sellerId}";

            // Clear the cache to force refresh
            Cache::forget($cacheKey);
        }
    }

    public function render()
    {
        return view('livewire.cart-nav-item');
    }
}
