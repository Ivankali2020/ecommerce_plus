<?php

namespace App\Filament\Seller\Resources\ProductResource\Pages;

use App\Filament\Seller\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    // protected function getFormActions(): array
    // {
    //     return [
    //         $this->getSaveFormAction()->extraAttributes(['style' => 'display:none']),
    //         $this->getCancelFormAction()->extraAttributes(['style' => 'display:none']),
    //     ];
    // }

    protected function getHeaderActions(): array
    {
        return [
           $this->getSaveFormAction()
                ->submit(null)
                ->action('save'),
            $this->getCancelFormAction(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
