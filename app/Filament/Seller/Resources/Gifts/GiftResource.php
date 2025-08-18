<?php

namespace App\Filament\Seller\Resources\Gifts;

use App\Filament\Seller\Resources\Gifts\Pages\CreateGift;
use App\Filament\Seller\Resources\Gifts\Pages\EditGift;
use App\Filament\Seller\Resources\Gifts\Pages\ListGifts;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Gift;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GiftResource extends Resource
{
    protected static ?string $model = Gift::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static string|\UnitEnum|null $navigationGroup = 'Loyalty Program';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/gift-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/gift-resource.navigation.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/gift-resource.navigation.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament/resources/gift-resource.form.sections.gift_details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament/resources/gift-resource.form.name.label'))
                            ->placeholder(__('filament/resources/gift-resource.form.name.placeholder'))
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label(__('filament/resources/gift-resource.form.description.label'))
                            ->placeholder(__('filament/resources/gift-resource.form.description.placeholder'))
                            ->rows(3)
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('filament/resources/gift-resource.form.image.label'))
                            ->collection('images')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),

                    ])->columns(2),

                Section::make(__('filament/resources/gift-resource.form.sections.points_stock'))
                    ->schema([
                        TextInput::make('points_required')
                            ->label(__('filament/resources/gift-resource.form.points_required.label'))
                            ->helperText(__('filament/resources/gift-resource.form.points_required.helper_text'))
                            ->suffix(__('filament/resources/gift-resource.form.points_required.suffix'))
                            ->required()
                            ->numeric()
                            ->minValue(1),

                        TextInput::make('stock_quantity')
                            ->label(__('filament/resources/gift-resource.form.stock_quantity.label'))
                            ->helperText(__('filament/resources/gift-resource.form.stock_quantity.helper_text'))
                            ->suffix(__('filament/resources/gift-resource.form.stock_quantity.suffix'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(1),

                        Toggle::make('is_active')
                            ->label(__('filament/resources/gift-resource.form.is_active.label'))
                            ->helperText(__('filament/resources/gift-resource.form.is_active.helper_text'))
                            ->default(true),
                    ])->columns(2),

                Section::make(__('filament/resources/gift-resource.form.sections.additional_info'))
                    ->schema([
                        KeyValue::make('additional_data')
                            ->label(__('filament/resources/gift-resource.form.additional_data.label'))
                            ->helperText(__('filament/resources/gift-resource.form.additional_data.helper_text'))
                            ->columnSpanFull(),
                    ])->collapsible(),

                // Hidden fields
                Hidden::make('seller_id')
                    ->default(CheckFilamentSellerUser::getSellerId()),
                Hidden::make('tenant_id')
                    ->default(tenant('id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('filament/resources/gift-resource.table.columns.image'))
                    ->collection('images')
                    ->size(60),

                TextColumn::make('name')
                    ->label(__('filament/resources/gift-resource.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('points_required')
                    ->label(__('filament/resources/gift-resource.table.columns.points_required'))
                    ->suffix(' points')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('stock_quantity')
                    ->label(__('filament/resources/gift-resource.table.columns.stock_quantity'))
                    ->suffix(' items')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($state) => $state <= 5 ? 'warning' : 'success'),

                IconColumn::make('is_active')
                    ->label(__('filament/resources/gift-resource.table.columns.is_active'))
                    ->boolean(),

                TextColumn::make('giftExchanges_count')
                    ->counts('giftExchanges')
                    ->label(__('filament/resources/gift-resource.table.columns.exchanges_count'))
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label(__('filament/resources/gift-resource.table.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('filament/resources/gift-resource.table.filters.status.label'))
                    ->placeholder(__('filament/resources/gift-resource.table.filters.status.placeholder'))
                    ->trueLabel(__('filament/resources/gift-resource.table.filters.status.active'))
                    ->falseLabel(__('filament/resources/gift-resource.table.filters.status.inactive')),

                Filter::make('low_stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '<=', 5))
                    ->label(__('filament/resources/gift-resource.table.filters.low_stock.label')),
            ])
            ->recordActions([
                ViewAction::make()->label(__('filament/resources/gift-resource.actions.view')),
                EditAction::make()->label(__('filament/resources/gift-resource.actions.edit')),
                DeleteAction::make()->label(__('filament/resources/gift-resource.actions.delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('filament/resources/gift-resource.actions.delete')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('seller_id', CheckFilamentSellerUser::getSellerId());
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
            'index' => ListGifts::route('/'),
            'create' => CreateGift::route('/create'),
            'edit' => EditGift::route('/{record}/edit'),
        ];
    }
}
