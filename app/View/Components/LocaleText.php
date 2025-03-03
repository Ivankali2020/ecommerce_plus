<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LocaleText extends Component
{
    public function render()
    {
        return view('components.locale-text');
    }

    public function isMyanmar(): bool
    {
        return app()->currentLocale() === 'my';
    }
}
