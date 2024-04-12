<?php

namespace App\Filament\Seller\Resources\OrderProductResource\Pages;

use App\Filament\Seller\Resources\OrderProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderProduct extends EditRecord
{
    protected static string $resource = OrderProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
