<?php

namespace App\Helper;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethodExpanse: string implements HasColor, HasIcon, HasLabel
{
    case Cash = 'cash';
    case Bank = 'bank';
    case Card = 'card';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Bank => 'Bank Transfer',
            self::Card => 'Credit/Debit Card',
            self::Other => 'Other',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Cash => 'success', // Green
            self::Bank => 'primary', // Blue
            self::Card => 'purple',  // Purple
            self::Other => 'gray',   // Gray
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Cash => 'heroicon-m-banknotes', // 🏦
            self::Bank => 'heroicon-m-building-library', // 🏛
            self::Card => 'heroicon-m-credit-card', // 💳
            self::Other => 'heroicon-m-question-mark-circle', // ❓
        };
    }

    public static function options(): array
    {
        return [
            self::Cash => 'Cash',
            self::Bank => 'Bank Transfer',
            self::Card => 'Credit/Debit Card',
            self::Other => 'Other',
        ];
    }
}
