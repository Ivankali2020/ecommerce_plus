<?php

namespace App\Filament\Seller\Resources\SellerAddresses\Pages;

use App\Filament\Seller\Resources\SellerAddresses\SellerAddressResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSellerAddresses extends ListRecords
{
    protected static string $resource = SellerAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
