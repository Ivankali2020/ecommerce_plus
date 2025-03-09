<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\BrandResource\Pages;
use App\Filament\Seller\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Business Section';

    public static function getModelLabel(): string
    {
        return __('filament/resources/brand-resource.navigation.label');
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return __("filament/resources/brand-resource.navigation.group");
    // }

    public static function form(Form $form): Form
    {
        return \App\Filament\Admin\Resources\BrandResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\BrandResource::table($table);
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id',Filament::auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
