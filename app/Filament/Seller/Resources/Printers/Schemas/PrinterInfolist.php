<?php

namespace App\Filament\Seller\Resources\Printers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PrinterInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.id'),
                TextEntry::make('seller.name'),
                TextEntry::make('name'),
                TextEntry::make('connection_type'),
                TextEntry::make('ip_address'),
                TextEntry::make('port')
                    ->numeric(),
                TextEntry::make('usb_path'),
                IconEntry::make('is_default')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
