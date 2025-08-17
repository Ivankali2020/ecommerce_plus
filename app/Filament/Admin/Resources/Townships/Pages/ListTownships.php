<?php

namespace App\Filament\Admin\Resources\Townships\Pages;

use App\Filament\Admin\Resources\Townships\TownshipResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTownships extends ListRecords
{
    protected static string $resource = TownshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
