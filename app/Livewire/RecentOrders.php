<?php

namespace App\Livewire;

use App\Models\OrderProduct;
use App\Models\Product;
    use App\Traits\WithCart;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class RecentOrders extends Component
{
    use WithCart,WithPagination;

    public function render()
    {
        $user = Auth::user();
        $page = request()->get('page', 1);
        $cacheKey = 'recent_orders_user_' . $user->id . '_page_' . $page;

    $recentProducts = Cache::remember($cacheKey, 300, function () use ($user) {
            $productIds = OrderProduct::whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->distinct('product_id')
                ->pluck('product_id');

            return Product::whereIn('id', $productIds)
                ->with(['category', 'media', 'variants.media'])
                ->latest()
                ->paginate(12);
        });

        return view('livewire.recent-orders', [
            'recentProducts' => $recentProducts,
        ]);
    }
}
