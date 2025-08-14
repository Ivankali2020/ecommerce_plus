<?php

namespace App\Filament\Seller\Resources\GiftResource\Pages;

use App\Filament\Seller\Resources\GiftResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGift extends EditRecord
{
    protected static string $resource = GiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
