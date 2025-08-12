<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DeliveryResource\Pages;
use App\Models\Delivery;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $navigationGroup = 'Order Section';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/delivery-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/delivery-resource.model.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/delivery-resource.model.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\Select::make('seller_id')
                            ->required()->searchable()->preload()
                            ->hidden(Filament::getAuthGuard() == 'seller')
                            ->relationship('seller', 'name')
                            ->label(__('filament/resources/delivery-resource.form.fields.name')),

                        Forms\Components\Select::make('region_id')->live()
                            ->required()->searchable()->preload()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('township_id', null))
                            ->relationship('region', 'mm_name')
                            ->label(__('filament/resources/delivery-resource.form.fields.region')),

                        Forms\Components\Select::make('township_id')
                            ->hidden(fn (Forms\Get $get) => blank($get('region_id')))
                            ->required()->searchable()->preload()
                            ->relationship('township', 'mm_name', modifyQueryUsing: fn ($query, Forms\Get $get) => $query->where('region_id', $get('region_id')))
                            ->label(__('filament/resources/delivery-resource.form.fields.township')),

                        Forms\Components\TextInput::make('fees')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->label(__('filament/resources/delivery-resource.form.fields.delivery_fee')),
                    ]),
                ]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make(__('filament/resources/delivery-resource.form.fields.shipping_information'))->schema([
                        Forms\Components\Toggle::make('is_cod')
                            ->label(__('filament/resources/delivery-resource.form.fields.cash_on_delivery'))
                            ->offIcon('heroicon-m-squares-2x2')
                            ->onIcon('heroicon-m-bolt')
                            ->required(),

                        Forms\Components\Toggle::make('is_pick_up')
                            ->label(__('filament/resources/delivery-resource.form.fields.self_pickup'))
                            ->offIcon('heroicon-m-squares-2x2')
                            ->onIcon('heroicon-m-shield-exclamation')
                            ->required(),

                        Forms\Components\TextInput::make('remark')
                            ->label(__('filament/resources/delivery-resource.form.fields.remark'))
                            ->maxLength(255),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->label(__('filament/resources/delivery-resource.table.columns.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('region.mm_name')
                    ->label(__('filament/resources/delivery-resource.table.columns.region'))
                    ->description(fn (Delivery $delivery) => $delivery->region->en_name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('township.mm_name')
                    ->label(__('filament/resources/delivery-resource.table.columns.township'))
                    ->description(fn (Delivery $delivery) => $delivery->region->en_name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('fees')
                    ->label(__('filament/resources/delivery-resource.table.columns.delivery_fee'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_cod')
                    ->label(__('filament/resources/delivery-resource.table.columns.cod'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_pick_up')
                    ->label(__('filament/resources/delivery-resource.table.columns.self_pickup'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('remark')
                    ->label(__('filament/resources/delivery-resource.table.columns.remark'))
                    ->searchable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveries::route('/'),
            'create' => Pages\CreateDelivery::route('/create'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
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
