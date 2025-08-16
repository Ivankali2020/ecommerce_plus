<?php

namespace App\Livewire\Content;

use App\Models\Setting;
use Livewire\Component;

class ReturnsExchanges extends Component
{
    public $setting;

    public $content = '';

    public function mount()
    {
        // Get the current tenant's setting
        $this->setting = Setting::first();
        $this->content = $this->setting?->returns_exchanges ?? '';
    }

    public function render()
    {
        return view('livewire.content.returns-exchanges');
    }
}
