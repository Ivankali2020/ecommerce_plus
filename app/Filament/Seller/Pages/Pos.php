<?php

namespace App\Filament\Seller\Pages;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class Pos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.seller.pages.pos';

    public function categoryAction(): Action
    {
        return Action::make('category')
//            ->requiresConfirmation()
            ->form([
                TextInput::make('name')
            ])
            ->modalHeading('အမျိုးအစားအလိုက်ရှာရန်')
//            ->modalDescription('အရေးပေါ် အခန်းသို့ ကြွရောက်ပေးပါရန်')
            ->color('success')->outlined()
            ->action(function (array $arguments){
                dd($arguments);
            });
    }
}
