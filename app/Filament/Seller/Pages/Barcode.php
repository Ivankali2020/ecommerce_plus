<?php

namespace App\Filament\Seller\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Pages\Page;

class Barcode extends Page
{
    use HasPageShield;
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Business Section';

    protected static string $view = 'filament.seller.pages.barcode';
}
