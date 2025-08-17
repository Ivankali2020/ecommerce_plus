<?php

namespace App\Filament\Seller\Resources\SellerAddressResource\Pages;

use App\Filament\Seller\Resources\SellerAddressResource;
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
