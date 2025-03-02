<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class OrderDetail extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order->load([
            'seller',
            'address',
            'products.product',
            'products.variant',
        ]);

    }

    public function render()
    {
        return view('livewire.order-detail');
    }
}
