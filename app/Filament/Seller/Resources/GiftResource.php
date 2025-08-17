<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\GiftResource\Pages;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Gift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GiftResource extends Resource
{
    protected static ?string $model = Gift::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Loyalty Program';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament/resources/gift-resource.form.sections.gift_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament/resources/gift-resource.form.name.label'))
                            ->placeholder(__('filament/resources/gift-resource.form.name.placeholder'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label(__('filament/resources/gift-resource.form.description.label'))
                            ->placeholder(__('filament/resources/gift-resource.form.description.placeholder'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('filament/resources/gift-resource.form.image.label'))
                            ->collection('images')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),

                    ])->columns(2),

                Forms\Components\Section::make(__('filament/resources/gift-resource.form.sections.points_stock'))
                    ->schema([
                        Forms\Components\TextInput::make('points_required')
                            ->label(__('filament/resources/gift-resource.form.points_required.label'))
                            ->helperText(__('filament/resources/gift-resource.form.points_required.helper_text'))
                            ->suffix(__('filament/resources/gift-resource.form.points_required.suffix'))
                            ->required()
                            ->numeric()
                            ->minValue(1),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label(__('filament/resources/gift-resource.form.stock_quantity.label'))
                            ->helperText(__('filament/resources/gift-resource.form.stock_quantity.helper_text'))
                            ->suffix(__('filament/resources/gift-resource.form.stock_quantity.suffix'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('filament/resources/gift-resource.form.is_active.label'))
                            ->helperText(__('filament/resources/gift-resource.form.is_active.helper_text'))
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make(__('filament/resources/gift-resource.form.sections.additional_info'))
                    ->schema([
                        Forms\Components\KeyValue::make('additional_data')
                            ->label(__('filament/resources/gift-resource.form.additional_data.label'))
                            ->helperText(__('filament/resources/gift-resource.form.additional_data.helper_text'))
                            ->columnSpanFull(),
                    ])->collapsible(),

                // Hidden fields
                Forms\Components\Hidden::make('seller_id')
                    ->default(CheckFilamentSellerUser::getSellerId()),
                Forms\Components\Hidden::make('tenant_id')
                    ->default(tenant('id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('filament/resources/gift-resource.table.columns.image'))
                    ->collection('images')
                    ->size(60),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament/resources/gift-resource.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('points_required')
                    ->label(__('filament/resources/gift-resource.table.columns.points_required'))
                    ->suffix(' points')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label(__('filament/resources/gift-resource.table.columns.stock_quantity'))
                    ->suffix(' items')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($state) => $state <= 5 ? 'warning' : 'success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament/resources/gift-resource.table.columns.is_active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('giftExchanges_count')
                    ->counts('giftExchanges')
                    ->label(__('filament/resources/gift-resource.table.columns.exchanges_count'))
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament/resources/gift-resource.table.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('filament/resources/gift-resource.table.filters.status.label'))
                    ->placeholder(__('filament/resources/gift-resource.table.filters.status.placeholder'))
                    ->trueLabel(__('filament/resources/gift-resource.table.filters.status.active'))
                    ->falseLabel(__('filament/resources/gift-resource.table.filters.status.inactive')),

                Tables\Filters\Filter::make('low_stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '<=', 5))
                    ->label(__('filament/resources/gift-resource.table.filters.low_stock.label')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('filament/resources/gift-resource.actions.view')),
                Tables\Actions\EditAction::make()->label(__('filament/resources/gift-resource.actions.edit')),
                Tables\Actions\DeleteAction::make()->label(__('filament/resources/gift-resource.actions.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('filament/resources/gift-resource.actions.delete')),
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
            'index' => Pages\ListGifts::route('/'),
            'create' => Pages\CreateGift::route('/create'),
            'edit' => Pages\EditGift::route('/{record}/edit'),
        ];
    }
}
