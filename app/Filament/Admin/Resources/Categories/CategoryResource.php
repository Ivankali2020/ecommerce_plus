<?php

namespace App\Filament\Admin\Resources\Categories;

use App\Filament\Admin\Resources\Categories\Pages\CreateCategory;
use App\Filament\Admin\Resources\Categories\Pages\EditCategory;
use App\Filament\Admin\Resources\Categories\Pages\ListCategories;
use App\Filament\Admin\Resources\Categories\RelationManagers\CategoriesRelationManager;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Business Section';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()->schema([
                    Group::make()->schema([
                        Section::make()->schema([

                            Toggle::make('is_sub_category')->live()
                                ->hidden(fn (Page $livewire) => $livewire instanceof EditRecord)
                                ->helperText(__('filament/resources/category-resource.form.helper_text.sub_category'))
                                ->label(__('filament/resources/category-resource.form.label.is_sub_category')),

                            Select::make('parent_id')->label(__('filament/resources/category-resource.form.label.parent_category'))
                                ->options(Category::where('seller_id', CheckFilamentSellerUser::getSellerId())->pluck('name', 'id'))
                                ->hidden(fn (Page $livewire) => $livewire instanceof EditRecord)
                                ->visible(fn (Get $get, Page $livewire) => ($get('is_sub_category')))
                                ->preload()->searchable(),

                            Select::make('seller_id')
                                ->searchable()->preload()->hidden(Filament::getAuthGuard() == 'seller')
                                ->required()->relationship('seller', 'name')
                                ->label(__('filament/resources/category-resource.form.label.seller')),

                            TextInput::make('name')
                                ->helperText(__('filament/resources/category-resource.form.helper_text.name'))
                                ->required()
                                ->maxLength(255)
                                ->label(__('filament/resources/category-resource.form.label.name')),

                            Textarea::make('description')
                                ->maxLength(16777215)
                                ->columnSpanFull()
                                ->label(__('filament/resources/category-resource.form.label.description')),
                        ]),
                    ])->columnSpan(1),

                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seller.name')->hidden(Filament::getAuthGuard() == 'seller')
                    ->sortable()
                    ->label(__('filament/resources/category-resource.table.columns.seller')),

                TextColumn::make('name')
                    ->description(fn (Category $category) => Str::limit($category->description, 80))
                    ->searchable()
                    ->label(__('filament/resources/category-resource.table.columns.name')),

                TextColumn::make('subCategories.name')
                    ->badge()
                    ->label(__('filament/resources/category-resource.table.columns.sub_categories')),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/category-resource.table.columns.deleted_at')),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/category-resource.table.columns.created_at')),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/category-resource.table.columns.updated_at')),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
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
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
