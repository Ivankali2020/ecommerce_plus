<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExchangeResource\Pages;
use App\Filament\Admin\Resources\ExchangeResource\RelationManagers;
use App\Models\Exchange;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class ExchangeResource extends Resource
{
    protected static ?string $model = Exchange::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Money Section';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\Select::make('seller_id')
                            ->required()->relationship('seller','name')
                            ->hidden(Filament::getAuthGuard() == 'seller')
                            ->searchable()->preload(),

                        Forms\Components\Select::make('currency_id')
                            ->required()->relationship('currency','code')
                            ->unique(table: 'exchanges',column: 'currency_id',ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule,Forms\Get $get){
                                    return $rule->where('seller_id',$get('seller_id')??Filament::auth()->id())
                                        ->where('currency_id',$get('currency_id'));
                                })
                            ->searchable()->preload(),

                        Forms\Components\TextInput::make('rate')
                            ->required()
                            ->numeric(),
                    ])->columns(2)
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->hidden(Filament::getAuthGuard() == 'seller')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.code')
                    ->description(fn(Exchange $exchange)=>$exchange->currency->name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate')
                    ->prefix(fn(Exchange $exchange)=> $exchange->currency->symbol_native ." ")
                    ->description('calculate base on 1 USD')
                    ->sortable(),
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
                Tables\Actions\ViewAction::make()->modalWidth('sm'),
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
            'index' => Pages\ListExchanges::route('/'),
            'create' => Pages\CreateExchange::route('/create'),
            'edit' => Pages\EditExchange::route('/{record}/edit'),
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
