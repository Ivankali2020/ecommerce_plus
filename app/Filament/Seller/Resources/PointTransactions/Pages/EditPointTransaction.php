<?php

namespace App\Filament\Seller\Resources\PointTransactionResource\Pages;

use App\Filament\Seller\Resources\PointTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPointTransaction extends EditRecord
{
    protected static string $resource = PointTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
