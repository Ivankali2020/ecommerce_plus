<?php

namespace App\Filament\Admin\Resources\SellerSubscriptions\Pages;

use App\Filament\Admin\Resources\SellerSubscriptions\SellerSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSellerSubscription extends EditRecord
{
    protected static string $resource = SellerSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
