<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Admin\Resources\CategoryResource\RelationManagers\CategoriesRelationManager;
use App\Filament\Seller\Resources\CategoryResource\Pages;
use App\Filament\Seller\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/category-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/category-resource.navigation.label');
    }

    protected static ?string $navigationGroup = 'Business Section';

    public static function form(Form $form): Form
    {
        return \App\Filament\Admin\Resources\CategoryResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\CategoryResource::table($table);
    }

    public static function getRelations(): array
    {
        return [
           CategoriesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
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
