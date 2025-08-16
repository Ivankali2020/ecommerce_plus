<?php

namespace App\Livewire\Content;

use App\Models\Setting;
use Livewire\Component;

class Faq extends Component
{
    public $setting;

    public $faqs = [];

    public $openFaq = null;

    public $searchTerm = '';

    public function mount()
    {
        // Get the current tenant's setting
        $this->setting = Setting::first();
        $this->faqs = $this->setting?->faq ?? [];
    }

    public function toggleFaq($index)
    {
        if ($this->openFaq === $index) {
            $this->openFaq = null;
        } else {
            $this->openFaq = $index;
        }
    }

    public function updatedSearchTerm()
    {
        $this->openFaq = null; // Close any open FAQ when searching
    }

    public function getFilteredFaqsProperty()
    {
        if (empty($this->searchTerm)) {
            return $this->faqs;
        }

        return array_filter($this->faqs, function ($faq) {
            return str_contains(strtolower($faq['question'] ?? ''), strtolower($this->searchTerm)) ||
                str_contains(strtolower(strip_tags($faq['answer'] ?? '')), strtolower($this->searchTerm));
        });
    }

    public function render()
    {
        return view('livewire.content.faq');
    }
}
