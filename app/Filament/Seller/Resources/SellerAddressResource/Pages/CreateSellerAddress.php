<?php

namespace App\Filament\Seller\Resources\SellerAddressResource\Pages;

use App\Filament\Seller\Resources\SellerAddressResource;
use App\Helper\CheckFilamentSellerUser;
use Filament\Actions;
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
