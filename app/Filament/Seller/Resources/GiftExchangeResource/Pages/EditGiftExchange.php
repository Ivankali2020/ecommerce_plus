<?php

namespace App\Filament\Seller\Resources\GiftExchangeResource\Pages;

use App\Filament\Seller\Resources\GiftExchangeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGiftExchange extends EditRecord
{
    protected static string $resource = GiftExchangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
