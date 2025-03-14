<?php

namespace App\Filament\Seller\Resources\ExpanseResource\Pages;

use App\Filament\Seller\Resources\ExpanseResource;
use App\Filament\Seller\Resources\ExpanseResource\Widgets\ExpanseChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpanses extends ListRecords
{
    protected static string $resource = ExpanseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            (ExpanseChart::class)
        ];
    }
}
