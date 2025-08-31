<?php

namespace App\Filament\Seller\Resources\OrderReturns;

use App\Filament\Seller\Resources\OrderReturns\Pages\CreateOrderReturn;
use App\Filament\Seller\Resources\OrderReturns\Pages\EditOrderReturn;
use App\Filament\Seller\Resources\OrderReturns\Pages\ListOrderReturns;
use App\Filament\Seller\Resources\OrderReturns\Pages\ViewOrderReturn;
use App\Filament\Seller\Resources\OrderReturns\Schemas\OrderReturnForm;
use App\Filament\Seller\Resources\OrderReturns\Schemas\OrderReturnInfolist;
use App\Filament\Seller\Resources\OrderReturns\Tables\OrderReturnsTable;
use App\Models\OrderReturn;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderReturnResource extends Resource
{
    protected static ?string $model = OrderReturn::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Order Section';

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function form(Schema $schema): Schema
    {
        return OrderReturnForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderReturnInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderReturnsTable::configure($table);
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
            'index' => ListOrderReturns::route('/'),
            'create' => CreateOrderReturn::route('/create'),
            'view' => ViewOrderReturn::route('/{record}'),
            'edit' => EditOrderReturn::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
