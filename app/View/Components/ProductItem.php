<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProductItem extends Component
{
    public $product;

    public $type;

    public function __construct($product, $type = 'default')
    {
        $this->product = $product;
        $this->type = $type;
    }

    public function render()
    {
        return view('components.product-item');
    }
}
