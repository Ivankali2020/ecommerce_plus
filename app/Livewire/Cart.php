<?php

namespace App\Livewire;

use App\Helper\UserHelper;
use App\Models\Cart as CartModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Cart extends Component
{
    #[Locked]
    public $sellerId;

    public $cartItems = [];

    public function mount()
    {
        if (! Auth::check('web')) {
            return redirect()->route('login');
        }

        $this->sellerId = UserHelper::getUserSellerId();
        $this->loadCartItems();
    }

    #[On('cart-updated')]
    public function loadCartItems(): void
    {
        $this->cartItems = CartModel::with(['product', 'variant'])
            ->where('user_id', auth('web')->id())
            ->where('seller_id', $this->sellerId)
            ->get();
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
