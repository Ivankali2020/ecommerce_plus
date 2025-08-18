<?php

namespace App\Filament\Admin\Resources\Sellers\Pages;

use App\Filament\Admin\Resources\Sellers\SellerResource;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Resources\Pages\CreateRecord;

class CreateSeller extends CreateRecord
{
    protected static string $resource = SellerResource::class;

    protected function afterCreate(): void
    {
        // Get the created seller
        $seller = $this->record;

        $notification = new VerifyEmail;
        $notification->url = filament()->getPanel('seller')->getVerifyEmailUrl($seller);
        $seller->notify($notification);

        // Send verification notification specifically for Filament sellers
        // $seller->sendEmailVerificationNotification();
    }
}
