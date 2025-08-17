<?php

namespace App\Filament\Seller\Resources\Expanses\Pages;

use App\Filament\Seller\Resources\Expanses\ExpanseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExpanse extends EditRecord
{
    protected static string $resource = ExpanseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
