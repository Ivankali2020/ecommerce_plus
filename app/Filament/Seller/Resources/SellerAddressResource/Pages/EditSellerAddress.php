<?php

namespace App\Filament\Seller\Resources\SellerAddressResource\Pages;

use App\Filament\Seller\Resources\SellerAddressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSellerAddress extends EditRecord
{
    protected static string $resource = SellerAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
