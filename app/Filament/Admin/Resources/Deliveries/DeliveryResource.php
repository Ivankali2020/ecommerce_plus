<?php

namespace App\Filament\Admin\Resources\Deliveries;

use App\Filament\Admin\Resources\Deliveries\Pages\CreateDelivery;
use App\Filament\Admin\Resources\Deliveries\Pages\EditDelivery;
use App\Filament\Admin\Resources\Deliveries\Pages\ListDeliveries;
use App\Models\Delivery;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Order Section';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make()->schema([
                        Select::make('seller_id')
                            ->required()->searchable()->preload()
                            ->hidden(Filament::getAuthGuard() == 'seller')
                            ->relationship('seller', 'name')
                            ->label(__('filament/resources/delivery-resource.form.fields.name')),

                        Select::make('region_id')->live()
                            ->required()->searchable()->preload()
                            ->afterStateUpdated(fn (Set $set) => $set('township_id', null))
                            ->relationship('region', 'mm_name')
                            ->label(__('filament/resources/delivery-resource.form.fields.region')),

                        Select::make('township_id')
                            ->hidden(fn (Get $get) => blank($get('region_id')))
                            ->required()->searchable()->preload()
                            ->relationship('township', 'mm_name', modifyQueryUsing: fn ($query, Get $get) => $query->where('region_id', $get('region_id')))
                            ->label(__('filament/resources/delivery-resource.form.fields.township')),

                        TextInput::make('fees')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->label(__('filament/resources/delivery-resource.form.fields.delivery_fee')),
                    ]),
                ]),

                Group::make()->schema([
                    Section::make(__('filament/resources/delivery-resource.form.fields.shipping_information'))->schema([
                        Toggle::make('is_cod')
                            ->label(__('filament/resources/delivery-resource.form.fields.cash_on_delivery'))
                            ->offIcon('heroicon-m-squares-2x2')
                            ->onIcon('heroicon-m-bolt')
                            ->required(),

                        Toggle::make('is_pick_up')
                            ->label(__('filament/resources/delivery-resource.form.fields.self_pickup'))
                            ->offIcon('heroicon-m-squares-2x2')
                            ->onIcon('heroicon-m-shield-exclamation')
                            ->required(),

                        TextInput::make('remark')
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
                TextColumn::make('seller.name')
                    ->label(__('filament/resources/delivery-resource.table.columns.name'))
                    ->sortable(),
                TextColumn::make('region.mm_name')
                    ->label(__('filament/resources/delivery-resource.table.columns.region'))
                    ->description(fn (Delivery $delivery) => $delivery->region->en_name)
                    ->sortable(),
                TextColumn::make('township.mm_name')
                    ->label(__('filament/resources/delivery-resource.table.columns.township'))
                    ->description(fn (Delivery $delivery) => $delivery->region->en_name)
                    ->sortable(),
                TextColumn::make('fees')
                    ->label(__('filament/resources/delivery-resource.table.columns.delivery_fee'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_cod')
                    ->label(__('filament/resources/delivery-resource.table.columns.cod'))
                    ->boolean(),
                IconColumn::make('is_pick_up')
                    ->label(__('filament/resources/delivery-resource.table.columns.self_pickup'))
                    ->boolean(),
                TextColumn::make('remark')
                    ->label(__('filament/resources/delivery-resource.table.columns.remark'))
                    ->searchable(),
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
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
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
            'index' => ListDeliveries::route('/'),
            'create' => CreateDelivery::route('/create'),
            'edit' => EditDelivery::route('/{record}/edit'),
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
