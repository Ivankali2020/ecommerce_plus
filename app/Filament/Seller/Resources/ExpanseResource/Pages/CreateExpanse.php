<?php

namespace App\Filament\Seller\Resources\ExpanseResource\Pages;

use App\Filament\Seller\Resources\ExpanseResource;
use App\Helper\CheckFilamentSellerUser;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpanse extends CreateRecord
{
    protected static string $resource = ExpanseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['seller_id'] = CheckFilamentSellerUser::getSellerId();
        return $data;
    }
}
