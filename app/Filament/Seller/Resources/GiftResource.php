<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\GiftResource\Pages;
use App\Filament\Seller\Resources\GiftResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Gift;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GiftResource extends Resource
{
    protected static ?string $model = Gift::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Loyalty Program';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Gift Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Coffee Cup, T-Shirt, Hat'),

                        Forms\Components\Textarea::make('description')
                            ->placeholder('Describe the gift product...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                            ->collection('images')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),

                    ])->columns(2),

                Forms\Components\Section::make('Points & Stock')
                    ->schema([
                        Forms\Components\TextInput::make('points_required')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->suffix('points')
                            ->helperText('How many points needed to exchange for this gift'),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(1)
                            ->suffix('items')
                            ->helperText('Available quantity'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Available for Exchange')
                            ->default(true)
                            ->helperText('Enable/disable this gift for customer exchanges'),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\KeyValue::make('additional_data')
                            ->label('Extra Details')
                            ->helperText('Add size, color, or other specifications')
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
                    ->collection('images')
                    ->size(60),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('points_required')
                    ->suffix(' points')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->suffix(' items')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn($state) => $state <= 5 ? 'warning' : 'success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Tables\Columns\TextColumn::make('giftExchanges_count')
                    ->counts('giftExchanges')
                    ->label('Exchanged')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All gifts')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\Filter::make('low_stock')
                    ->query(fn(Builder $query): Builder => $query->where('stock_quantity', '<=', 5))
                    ->label('Low Stock (≤5 items)'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
