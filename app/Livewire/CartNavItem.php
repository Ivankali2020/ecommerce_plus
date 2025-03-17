<?php

namespace App\Livewire;

use App\Helper\UserHelper;
use Livewire\Component;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartNavItem extends Component
{
    public $cartCount = 0;
    
    protected $listeners = ['cartUpdated' => 'updateCartCount'];

    public function mount()
    {
        $this->updateCartCount();
    }

    public function updateCartCount()
    {
        if (Auth::check()) {
            $this->cartCount = Cart::where('seller_id',UserHelper::getUserSellerId())->where('user_id', Auth::id())->sum('quantity');
        } else {
            $this->cartCount = 0;
        }
    }

    public function render()
    {
        return view('livewire.cart-nav-item');
    }
}
