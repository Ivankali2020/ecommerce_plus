<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\SellerAddressResource\Pages;
use App\Filament\Seller\Resources\SellerAddressResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Models\SellerAddress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SellerAddressResource extends Resource
{
    protected static ?string $model = SellerAddress::class;

    protected static ?string $modelLabel = null;
    protected static ?string $pluralModelLabel = null;
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('region_id')->live()
                    ->required()->searchable()->preload()
                    ->afterStateUpdated(fn(Forms\Set $set)=>  $set('township_id',null))
                    ->relationship('region','mm_name')
                    ->label(__('filament/resources.seller-address.form.region_id')),

                Forms\Components\Select::make('township_id')
                    ->hidden(fn(Forms\Get $get)=> blank($get('region_id')))
                    ->required()->searchable()->preload()
                    ->relationship('township','mm_name',modifyQueryUsing: fn($query,Forms\Get $get)=> $query->where('region_id',$get('region_id')))
                    ->label(__('filament/resources.seller-address.form.township_id')),

                Forms\Components\Textarea::make('address')
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
                Tables\Columns\TextColumn::make('region.mm_name')
                    ->numeric()
                    ->sortable()
                    ->label(__('filament/resources.seller-address.table.region')),
                Tables\Columns\TextColumn::make('township.mm_name')
                    ->numeric()
                    ->sortable()
                    ->label(__('filament/resources.seller-address.table.township')),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->label(__('filament/resources.seller-address.table.address')),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSellerAddresses::route('/'),
            'create' => Pages\CreateSellerAddress::route('/create'),
            'edit' => Pages\EditSellerAddress::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id',CheckFilamentSellerUser::getSellerId());
    }
}
