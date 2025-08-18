<?php

namespace App\Livewire\Cart;

use App\Helper\UserHelper;
use App\Models\Cart as CartModel;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CartSummary extends Component
{
    #[Locked]
    public $sellerId;

    public $cartItems = [];

    public $deliveryFee = 0;

    public function mount(): void
    {
        $this->sellerId = UserHelper::getUserSellerId();
        $this->loadCartItems();
    }

    #[On('cart-updated')]
    #[On('delivery-updated')]
    public function loadCartItems(): void
    {
        $this->cartItems = CartModel::with(['product', 'variant'])
            ->where('user_id', auth('web')->id())
            ->where('seller_id', $this->sellerId)
            ->get();
    }

    #[On('delivery-fee-updated')]
    public function updateDeliveryFee($fee): void
    {
        $this->deliveryFee = $fee;
    }

    #[Computed]
    public function subtotal(): float
    {
        return collect($this->cartItems)->sum(fn ($item) => $item->total);
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

        $subtotal = $this->subtotal;

        // Get points earning percentage from system settings (default to 1% if not set)
        $pointsPercentage = Seller::find($this->sellerId)->setting->data['point_percentage'] ?? 1;

        return floor($subtotal * ((int) $pointsPercentage / 100));
    }

    public function render()
    {
        return view('livewire.cart.cart-summary');
    }
}
