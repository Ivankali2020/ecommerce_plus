<?php

namespace App\Livewire;

use App\Models\Seller;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Slider extends Component
{
    public $sliders;

    public function mount()
    {
        $seller = Cache::remember('seller_'.tenant('id'), 60, function () {
            return Seller::where('tenant_id', tenant('id'))->whereNull('parent_id')->with('media')->first();
        });
        $this->sliders = $seller->getMedia('cover_photos');
    }

    public function render()
    {
        return view('livewire.slider');
    }
}
