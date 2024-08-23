<?php

namespace App\Filament\Seller\Resources\ExchangeResource\Pages;

use App\Filament\Seller\Resources\ExchangeResource;
use App\Models\Exchange;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExchange extends EditRecord
{
    protected static string $resource = ExchangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->hidden(fn (Exchange $exchange)=> $exchange->currency->code == 'MMK')->modalDescription('ဖျက်လိုက်ပါက သက်ဆိုင်ရာ products များ အားလုံး ဖျက်သွားပါမည်'),
            Actions\ForceDeleteAction::make()->hidden(fn (Exchange $exchange)=> $exchange->currency->code == 'MMK'),
            Actions\RestoreAction::make()->hidden(fn (Exchange $exchange)=> $exchange->currency->code == 'MMK'),
        ];
    }
}
