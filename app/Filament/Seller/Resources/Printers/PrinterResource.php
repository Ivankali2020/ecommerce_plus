<?php

namespace App\Filament\Seller\Resources\Printers;

use App\Filament\Seller\Resources\Printers\Pages\CreatePrinter;
use App\Filament\Seller\Resources\Printers\Pages\EditPrinter;
use App\Filament\Seller\Resources\Printers\Pages\ListPrinters;
use App\Filament\Seller\Resources\Printers\Pages\ViewPrinter;
use App\Filament\Seller\Resources\Printers\Schemas\PrinterForm;
use App\Filament\Seller\Resources\Printers\Schemas\PrinterInfolist;
use App\Filament\Seller\Resources\Printers\Tables\PrintersTable;
use App\Models\Printer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrinterResource extends Resource
{
    protected static ?string $model = Printer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-printer';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PrinterForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrinterInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrinters::route('/'),
            'create' => CreatePrinter::route('/create'),
            'view' => ViewPrinter::route('/{record}'),
            'edit' => EditPrinter::route('/{record}/edit'),
        ];
    }
}
