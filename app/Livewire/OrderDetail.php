<?php

namespace App\Livewire;

use App\Models\Order;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
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

        if($order->user_id != Auth::id()){
            Notification::make()
                ->title('Order Detail')
                ->body('You are not authorized to view this order')
                ->success()
                ->send();
          redirect()->route('order');
        }

    }

    public function render()
    {
        return view('livewire.order-detail');
    }
}
