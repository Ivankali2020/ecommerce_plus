<?php

namespace App\Filament\Seller\Resources\ExpanseResource\Pages;

use App\Filament\Seller\Resources\ExpanseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpanse extends EditRecord
{
    protected static string $resource = ExpanseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
