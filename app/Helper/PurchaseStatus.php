<?php

namespace App\Helper;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PurchaseStatus: string implements HasColor, HasIcon, HasLabel
{
    case Waiting = 'processing';

    case Complete = 'delivered';

    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Waiting => 'Waiting',
            self::Complete => 'Complete',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Waiting => 'warning',
            self::Complete => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Waiting => 'heroicon-m-arrow-path',
            self::Complete => 'heroicon-m-check-badge',
            self::Cancelled => 'heroicon-m-x-circle',
        };
    }
}
