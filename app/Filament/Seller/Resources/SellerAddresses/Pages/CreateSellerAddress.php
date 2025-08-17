<?php

namespace App\Filament\Seller\Resources\SellerAddresses\Pages;

use App\Filament\Seller\Resources\SellerAddresses\SellerAddressResource;
use App\Helper\CheckFilamentSellerUser;
use Filament\Resources\Pages\CreateRecord;

class CreateSellerAddress extends CreateRecord
{
    protected static string $resource = SellerAddressResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['seller_id'] = CheckFilamentSellerUser::getSellerId();

        return $data;
    }
}
