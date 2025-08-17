<?php

namespace App\Filament\Seller\Resources\PointTransactions;

use App\Filament\Seller\Resources\PointTransactions\Pages\CreatePointTransaction;
use App\Filament\Seller\Resources\PointTransactions\Pages\EditPointTransaction;
use App\Filament\Seller\Resources\PointTransactions\Pages\ListPointTransactions;
use App\Models\PointTransaction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PointTransactionResource extends Resource
{
    protected static ?string $model = PointTransaction::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-arrow-up';

    protected static string|\UnitEnum|null $navigationGroup = 'Loyalty Program';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.user'))
                    ->relationship('user', 'name')
                    ->required(),

                TextInput::make('type')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.type'))
                    ->required(),
                TextInput::make('points')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.points'))
                    ->required()
                    ->numeric(),
                TextInput::make('balance_after')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.balance_after'))
                    ->required()
                    ->numeric(),
                
                Textarea::make('description')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.description'))
                    ->columnSpanFull(),
                TextInput::make('metadata')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.metadata')),
                DateTimePicker::make('expires_at')
                    ->label(__('filament/resources/point-transaction-resource.form.fields.expires_at')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.user'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-m-user')
                    ->iconColor('gray'),

                TextColumn::make('type')
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

                TextColumn::make('points')
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

                TextColumn::make('balance_after')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.balance_after'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color('primary')
                    ->weight('medium')
                    ->formatStateUsing(fn ($state): string => number_format($state)),

                

                TextColumn::make('description')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.description'))
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    })
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('expires_at')
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

                TextColumn::make('created_at')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.created'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->color('gray')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('filament/resources/point-transaction-resource.table.columns.updated'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->color('gray')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label(__('filament/resources/point-transaction-resource.table.filters.type.label'))
                    ->options([
                        'earned' => 'Earned',
                        'spent' => 'Spent',
                        'expired' => 'Expired',
                        'refunded' => 'Refunded',
                    ])
                    ->multiple(),

                Filter::make('has_expiry')
                    ->label(__('filament/resources/point-transaction-resource.table.filters.has_expiry.label'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('expires_at')),

                Filter::make('expired')
                    ->label(__('filament/resources/point-transaction-resource.table.filters.expired.label'))
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now())),

                Filter::make('created_at')
                    ->label(__('filament/resources/point-transaction-resource.table.filters.date_range.label'))
                    ->schema([
                        DatePicker::make('created_from')
                            ->label(__('filament/resources/point-transaction-resource.table.filters.date_range.from')),
                        DatePicker::make('created_until')
                            ->label(__('filament/resources/point-transaction-resource.table.filters.date_range.until')),
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
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('filament/resources/point-transaction-resource.actions.view'))
                    ->color('info'),
                EditAction::make()
                    ->label(__('filament/resources/point-transaction-resource.actions.edit'))
                    ->color('warning'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
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
            'index' => ListPointTransactions::route('/'),
            'create' => CreatePointTransaction::route('/create'),
            'edit' => EditPointTransaction::route('/{record}/edit'),
        ];
    }
}
