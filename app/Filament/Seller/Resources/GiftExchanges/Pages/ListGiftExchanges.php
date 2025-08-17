<?php

namespace App\Filament\Seller\Resources\GiftExchangeResource\Pages;

use App\Filament\Seller\Resources\GiftExchangeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGiftExchanges extends ListRecords
{
    protected static string $resource = GiftExchangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
