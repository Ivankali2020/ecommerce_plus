<?php

namespace App\Livewire\Content;

use App\Models\Setting;
use Livewire\Component;

class PrivacyPolicy extends Component
{
    public $setting;

    public $content = '';

    public function mount()
    {
        // Get the current tenant's setting
        $this->setting = Setting::first();
        $this->content = $this->setting?->privacy_policy ?? '';
    }

    public function render()
    {
        return view('livewire.content.privacy-policy');
    }
}
