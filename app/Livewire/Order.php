<?php

namespace App\Livewire;

use App\Models\Order as OrderModel;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class Order extends Component
{
    use WithPagination;

    #[Computed]
    public function orders()
    {
        $userId = auth('web')->id();
        $page = request()->get('page', 1);
        $cacheKey = 'user_orders_' . $userId . '_page_' . $page;

        return Cache::remember($cacheKey, 300, function () use ($userId) {
            return OrderModel::query()
                ->where('user_id', $userId)
                ->with(['products.product', 'products.variant', 'seller'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        });
    }

    public function render()
    {
        return view('livewire.order');
    }
}
