<?php

namespace App\Livewire;

use App\Models\Order as OrderModel;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Order extends Component
{
    use WithPagination;

    #[Computed]
    public function orders()
    {
        return OrderModel::query()
            ->where('user_id', auth('web')->id())
            ->with(['products.product', 'products.variant', 'seller'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.order');
    }
}
