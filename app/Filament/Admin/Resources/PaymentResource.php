<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Filament\Admin\Resources\PaymentResource\RelationManagers;
use App\Models\Currency;
use App\Models\Payment;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Money Section';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Grid::make()->schema([
                   Forms\Components\Group::make()->schema([
                       Forms\Components\Section::make()->schema([
                           Forms\Components\Select::make('seller_id')
                               ->hidden(Filament::getAuthGuard() == 'seller')
                               ->label(__('filament/resources/payment-resource.form.seller.label'))
                               ->searchable()->preload()
                               ->required()->relationship('seller','name'),

                           Forms\Components\Select::make('currency_id')
                               ->label(__('filament/resources/payment-resource.form.currency.label'))
                               ->searchable()->preload()->disabled()
                               ->default(fn()=> Currency::where('code','MMK')->first()->id)
                               ->helperText(__('filament/resources/payment-resource.form.currency.helper_text'))
                               ->required()->relationship('currency','name'),

                           Forms\Components\TextInput::make('name')
                               ->label(__('filament/resources/payment-resource.form.name.label'))
                               ->helperText(__('filament/resources/payment-resource.form.name.helper_text'))
                               ->required()
                               ->maxLength(255),

                           Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                               ->label(__('filament/resources/payment-resource.form.logo.label'))
                               ->collection('logos')->imageEditor()
                               ->image()->rules('required'),


                       ])
                   ]),


                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\Repeater::make('accounts')
                                ->label(__('filament/resources/payment-resource.form.accounts.label'))
                                ->collapsible()->reorderableWithButtons()->reorderable()
                                ->relationship('accounts')
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label(__('filament/resources/payment-resource.form.accounts.name.label'))
                                        ->required()
                                        ->placeholder(__('filament/resources/payment-resource.form.accounts.name.placeholder'))
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('number')
                                        ->label(__('filament/resources/payment-resource.form.accounts.number.label'))
                                        ->required()
                                        ->placeholder(__('filament/resources/payment-resource.form.accounts.number.placeholder'))
                                        ->maxLength(255),
                                ])->columns(2)
                        ])
                    ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('thumb')
                    ->label(__('filament/resources/payment-resource.table.columns.thumb'))
                    ->collection('logos'),

                Tables\Columns\TextColumn::make('seller.name')
                    ->label(__('filament/resources/payment-resource.table.columns.seller'))
                    ->hidden(Filament::getAuthGuard() == 'seller')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency.name')
                    ->label(__('filament/resources/payment-resource.table.columns.currency'))
                    ->numeric()
                    ->description(fn(Payment $payment) => $payment->currency->symbol)
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament/resources/payment-resource.table.columns.name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('accounts.name')
                    ->label(__('filament/resources/payment-resource.table.columns.accounts'))
                    ->badge()
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('is_published')
                    ->label(__('filament/resources/payment-resource.table.columns.is_published'))
                    ->onIcon('heroicon-m-bolt')
                    ->offIcon('heroicon-m-stop')
                    ->onColor('success')
                    ->offColor('danger'),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('filament/resources/payment-resource.table.columns.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament/resources/payment-resource.table.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament/resources/payment-resource.table.columns.updated_at'))
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
            ->defaultSort('id','desc')
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
            RelationManagers\PaymentAccountsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
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
