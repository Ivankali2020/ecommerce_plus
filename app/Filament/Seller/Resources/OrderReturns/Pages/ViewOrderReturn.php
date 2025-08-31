<?php

namespace App\Filament\Seller\Resources\OrderReturns\Pages;

use App\Filament\Seller\Resources\OrderReturns\OrderReturnResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrderReturn extends ViewRecord
{
    protected static string $resource = OrderReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
