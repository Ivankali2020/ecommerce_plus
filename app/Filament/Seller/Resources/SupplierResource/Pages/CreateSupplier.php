<?php

namespace App\Filament\Seller\Resources\SupplierResource\Pages;

use App\Filament\Seller\Resources\SupplierResource;
use App\Helper\CheckFilamentSellerUser;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['seller_id'] = CheckFilamentSellerUser::getSellerId();

        return $data;
    }
}
