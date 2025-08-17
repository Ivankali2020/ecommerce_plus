<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\GiftExchangeResource\Pages\CreateGiftExchange;
use App\Filament\Seller\Resources\GiftExchangeResource\Pages\EditGiftExchange;
use App\Filament\Seller\Resources\GiftExchangeResource\Pages\ListGiftExchanges;
use App\Filament\Seller\Resources\GiftExchangeResource\Pages\ViewGiftExchange;
use App\Models\GiftExchange;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GiftExchangeResource extends Resource
{
    protected static ?string $model = GiftExchange::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift-top';

    protected static string|\UnitEnum|null $navigationGroup = 'Loyalty Program';

    protected static ?string $navigationLabel = 'Gift Exchanges';

    protected static ?string $modelLabel = 'Gift Exchange';

    protected static ?string $pluralModelLabel = 'Gift Exchanges';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/gift-exchange-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/gift-exchange-resource.navigation.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/gift-exchange-resource.navigation.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament/resources/gift-exchange-resource.form.sections.exchange_details'))
                    ->schema([
                        Select::make('user_id')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.user'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Select::make('gift_id')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.gift'))
                            ->relationship('gift', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        TextInput::make('points_spent')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.points_spent'))
                            ->required()
                            ->numeric()
                            ->disabled()
                            ->suffix(__('filament/resources/gift-exchange-resource.form.fields.points_suffix')),
                    ])->columns(3),

                Section::make(__('filament/resources/gift-exchange-resource.form.sections.status_tracking'))
                    ->schema([
                        Select::make('status')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.status.label'))
                            ->options([
                                'pending' => __('filament/resources/gift-exchange-resource.form.fields.status.options.pending'),
                                'processing' => __('filament/resources/gift-exchange-resource.form.fields.status.options.processing'),
                                'shipped' => __('filament/resources/gift-exchange-resource.form.fields.status.options.shipped'),
                                'delivered' => __('filament/resources/gift-exchange-resource.form.fields.status.options.delivered'),
                                'cancelled' => __('filament/resources/gift-exchange-resource.form.fields.status.options.cancelled'),
                            ])
                            ->required()
                            ->native(false),
                        DateTimePicker::make('processed_at')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.processed_at')),
                        DateTimePicker::make('shipped_at')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.shipped_at')),
                        DateTimePicker::make('delivered_at')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.delivered_at')),
                    ])->columns(2),

                Section::make(__('filament/resources/gift-exchange-resource.form.sections.delivery_information'))
                    ->schema([
                        Textarea::make('delivery_address')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.delivery_address'))
                            ->rows(3)
                            ->disabled(),
                        Textarea::make('notes')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.notes'))
                            ->rows(3),
                    ])->columns(1),

                Section::make(__('filament/resources/gift-exchange-resource.form.sections.system_information'))
                    ->schema([
                        Select::make('seller_id')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.seller'))
                            ->relationship('seller', 'name')
                            ->disabled(),
                        Select::make('tenant_id')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.tenant'))
                            ->relationship('tenant', 'id')
                            ->disabled(),
                        Select::make('point_transaction_id')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.point_transaction'))
                            ->relationship('pointTransaction', 'id')
                            ->disabled(),
                        Textarea::make('gift_snapshot')
                            ->label(__('filament/resources/gift-exchange-resource.form.fields.gift_snapshot'))
                            ->rows(4)
                            ->disabled(),
                    ])->columns(2)
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('gift.name')
                    ->label(__('filament/resources/gift-exchange-resource.table.columns.gift'))
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                TextColumn::make('points_spent')
                    ->label(__('filament/resources/gift-exchange-resource.table.columns.points_spent'))
                    ->numeric()
                    ->sortable()
                    ->suffix(__('filament/resources/gift-exchange-resource.table.columns.points_suffix'))
                    ->alignEnd(),
                SelectColumn::make('status')
                    ->label(__('filament/resources/gift-exchange-resource.table.columns.status.label'))
                    ->options([
                        'pending' => __('filament/resources/gift-exchange-resource.table.columns.status.options.pending'),
                        'processing' => __('filament/resources/gift-exchange-resource.table.columns.status.options.processing'),
                        'shipped' => __('filament/resources/gift-exchange-resource.table.columns.status.options.shipped'),
                        'delivered' => __('filament/resources/gift-exchange-resource.table.columns.status.options.delivered'),
                        'cancelled' => __('filament/resources/gift-exchange-resource.table.columns.status.options.cancelled'),
                    ])
                    ->selectablePlaceholder(false)
                    ->rules(['required']),
                TextColumn::make('created_at')
                    ->label(__('filament/resources/gift-exchange-resource.table.columns.created_at'))
                    ->dateTime('M d, Y')
                    ->sortable(),
                TextColumn::make('processed_at')
                    ->label(__('filament/resources/gift-exchange-resource.table.columns.processed_at'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('shipped_at')
                    ->label(__('filament/resources/gift-exchange-resource.table.columns.shipped_at'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('delivered_at')
                    ->label(__('filament/resources/gift-exchange-resource.table.columns.delivered_at'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament/resources/gift-exchange-resource.table.columns.updated_at'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament/resources/gift-exchange-resource.table.filters.status.label'))
                    ->options([
                        'pending' => __('filament/resources/gift-exchange-resource.table.filters.status.options.pending'),
                        'processing' => __('filament/resources/gift-exchange-resource.table.filters.status.options.processing'),
                        'shipped' => __('filament/resources/gift-exchange-resource.table.filters.status.options.shipped'),
                        'delivered' => __('filament/resources/gift-exchange-resource.table.filters.status.options.delivered'),
                        'cancelled' => __('filament/resources/gift-exchange-resource.table.filters.status.options.cancelled'),
                    ])
                    ->multiple(),
                Filter::make('created_at')
                    ->label(__('filament/resources/gift-exchange-resource.table.filters.date_range.label'))
                    ->schema([
                        DatePicker::make('created_from')
                            ->label(__('filament/resources/gift-exchange-resource.table.filters.date_range.from')),
                        DatePicker::make('created_until')
                            ->label(__('filament/resources/gift-exchange-resource.table.filters.date_range.until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('gift')
                    ->label(__('filament/resources/gift-exchange-resource.table.filters.gift.label'))
                    ->relationship('gift', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('filament/resources/gift-exchange-resource.actions.view'))
                    ->color('info'),
                EditAction::make()
                    ->label(__('filament/resources/gift-exchange-resource.actions.edit'))
                    ->color('warning'),
                Action::make('mark_processed')
                    ->label(__('filament/resources/gift-exchange-resource.actions.mark_processed.label'))
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (GiftExchange $record) {
                        $record->update([
                            'status' => 'processing',
                            'processed_at' => now(),
                        ]);
                    })
                    ->visible(fn (GiftExchange $record) => $record->status === 'pending'),
                Action::make('mark_shipped')
                    ->label(__('filament/resources/gift-exchange-resource.actions.mark_shipped.label'))
                    ->icon('heroicon-m-truck')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (GiftExchange $record) {
                        $record->update([
                            'status' => 'shipped',
                            'shipped_at' => now(),
                        ]);
                    })
                    ->visible(fn (GiftExchange $record) => in_array($record->status, ['processing', 'pending'])),
                Action::make('mark_delivered')
                    ->label(__('filament/resources/gift-exchange-resource.actions.mark_delivered.label'))
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (GiftExchange $record) {
                        $record->update([
                            'status' => 'delivered',
                            'delivered_at' => now(),
                        ]);
                    })
                    ->visible(fn (GiftExchange $record) => $record->status === 'shipped'),
            ])
            ->headerActions([
                Action::make('export')
                    ->label(__('filament/resources/gift-exchange-resource.actions.export.label'))
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('gray'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_processing')
                        ->label(__('filament/resources/gift-exchange-resource.actions.bulk_mark_processing'))
                        ->icon('heroicon-m-clock')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'processing',
                                    'processed_at' => now(),
                                ]);
                            });
                        }),
                    DeleteBulkAction::make()
                        ->label(__('filament/resources/gift-exchange-resource.actions.delete')),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label(__('filament/resources/gift-exchange-resource.actions.create_first'))
                    ->icon('heroicon-m-plus'),
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
            'index' => ListGiftExchanges::route('/'),
            'create' => CreateGiftExchange::route('/create'),
            'view' => ViewGiftExchange::route('/{record}'),
            'edit' => EditGiftExchange::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = static::getModel()::where('status', 'pending')->count();

        return $pendingCount > 0 ? 'warning' : null;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'gift']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'gift.name', 'status'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Customer' => $record->user->name ?? 'Unknown',
            'Gift' => $record->gift->name ?? 'Unknown',
            'Points' => number_format($record->points_spent).' pts',
            'Status' => ucfirst($record->status),
        ];
    }
}
