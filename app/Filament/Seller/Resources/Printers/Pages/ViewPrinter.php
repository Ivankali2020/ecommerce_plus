<?php

namespace App\Filament\Seller\Resources\Printers\Pages;

use App\Filament\Seller\Resources\Printers\PrinterResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrinter extends ViewRecord
{
    protected static string $resource = PrinterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
