<?php

namespace App\Livewire\Cart;

use App\Helper\UserHelper;
use App\Models\Cart as CartModel;
use Filament\Notifications\Notification;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CartItems extends Component
{
    #[Locked]
    public $sellerId;

    public $cartItems = [];

    public function mount(): void
    {
        $this->sellerId = UserHelper::getUserSellerId();
        $this->loadCartItems();
    }

    public $delivery = null;

    #[On('delivery-updated')]
    public function updateDelivery($details): void
    {
        $this->delivery = $details ?? null;
    }

    public function loadCartItems(): void
    {
        $this->cartItems = CartModel::with(['product', 'variant'])
            ->where('user_id', auth('web')->id())
            ->where('seller_id', $this->sellerId)
            ->get();
    }

    public function removeItem(int $cartItemId): void
    {
        CartModel::find($cartItemId)?->delete();
        $this->loadCartItems();

        // Dispatch event to update other components
        $this->dispatch('cart-updated');
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity(int $cartItemId, int $quantity): void
    {
        $cart = CartModel::where('id', $cartItemId)
            ->where('seller_id', $this->sellerId)
            ->with(['product', 'variant'])
            ->first();

        if (! $cart || $quantity <= 0) {
            return;
        }

        // checking wholesale MOQ when decreasing quantity
        if ($cart->product->is_whole_sale && $quantity < $cart->product->MOQ) {
            Notification::make()
                ->title(__('messages.cart.wholesale_moq_title'))
                ->body(__('messages.cart.wholesale_moq_message', ['moq' => $cart->product->MOQ]))
                ->warning()
                ->send();

            return;
        }

        // Check available stock
        $availableStock = $cart->variant ? $cart->variant->quantity : $cart->product->quantity;

        if ($quantity > $availableStock) {
            Notification::make()
                ->title(__('messages.cart.out_of_stock'))
                ->body(__('messages.cart.limited_stock', ['quantity' => $availableStock]))
                ->warning()
                ->send();

            $quantity = $availableStock;
        }

        // Update cart item
        $cart->update([
            'quantity' => $quantity,
            'total' => $cart->price * $quantity,
        ]);

        $this->loadCartItems();

        // Dispatch event to update other components
        $this->dispatch('cart-updated');
        $this->dispatch('cartUpdated');
    }

    public function render()
    {
        return view('livewire.cart.cart-items');
    }
}
