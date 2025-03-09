<?php

namespace App\Livewire;

use App\Models\Order as OrderModel;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Order extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $seller_id;

    public function mount()
    {
        $this->seller_id = auth('web')->user()->seller_id;
    }

    public function render()
    {
        $orders = OrderModel::query()
            ->select([
                'orders.*',
                'sellers.name as seller_name'
            ])
            ->join('sellers', 'orders.seller_id', '=', 'sellers.id')
            ->where('orders.user_id', auth()->id())
            // ->with( ['products.product', 'products.variant'])
            ->orderBy('sellers.name')
            ->orderBy('orders.created_at', 'desc')
            ->paginate(10);

        // Group orders by seller name after pagination
        $groupedOrders = $orders->groupBy('seller_name');

        return view('livewire.order', [
            'groupedOrders' => $groupedOrders,
            'orders' => $orders
        ]);
    }
}

