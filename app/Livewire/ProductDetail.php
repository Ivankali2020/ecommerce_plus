<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;

    public string $type;

    public function mount($product,$type)
    {
        $this->product = $product;
        $this->type = $type;
    }

    public function render()
    {
        return view('livewire.product-detail');
    }
}
