<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Cart as CartModel;

class Cart extends Component
{
    public $cartItems;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->cartItems = CartModel::with(['product', 'variant'])
            ->where('user_id', auth()->id())
            ->get();
    }

    public function removeItem($cartItemId)
    {
        CartModel::find($cartItemId)->delete();
        $this->cartItems = CartModel::with(['product', 'variant'])
            ->where('user_id', auth()->id())
            ->get();
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        if ($quantity > 0) {
            CartModel::find($cartItemId)->update(['quantity' => $quantity]);
            $this->cartItems = CartModel::with(['product', 'variant'])
                ->where('user_id', auth()->id())
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
