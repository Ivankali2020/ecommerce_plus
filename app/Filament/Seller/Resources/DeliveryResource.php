<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\DeliveryResource\Pages\CreateDelivery;
use App\Filament\Seller\Resources\DeliveryResource\Pages\EditDelivery;
use App\Filament\Seller\Resources\DeliveryResource\Pages\ListDeliveries;
use App\Models\Delivery;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    // protected static ?string $navigationGroup = 'Payment Section';
    public static function getNavigationGroup(): string
    {
        return __('filament/resources/payment-resource.navigation.group');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/delivery-resource.model.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/delivery-resource.model.plural_label');
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return __("filament/resources/delivery-resource.navigation.group");
    // }

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Admin\Resources\DeliveryResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\DeliveryResource::table($table);
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
            'index' => ListDeliveries::route('/'),
            'create' => CreateDelivery::route('/create'),
            'edit' => EditDelivery::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id', Filament::auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
