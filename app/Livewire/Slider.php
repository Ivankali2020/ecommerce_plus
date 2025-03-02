<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seller;

class Slider extends Component
{
    public  $sliders;

    public function mount(Seller $seller)
    {
        $this->sliders = $seller->getMedia('cover_photos');
    }

    public function render()
    {
        return view('livewire.slider');
    }
}
