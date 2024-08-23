<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class BarCodePrint extends Component
{
    public $pages = 1;
    public Product $product;

    public function print($code,$name,$price)
    {
       return redirect()->route('code.print',['code' => $code,'page' => $this->pages,'price' => $price, 'name' => $name , ]);
    }

    public function render()
    {
        return view('livewire.bar-code-print');
    }
}
