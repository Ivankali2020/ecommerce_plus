<?php

namespace App\Filament\Seller\Pages;

use Filament\Pages\Page;

class Billing extends Page
{
    protected string $view = 'filament.seller.pages.billing';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    public static function canAccess(): bool
    {
        return auth()->user()->isSubscriptionExpired();
    }
}
