<?php

namespace App\Filament\Admin\Resources\Exchanges;

use App\Filament\Admin\Resources\Exchanges\Pages\CreateExchange;
use App\Filament\Admin\Resources\Exchanges\Pages\EditExchange;
use App\Filament\Admin\Resources\Exchanges\Pages\ListExchanges;
use App\Models\Currency;
use App\Models\Exchange;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class ExchangeResource extends Resource
{
    protected static ?string $model = Exchange::class;

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Money Section';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/exchange-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/exchange-resource.model.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/exchange-resource.model.label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make()->schema([
                        Select::make('seller_id')
                            ->required()->relationship('seller', 'name')
                            ->label(__('filament/resources/exchange-resource.form.seller_id.label'))
                            ->hidden(Filament::getAuthGuard() == 'seller')
                            ->searchable()->preload(),

                        Select::make('currency_id')
                            ->required()->relationship('currency', 'name')
                            ->live(onBlur: true)
                            ->label(__('filament/resources/exchange-resource.form.currency_id.label'))
                            ->unique(table: 'exchanges', column: 'currency_id', ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule, Get $get) {
                                    return $rule->where('seller_id', $get('seller_id') ?? Filament::auth()->id())
                                        ->where('currency_id', $get('currency_id'));
                                })
                            ->searchable()->preload(),

                        TextInput::make('rate')
                            ->label(__('filament/resources/exchange-resource.form.rate.label'))
                            ->helperText(__('filament/resources/exchange-resource.form.rate.helper'))
                            ->prefix('1 Ks = ')
                            ->suffix(fn (Get $get) => $get('currency_id') != null ? Currency::find($get('currency_id'))->symbol_native : '')
                            ->required()
                            ->numeric(),
                    ])->columns(2),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seller.name')
                    ->label(__('filament/resources/exchange-resource.table.seller_name'))
                    ->hidden(Filament::getAuthGuard() == 'seller')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency.code')
                    ->label(__('filament/resources/exchange-resource.table.currency_code'))
                    ->description(fn (Exchange $exchange) => $exchange->currency->name)
                    ->sortable(),
                TextColumn::make('rate')
                    ->label(__('filament/resources/exchange-resource.table.rate'))
                    ->prefix(fn (Exchange $exchange) => $exchange->currency->symbol_native.' ')
                    ->description(__('filament/resources/exchange-resource.table.rate_description'))
                    ->prefix(fn (Exchange $exchange) => $exchange->currency->symbol_native.' ')
                    ->description('calculate base on 1 USD')
                    ->sortable(),
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
                ViewAction::make()->modalWidth('sm'),
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
            'index' => ListExchanges::route('/'),
            'create' => CreateExchange::route('/create'),
            'edit' => EditExchange::route('/{record}/edit'),
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
