<?php

namespace App\Helper;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AttributeType: string implements HasColor, HasIcon, HasLabel
{
    case Color = 'color';
    case Text = 'text';


    public function getLabel(): string
    {
        return match ($this) {
            self::Color => 'Color',
            self::Text => 'Text',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Color => 'info',
            self::Text => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Color => 'heroicon-m-swatch',
            self::Text => 'heroicon-m-document-text',
        };
    }
}
