<?php

namespace App\Filament\Seller\Resources\Categories;

use App\Filament\Admin\Resources\Categories\RelationManagers\CategoriesRelationManager;
use App\Filament\Seller\Resources\Categories\Pages\CreateCategory;
use App\Filament\Seller\Resources\Categories\Pages\EditCategory;
use App\Filament\Seller\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
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

    protected static string|\UnitEnum|null $navigationGroup = 'Business Section';

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Admin\Resources\Categories\CategoryResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\Categories\CategoryResource::table($table);
    }

    public static function getRelations(): array
    {
        return [
            CategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
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
