<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\AttributeResource\Pages;
use App\Filament\Seller\Resources\AttributeResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Business Section';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/attribute-resource.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/attribute-resource.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return \App\Filament\Admin\Resources\AttributeResource::form($form);
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
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id',CheckFilamentSellerUser::getSellerId())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
