<?php

namespace App\Filament\Seller\Resources\Printers\Pages;

use App\Filament\Seller\Resources\Printers\PrinterResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePrinter extends CreateRecord
{
    protected static string $resource = PrinterResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::guard('seller')->user();
        $data['seller_id'] = $user->parent_id ?? $user->id;
        
        // Set tenant_id if available in the context
        if (tenant()) {
            $data['tenant_id'] = tenant()->id;
        }

        return $data;
    }
}
