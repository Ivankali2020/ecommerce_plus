<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\PointTransactionResource\Pages;
use App\Models\PointTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PointTransactionResource extends Resource
{
    protected static ?string $model = PointTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-up';

    protected static ?string $navigationGroup = 'Loyalty Program';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/point-transaction-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/point-transaction-resource.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/point-transaction-resource.navigation.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.user'))
                    ->relationship('user', 'name')
                    ->required(),

                Forms\Components\TextInput::make('type')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.type'))
                    ->required(),
                Forms\Components\TextInput::make('points')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.points'))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('balance_after')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.balance_after'))
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('reference_type')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.reference_type'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference_id')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.reference_id'))
                    ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.description'))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('metadata')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.metadata')),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.expires_at')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.user'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-m-user')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'earned' => 'success',
                        'spent' => 'warning',
                        'expired' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'earned' => 'heroicon-m-plus-circle',
                        'spent' => 'heroicon-m-minus-circle',
                        'expired' => 'heroicon-m-clock',
                        'refunded' => 'heroicon-m-arrow-uturn-left',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('points')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.points'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->weight('bold')
                    ->color(fn ($record): string => $record->type === 'earned' ? 'success' : ($record->type === 'spent' ? 'warning' : 'gray'))
                    ->formatStateUsing(
                        fn ($record): string => ($record->type === 'earned' ? '+' : ($record->type === 'spent' ? '-' : '')).
                            number_format(abs($record->points))
                    ),

                Tables\Columns\TextColumn::make('balance_after')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.balance_after'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color('primary')
                    ->weight('medium')
                    ->formatStateUsing(fn ($state): string => number_format($state)),

                Tables\Columns\TextColumn::make('reference_type')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.reference_type'))
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(
                        fn (?string $state): string => $state ? str_replace('App\\Models\\', '', $state) : 'N/A'
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reference_id')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.reference_id'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color('gray')
                    ->formatStateUsing(fn (?int $state): string => $state ? '#'.$state : 'N/A')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.description'))
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    })
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.expires_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->color(
                        fn ($record): string => $record->expires_at && $record->expires_at->isPast() ? 'danger' : 'gray'
                    )
                    ->icon(
                        fn ($record): ?string => $record->expires_at && $record->expires_at->isPast() ? 'heroicon-m-exclamation-triangle' : null
                    )
                    ->iconColor('danger')
                    ->placeholder('Never')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.created_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->color('gray')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.updated_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->color('gray')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('filament/resources/point-transaction-resource.table.filters.type.label'))
                    ->options([
                        'earned' => 'Earned',
                        'spent' => 'Spent',
                        'expired' => 'Expired',
                        'refunded' => 'Refunded',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('has_expiry')
                    ->label(__('filament/resources/point-transaction-resource.table.filters.has_expiry.label'))
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('expires_at')),

                Tables\Filters\Filter::make('expired')
                    ->label(__('filament/resources/point-transaction-resource.table.filters.expired.label'))
                    ->query(fn(Builder $query): Builder => $query->where('expires_at', '<', now())),

                Tables\Filters\Filter::make('created_at')
                    ->label(__('filament/resources/point-transaction-resource.table.filters.date_range.label'))
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('filament/resources/point-transaction-resource.table.filters.date_range.from')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('filament/resources/point-transaction-resource.table.filters.date_range.until')),
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('filament/resources/point-transaction-resource.actions.view'))
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->label(__('filament/resources/point-transaction-resource.actions.edit'))
                    ->color('warning'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('filament/resources/point-transaction-resource.actions.delete')),
                ]),
            ])
            ->emptyStateHeading(__('filament/resources/point-transaction-resource.empty_state.heading'))
            ->emptyStateDescription(__('filament/resources/point-transaction-resource.empty_state.description'))
            ->emptyStateIcon('heroicon-o-bars-arrow-up')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListPointTransactions::route('/'),
            'create' => Pages\CreatePointTransaction::route('/create'),
            'edit' => Pages\EditPointTransaction::route('/{record}/edit'),
        ];
    }
}
