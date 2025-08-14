<?php

namespace App\Filament\Seller\Resources\SellerAddressResource\Pages;

use App\Filament\Seller\Resources\SellerAddressResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSellerAddresses extends ListRecords
{
    protected static string $resource = SellerAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
