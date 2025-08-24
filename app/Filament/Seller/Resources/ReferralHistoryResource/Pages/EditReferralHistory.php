<?php

namespace App\Filament\Seller\Resources\ReferralHistoryResource\Pages;

use App\Filament\Seller\Resources\ReferralHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReferralHistory extends EditRecord
{
    protected static string $resource = ReferralHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
