<?php

namespace App\Filament\Seller\Resources\ExchangeResource\Pages;

use App\Filament\Seller\Resources\ExchangeResource;
use App\Models\Exchange;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditExchange extends EditRecord
{
    protected static string $resource = ExchangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->hidden(fn (Exchange $exchange) => $exchange->currency->code == 'MMK')->modalDescription('ဖျက်လိုပါက သက်ဆိုင်ရာ products မှား အားလုံး ဖျက်သွားပါမည်'),
            ForceDeleteAction::make()->hidden(fn (Exchange $exchange) => $exchange->currency->code == 'MMK'),
            RestoreAction::make()->hidden(fn (Exchange $exchange) => $exchange->currency->code == 'MMK'),
        ];
    }
}
