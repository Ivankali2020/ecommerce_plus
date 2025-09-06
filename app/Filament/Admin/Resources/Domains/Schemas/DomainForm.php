<?php

namespace App\Filament\Admin\Resources\Domains\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DomainForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('domain')
                    ->label('Domain')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('example.com')
                    ->helperText('Enter the domain name for the tenant.'),
            ]);
    }
}
