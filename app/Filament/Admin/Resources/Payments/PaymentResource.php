<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaymentResource\Pages\CreatePayment;
use App\Filament\Admin\Resources\PaymentResource\Pages\EditPayment;
use App\Filament\Admin\Resources\PaymentResource\Pages\ListPayments;
use App\Filament\Admin\Resources\PaymentResource\RelationManagers\PaymentAccountsRelationManager;
use App\Models\Currency;
use App\Models\Payment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Money Section';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Grid::make()->schema([
                    Group::make()->schema([
                        Section::make()->schema([
                            Select::make('seller_id')
                                ->hidden(Filament::getAuthGuard() == 'seller')
                                ->label(__('filament/resources/payment-resource.form.seller.label'))
                                ->searchable()->preload()
                                ->required()->relationship('seller', 'name'),

                            Select::make('currency_id')
                                ->label(__('filament/resources/payment-resource.form.currency.label'))
                                ->searchable()->preload()->disabled()
                                ->hidden()
                                ->default(fn () => Currency::where('code', 'MMK')->first()->id)
                                ->helperText(__('filament/resources/payment-resource.form.currency.helper_text'))
                                ->required()->relationship('currency', 'name'),

                            TextInput::make('name')
                                ->label(__('filament/resources/payment-resource.form.name.label'))
                                ->helperText(__('filament/resources/payment-resource.form.name.helper_text'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('note')
                                ->label(__('filament/resources/payment-resource.form.note.label'))
                                ->helperText(__('filament/resources/payment-resource.form.note.helper_text'))
                                ->maxLength(255)
                                ->nullable(),

                            SpatieMediaLibraryFileUpload::make('logo')
                                ->label(__('filament/resources/payment-resource.form.logo.label'))
                                ->collection('logos')->imageEditor()
                                ->image()->rules('required'),

                        ]),
                    ]),

                    Group::make()->schema([
                        Section::make()->schema([
                            Repeater::make('accounts')
                                ->label(__('filament/resources/payment-resource.form.accounts.label'))
                                ->collapsible()->reorderableWithButtons()->reorderable()
                                ->relationship('accounts')
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('filament/resources/payment-resource.form.accounts.name.label'))
                                        ->required()
                                        ->placeholder(__('filament/resources/payment-resource.form.accounts.name.placeholder'))
                                        ->maxLength(255),

                                    TextInput::make('number')
                                        ->label(__('filament/resources/payment-resource.form.accounts.number.label'))
                                        ->required()
                                        ->placeholder(__('filament/resources/payment-resource.form.accounts.number.placeholder'))
                                        ->maxLength(255),
                                ])->columns(2),
                        ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('thumb')
                    ->label(__('filament/resources/payment-resource.table.columns.thumb'))
                    ->collection('logos'),

                TextColumn::make('seller.name')
                    ->label(__('filament/resources/payment-resource.table.columns.seller'))
                    ->hidden(Filament::getAuthGuard() == 'seller')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('currency.name')
                    ->label(__('filament/resources/payment-resource.table.columns.currency'))
                    ->numeric()
                    ->description(fn (Payment $payment) => $payment->currency->symbol)
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('filament/resources/payment-resource.table.columns.name'))
                    ->searchable(),

                TextColumn::make('accounts.name')
                    ->label(__('filament/resources/payment-resource.table.columns.accounts'))
                    ->badge()
                    ->searchable(),

                ToggleColumn::make('is_published')
                    ->label(__('filament/resources/payment-resource.table.columns.is_published'))
                    ->onIcon('heroicon-m-bolt')
                    ->offIcon('heroicon-m-stop')
                    ->onColor('success')
                    ->offColor('danger'),

                TextColumn::make('deleted_at')
                    ->label(__('filament/resources/payment-resource.table.columns.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('filament/resources/payment-resource.table.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament/resources/payment-resource.table.columns.updated_at'))
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
            ->defaultSort('id', 'desc')
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
            PaymentAccountsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'edit' => EditPayment::route('/{record}/edit'),
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
