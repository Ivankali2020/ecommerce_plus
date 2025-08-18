<?php

namespace App\Filament\Seller\Resources\Suppliers\Pages;

use App\Filament\Seller\Resources\Suppliers\SupplierResource;
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
