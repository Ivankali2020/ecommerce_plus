<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;

    public function mount($product)
    {
        $this->product = $product;
    }
    public function render()
    {
        return view('livewire.product-detail');
    }
}
