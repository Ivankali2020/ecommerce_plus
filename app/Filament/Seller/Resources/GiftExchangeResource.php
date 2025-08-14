<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\GiftExchangeResource\Pages;
use App\Filament\Seller\Resources\GiftExchangeResource\RelationManagers;
use App\Models\GiftExchange;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GiftExchangeResource extends Resource
{
    protected static ?string $model = GiftExchange::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift-top';

    protected static ?string $navigationGroup = 'Loyalty Program';

    protected static ?string $navigationLabel = 'Gift Exchanges';

    protected static ?string $modelLabel = 'Gift Exchange';

    protected static ?string $pluralModelLabel = 'Gift Exchanges';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Exchange Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('gift_id')
                            ->relationship('gift', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('points_spent')
                            ->required()
                            ->numeric()
                            ->disabled()
                            ->suffix('points'),
                    ])->columns(3),

                Forms\Components\Section::make('Status & Tracking')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\DateTimePicker::make('processed_at')
                            ->label('Processed At'),
                        Forms\Components\DateTimePicker::make('shipped_at')
                            ->label('Shipped At'),
                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->label('Delivered At'),
                    ])->columns(2),

                Forms\Components\Section::make('Delivery Information')
                    ->schema([
                        Forms\Components\Textarea::make('delivery_address')
                            ->label('Delivery Address')
                            ->rows(3)
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Exchange Notes')
                            ->rows(3),
                    ])->columns(1),

                Forms\Components\Section::make('System Information')
                    ->schema([
                        Forms\Components\Select::make('seller_id')
                            ->relationship('seller', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('tenant_id')
                            ->relationship('tenant', 'id')
                            ->disabled(),
                        Forms\Components\Select::make('point_transaction_id')
                            ->relationship('pointTransaction', 'id')
                            ->disabled(),
                        Forms\Components\Textarea::make('gift_snapshot')
                            ->label('Gift Snapshot (JSON)')
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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('gift.name')
                    ->label('Gift')
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('points_spent')
                    ->label('Points')
                    ->numeric()
                    ->sortable()
                    ->suffix(' pts')
                    ->alignEnd(),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->selectablePlaceholder(false)
                    ->rules(['required']),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ordered')
                    ->dateTime('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Processed')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('shipped_at')
                    ->label('Shipped')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('delivered_at')
                    ->label('Delivered')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Ordered From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Ordered Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('gift')
                    ->relationship('gift', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->color('warning'),
                Tables\Actions\Action::make('mark_processed')
                    ->label('Mark as Processed')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (GiftExchange $record) {
                        $record->update([
                            'status' => 'processing',
                            'processed_at' => now(),
                        ]);
                    })
                    ->visible(fn(GiftExchange $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('mark_shipped')
                    ->label('Mark as Shipped')
                    ->icon('heroicon-m-truck')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (GiftExchange $record) {
                        $record->update([
                            'status' => 'shipped',
                            'shipped_at' => now(),
                        ]);
                    })
                    ->visible(fn(GiftExchange $record) => in_array($record->status, ['processing', 'pending'])),
                Tables\Actions\Action::make('mark_delivered')
                    ->label('Mark as Delivered')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (GiftExchange $record) {
                        $record->update([
                            'status' => 'delivered',
                            'delivered_at' => now(),
                        ]);
                    })
                    ->visible(fn(GiftExchange $record) => $record->status === 'shipped'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Exchanges')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_processing')
                        ->label('Mark as Processing')
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create First Exchange')
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
            'index' => Pages\ListGiftExchanges::route('/'),
            'create' => Pages\CreateGiftExchange::route('/create'),
            'view' => Pages\ViewGiftExchange::route('/{record}'),
            'edit' => Pages\EditGiftExchange::route('/{record}/edit'),
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
            'Points' => number_format($record->points_spent) . ' pts',
            'Status' => ucfirst($record->status),
        ];
    }
}
