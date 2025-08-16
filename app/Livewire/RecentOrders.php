<?php

namespace App\Livewire;

use App\Models\OrderProduct;
use App\Models\Product;
use App\Traits\WithCart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RecentOrders extends Component
{
    use WithCart,WithPagination;

    public function render()
    {
        $user = Auth::user();

        // Get unique products from user's order history
        $productIds = OrderProduct::whereHas('order', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->distinct('product_id')
            ->pluck('product_id');

        // Fetch the actual products with their details
        $recentProducts = Product::whereIn('id', $productIds)
            ->with(['category', 'media', 'variants.media'])
            ->latest()
            ->paginate(12);

        return view('livewire.recent-orders', [
            'recentProducts' => $recentProducts,
        ]);
    }
}
