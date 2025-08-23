<?php

namespace App\Livewire\Cart;

use App\Helper\UserHelper;
use App\Models\Cart as CartModel;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CartSummary extends Component
{
    #[Locked]
    public $sellerId;

    public $deliveryFee = 0;

    public function mount(): void
    {
        $this->sellerId = UserHelper::getUserSellerId();
    }

    #[Computed]
    public function cartItems()
    {
        if (! Auth::check()) {
            return collect();
        }

        $userId = Auth::id();
        $cacheKey = "cart_items_{$userId}_{$this->sellerId}";

        return Cache::remember($cacheKey, 300, function () use ($userId) {
            return CartModel::select(['id', 'user_id', 'seller_id', 'product_id', 'variant_id', 'quantity', 'price', 'total'])
                ->with([
                    'product:id,name,price,seller_id',
                    'variant:id,name,price,product_id',
                ])
                ->where('user_id', $userId)
                ->where('seller_id', $this->sellerId)
                ->get();
        });
    }

    #[On('cart-updated')]
    #[On('delivery-updated')]
    public function refreshCartItems(): void
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $cacheKey = "cart_items_{$userId}_{$this->sellerId}";
            Cache::forget($cacheKey);
        }
    }

    #[On('delivery-fee-updated')]
    public function updateDeliveryFee($fee): void
    {
        $this->deliveryFee = $fee;
    }

    #[Computed]
    public function subtotal(): float
    {
        return $this->cartItems->sum('total');
    }

    #[Computed]
    public function total(): float
    {
        return $this->subtotal + $this->deliveryFee;
    }

    #[Computed]
    public function pointsToEarn(): int
    {
        if (! Auth::check()) {
            return 0;
        }

        $cacheKey = "seller_points_percentage_{$this->sellerId}";

        $pointsPercentage = Cache::remember($cacheKey, 1800, function () {
            $seller = Seller::select(['id'])->with(['setting:id,seller_id,data'])->find($this->sellerId);

            return $seller?->setting?->data['point_percentage'] ?? 1;
        });

        return floor($this->subtotal * ((int) $pointsPercentage / 100));
    }

    public function render()
    {
        return view('livewire.cart.cart-summary');
    }
}
