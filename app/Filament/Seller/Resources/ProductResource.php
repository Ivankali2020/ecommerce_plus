<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\ProductResource\Pages;
use App\Filament\Seller\Resources\ProductResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Business Section';

    public static function form(Form $form): Form
    {
        return \App\Filament\Admin\Resources\ProductResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\ProductResource::table($table);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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
