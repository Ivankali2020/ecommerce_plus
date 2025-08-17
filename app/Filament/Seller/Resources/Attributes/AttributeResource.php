<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\AttributeResource\Pages\CreateAttribute;
use App\Filament\Seller\Resources\AttributeResource\Pages\EditAttribute;
use App\Filament\Seller\Resources\AttributeResource\Pages\ListAttributes;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Attribute;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Business Section';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/attribute-resource.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/attribute-resource.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Admin\Resources\AttributeResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\AttributeResource::table($table);
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
            'index' => ListAttributes::route('/'),
            'create' => CreateAttribute::route('/create'),
            'edit' => EditAttribute::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id', CheckFilamentSellerUser::getSellerId())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
