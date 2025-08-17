<?php

namespace App\Filament\Seller\Resources\Expanses\Pages;

use App\Filament\Seller\Resources\Expanses\ExpanseResource;
use App\Filament\Seller\Resources\Expanses\Widgets\ExpanseChart;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpanses extends ListRecords
{
    protected static string $resource = ExpanseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            (ExpanseChart::class),
        ];
    }
}
