<?php

namespace App\Filament\Seller\Resources\SellerAddresses;

use App\Filament\Seller\Resources\SellerAddresses\Pages\CreateSellerAddress;
use App\Filament\Seller\Resources\SellerAddresses\Pages\EditSellerAddress;
use App\Filament\Seller\Resources\SellerAddresses\Pages\ListSellerAddresses;
use App\Helper\CheckFilamentSellerUser;
use App\Models\SellerAddress;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SellerAddressResource extends Resource
{
    protected static ?string $model = SellerAddress::class;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $navigationLabel = null;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    public static function getModelLabel(): string
    {
        return __('filament/resources.seller-address.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources.seller-address.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/resources.seller-address.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('region_id')->live()
                    ->required()->searchable()->preload()
                    ->afterStateUpdated(fn (Set $set) => $set('township_id', null))
                    ->relationship('region', 'mm_name')
                    ->label(__('filament/resources.seller-address.form.region_id')),

                Select::make('township_id')
                    ->hidden(fn (Get $get) => blank($get('region_id')))
                    ->required()->searchable()->preload()
                    ->relationship('township', 'mm_name', modifyQueryUsing: fn ($query, Get $get) => $query->where('region_id', $get('region_id')))
                    ->label(__('filament/resources.seller-address.form.township_id')),

                Textarea::make('address')
                    ->required()
                    ->placeholder(__('filament/resources.seller-address.form.address_placeholder'))
                    ->label(__('filament/resources.seller-address.form.address'))
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('region.mm_name')
                    ->numeric()
                    ->sortable()
                    ->label(__('filament/resources.seller-address.table.region')),
                TextColumn::make('township.mm_name')
                    ->numeric()
                    ->sortable()
                    ->label(__('filament/resources.seller-address.table.township')),
                TextColumn::make('address')
                    ->searchable()
                    ->label(__('filament/resources.seller-address.table.address')),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListSellerAddresses::route('/'),
            'create' => CreateSellerAddress::route('/create'),
            'edit' => EditSellerAddress::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id', CheckFilamentSellerUser::getSellerId());
    }
}
