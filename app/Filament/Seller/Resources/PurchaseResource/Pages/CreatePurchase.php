<?php

namespace App\Filament\Seller\Resources\PurchaseResource\Pages;

use App\Filament\Seller\Resources\PurchaseResource;
use App\Helper\CheckFilamentSellerUser;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['seller_id'] = CheckFilamentSellerUser::getSellerId();

        return $data;
    }
}
