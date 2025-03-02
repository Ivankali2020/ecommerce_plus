<?php

namespace App\Livewire;

use App\Models\Product;
use App\Traits\WithCart;
use Livewire\Component;

class ProductDetail extends Component
{
    use WithCart;
    public Product $product;

    public string $type;

    public function mount($product,$type = 'detail')
    {
        $this->product = $product;
        $this->type = $type;
    }


    public function render()
    {
        return view('livewire.product-detail');
    }
}
