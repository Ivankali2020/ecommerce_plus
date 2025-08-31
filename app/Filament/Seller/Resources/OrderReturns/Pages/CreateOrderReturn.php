<?php

namespace App\Filament\Seller\Resources\OrderReturns\Pages;

use App\Filament\Seller\Resources\OrderReturns\OrderReturnResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderReturn extends CreateRecord
{
    protected static string $resource = OrderReturnResource::class;
}
