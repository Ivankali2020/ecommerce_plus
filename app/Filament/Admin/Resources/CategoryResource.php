<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Filament\Admin\Resources\CategoryResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Category;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Business Section';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()->schema([
                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make()->schema([

                            Forms\Components\Toggle::make('is_sub_category')->live()
                                ->hidden(fn (Page $livewire) => $livewire instanceof EditRecord)
                                ->helperText(__('filament/resources/category-resource.form.helper_text.sub_category'))
                                ->label(__('filament/resources/category-resource.form.label.is_sub_category')),

                            Forms\Components\Select::make('parent_id')->label(__('filament/resources/category-resource.form.label.parent_category'))
                                ->options(Category::where('seller_id', CheckFilamentSellerUser::getSellerId())->pluck('name', 'id'))
                                ->hidden(fn (Page $livewire) => $livewire instanceof EditRecord)
                                ->visible(fn (Forms\Get $get, Page $livewire) => ($get('is_sub_category')))
                                ->preload()->searchable(),

                            Forms\Components\Select::make('seller_id')
                                ->searchable()->preload()->hidden(Filament::getAuthGuard() == 'seller')
                                ->required()->relationship('seller', 'name')
                                ->label(__('filament/resources/category-resource.form.label.seller')),

                            Forms\Components\TextInput::make('name')
                                ->helperText(__('filament/resources/category-resource.form.helper_text.name'))
                                ->required()
                                ->maxLength(255)
                                ->label(__('filament/resources/category-resource.form.label.name')),

                            Forms\Components\Textarea::make('description')
                                ->maxLength(16777215)
                                ->columnSpanFull()
                                ->label(__('filament/resources/category-resource.form.label.description')),
                        ]),
                    ])->columnSpan(1),

                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')->hidden(Filament::getAuthGuard() == 'seller')
                    ->sortable()
                    ->label(__('filament/resources/category-resource.table.columns.seller')),

                Tables\Columns\TextColumn::make('name')
                    ->description(fn (Category $category) => Str::limit($category->description, 80))
                    ->searchable()
                    ->label(__('filament/resources/category-resource.table.columns.name')),

                Tables\Columns\TextColumn::make('subCategories.name')
                    ->badge()
                    ->label(__('filament/resources/category-resource.table.columns.sub_categories')),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/category-resource.table.columns.deleted_at')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/category-resource.table.columns.created_at')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/category-resource.table.columns.updated_at')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CategoriesRelationManager::class,
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
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
