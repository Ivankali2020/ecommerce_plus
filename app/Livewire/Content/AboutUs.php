<?php

namespace App\Livewire\Content;

use App\Models\Setting;
use Livewire\Component;

class AboutUs extends Component
{
    public $setting;

    public $content = '';

    public function mount()
    {
        // Get the current tenant's setting
        $this->setting = Setting::first();
        $this->content = $this->setting?->about_us ?? '';
    }

    public function render()
    {
        return view('livewire.content.about-us');
    }
}
