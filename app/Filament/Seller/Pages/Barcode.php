<?php

namespace App\Filament\Seller\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class Barcode extends Page
{
    use HasPageShield;
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Business Section';

    protected string $view = 'filament.seller.pages.barcode';
}
