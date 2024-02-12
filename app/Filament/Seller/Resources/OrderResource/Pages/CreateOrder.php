<?php

namespace App\Filament\Seller\Resources\OrderResource\Pages;

use App\Filament\Seller\Resources\OrderResource;
use App\Helper\CheckFilamentSellerUser;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['seller_id'] = CheckFilamentSellerUser::getSellerId();
        $data['staff_id'] = CheckFilamentSellerUser::getStaffId();
        return $data;
    }
}

