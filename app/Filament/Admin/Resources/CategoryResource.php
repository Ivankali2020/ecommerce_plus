<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Filament\Admin\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Grid::make()->schema([
                   Forms\Components\Group::make()->schema([
                       Forms\Components\Section::make()->schema([

                           Forms\Components\Toggle::make('is_sub_category')->live()
                                ->hidden(fn(Page $livewire) => $livewire instanceof EditRecord)
                                ->helperText('If you want to create sub category please open'),

                           Forms\Components\Select::make('parent_id')->label('Parent Category')
                               ->options(Category::pluck('name','id'))
                               ->hidden(fn(Page $livewire) => $livewire instanceof EditRecord)
                               ->visible(fn(Forms\Get $get,Page $livewire) => ($get('is_sub_category')))
                               ->preload()->searchable(),

                           Forms\Components\Select::make('seller_id')
                               ->searchable()->preload()
                               ->required()->relationship('seller','name'),

                           Forms\Components\TextInput::make('name')
                               ->helperText('NIKE , ADIDAS , PUMA,  .. ')
                               ->required()
                               ->maxLength(255),

                           Forms\Components\Textarea::make('description')
                               ->maxLength(16777215)
                               ->columnSpanFull(),
                       ])
                   ])->columnSpan(1),


               ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->description(fn(Category $category)=> Str::limit($category->description,80))
                    ->searchable(),

                Tables\Columns\TextColumn::make('subCategories.name')
                    ->sortable()->badge(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            RelationManagers\CategoriesRelationManager::class
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
//            ->whereNull('parent_id')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
