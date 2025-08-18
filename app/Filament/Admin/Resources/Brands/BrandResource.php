<?php

namespace App\Filament\Admin\Resources\Brands;

use App\Filament\Admin\Resources\Brands\Pages\CreateBrand;
use App\Filament\Admin\Resources\Brands\Pages\EditBrand;
use App\Filament\Admin\Resources\Brands\Pages\ListBrands;
use App\Models\Brand;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Business Section';

    public static function getModelLabel(): string
    {
        return __('filament/resources/brand-resource.model.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/brand-resource.model.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make()->schema([
                        Select::make('seller_id')
                            ->searchable()->preload()->hidden(Filament::getAuthGuard() == 'seller')
                            ->required()->relationship('seller', 'name')
                            ->label(__('filament/resources/brand-resource.form.seller')),

                        TextInput::make('name')
                            ->helperText(__('filament/resources/brand-resource.form.name_helper'))
                            ->required()
                            ->maxLength(255)
                            ->label(__('filament/resources/brand-resource.form.name')),

                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logos')->imageEditor()
                            ->image()->rules('required')
                            ->label(__('filament/resources/brand-resource.form.logo')),

                        Textarea::make('description')
                            ->maxLength(16777215)
                            ->columnSpanFull()
                            ->label(__('filament/resources/brand-resource.form.description')),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('thumb')->collection('logos'),
                TextColumn::make('seller.name')
                    ->numeric()->hidden(Filament::getAuthGuard() == 'seller')
                    ->sortable()
                    ->label(__('filament/resources/brand-resource.table.columns.seller')),
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('filament/resources/brand-resource.table.columns.name')),
                TextColumn::make('description')
                    ->searchable()
                    ->label(__('filament/resources/brand-resource.table.columns.description')),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/brand-resource.table.columns.deleted_at')),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/brand-resource.table.columns.created_at')),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/brand-resource.table.columns.updated_at')),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('id', 'desc')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
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
