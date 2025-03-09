<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BrandResource\Pages;
use App\Filament\Admin\Resources\BrandResource\RelationManagers;
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
        return __('filament/resources/brand-resource.model.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/brand-resource.model.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\Select::make('seller_id')
                            ->searchable()->preload()->hidden(Filament::getAuthGuard() == 'seller')
                            ->required()->relationship('seller', 'name')
                            ->label(__('filament/resources/brand-resource.form.seller')),

                        Forms\Components\TextInput::make('name')
                            ->helperText(__('filament/resources/brand-resource.form.name_helper'))
                            ->required()
                            ->maxLength(255)
                            ->label(__('filament/resources/brand-resource.form.name')),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logos')->imageEditor()
                            ->image()->rules('required')
                            ->label(__('filament/resources/brand-resource.form.logo')),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(16777215)
                            ->columnSpanFull()
                            ->label(__('filament/resources/brand-resource.form.description')),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('thumb')->collection('logos'),
                Tables\Columns\TextColumn::make('seller.name')
                    ->numeric()->hidden(Filament::getAuthGuard() == 'seller')
                    ->sortable()
                    ->label(__('filament/resources/brand-resource.table.columns.seller')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('filament/resources/brand-resource.table.columns.name')),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->label(__('filament/resources/brand-resource.table.columns.description')),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/brand-resource.table.columns.deleted_at')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/brand-resource.table.columns.created_at')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/brand-resource.table.columns.updated_at')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('id', 'desc')
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
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
