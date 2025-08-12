<?php

namespace App\Livewire;

use App\Models\Seller;
use App\Models\Tenant;
use Livewire\Component;

class Slider extends Component
{
    public $sliders;

    public function mount()
    {
        $seller = Tenant::find(tenant('id'))->sellers()->first();
        $this->sliders = $seller->getMedia('cover_photos');
    }

    public function render()
    {
        return view('livewire.slider');
    }
}
