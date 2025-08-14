<?php

namespace App\Filament\Seller\Resources\OrderProductResource\Pages;

use App\Filament\Seller\Resources\OrderProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderProducts extends ListRecords
{
    protected static string $resource = OrderProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
