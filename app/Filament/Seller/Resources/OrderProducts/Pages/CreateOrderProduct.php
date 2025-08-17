<?php

namespace App\Filament\Seller\Resources\OrderProducts\Pages;

use App\Filament\Seller\Resources\OrderProducts\OrderProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderProduct extends CreateRecord
{
    protected static string $resource = OrderProductResource::class;
}
