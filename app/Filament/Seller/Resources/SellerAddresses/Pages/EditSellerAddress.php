<?php

namespace App\Filament\Seller\Resources\SellerAddresses\Pages;

use App\Filament\Seller\Resources\SellerAddresses\SellerAddressResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSellerAddress extends EditRecord
{
    protected static string $resource = SellerAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
