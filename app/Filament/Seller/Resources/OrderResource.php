<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\OrderResource\Pages;
use App\Filament\Seller\Resources\OrderResource\RelationManagers;
use App\Filament\Seller\Resources\OrderResource\Widgets\OrderChart;
use App\Helper\CheckFilamentSellerUser;
use App\Helper\OrderStatus;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

//  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Order Section';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/order-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/order-resource.navigation.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/order-resource.navigation.plural');
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('seller_id',CheckFilamentSellerUser::getSellerId())->where('status',OrderStatus::New)->count();
    }

    public static function form(Form $form): Form
    {
        return \App\Filament\Admin\Resources\OrderResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\OrderResource::table($table);
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrderResource\Widgets\OrderChart::class
        ];
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('seller_id',CheckFilamentSellerUser::getSellerId());
    }
}
