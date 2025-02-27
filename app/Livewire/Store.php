<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class Store extends Component
{
    public $store_id;
    
    public function mount($id)
    {
        $this->store_id = $id;
    }
    public function render()
    {
        return view('livewire.store');
    }
}
