<?php

namespace App\Filament\Seller\Resources\Printers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PrinterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Select::make('connection_type')
                    ->options([
                        'network' => 'Network (IP)',
                        'usb' => 'USB/Serial',
                    ])
                    ->required()
                    ->default('network'),

                TextInput::make('ip_address')
                    ->label('IP Address')
                    ->hiddenJs(<<<'JS'
                                $get('connection_type') !== 'network'
                            JS)
                    ->helperText('Required for network printers'),

                TextInput::make('port')
                    ->numeric()
                    ->hiddenJs(<<<'JS'
                                $get('connection_type') !== 'network'
                            JS)
                    ->default(9100)
                    ->helperText('Default: 9100'),

                TextInput::make('usb_path')
                    ->label('USB/Serial Path')
                    ->placeholder('/dev/ttyUSB0 or COM1')
                     ->hiddenJs(<<<'JS'
                                $get('connection_type') !== 'usb'
                            JS)
                    ->helperText('Required for USB printers'),

                Toggle::make('is_default')
                    ->label('Default Printer')
                    ->helperText('Only one printer can be default per seller'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
