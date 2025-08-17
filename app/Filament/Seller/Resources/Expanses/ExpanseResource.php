<?php

namespace App\Filament\Seller\Resources\Expanses;

use App\Filament\Seller\Resources\Expanses\Pages\CreateExpanse;
use App\Filament\Seller\Resources\Expanses\Pages\EditExpanse;
use App\Filament\Seller\Resources\Expanses\Pages\ListExpanses;
use App\Helper\CheckFilamentSellerUser;
use App\Helper\PaymentMethodExpanse;
use App\Models\Expanse;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpanseResource extends Resource
{
    protected static ?string $model = Expanse::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    // protected static ?string $navigationGroup = 'Finance';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/expanse-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/expanse-resource.navigation.label');
    }

    public static function getNavigationGroup(): string
    {
        return __('filament/resources/payment-resource.navigation.group');
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return __("filament/resources/expanse-resource.navigation.group");
    // }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('seller_id', CheckFilamentSellerUser::getSellerId());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Select::make('expanse_category_id')
                        ->required()->searchable()->preload()
                        ->label(__('filament/resources/expanse-resource.form.expanse_category_id.label'))
                        ->relationship('category', 'name', modifyQueryUsing: function (Builder $query) {
                            $query->where('seller_id', CheckFilamentSellerUser::getSellerId());
                        })
                        ->createOptionForm([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            // Forms\Components\TextInput::make('icon')
                            //     ->maxLength(255),
                            Hidden::make('seller_id')
                                ->required()->default(CheckFilamentSellerUser::getSellerId()),
                        ])->editOptionForm([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                        ]),
                    TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->label(__('filament/resources/expanse-resource.form.amount.label')),

                    ToggleButtons::make('payment_method')
                        ->label(__('filament/resources/expanse-resource.form.payment_method.label'))->grouped()
                        ->options(PaymentMethodExpanse::class)
                        ->required(),

                    // Forms\Components\Select::make('payment_method')
                    //     ->required()->options(PaymentMethodExpanse::class),

                    Hidden::make('seller_id')
                        ->required()->default(CheckFilamentSellerUser::getSellerId()),

                    Textarea::make('description')
                        ->label(__('filament/resources/expanse-resource.form.description.label'))
                        ->columnSpanFull(),

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label(__('filament/resources/expanse-resource.table.columns.category_name'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label(__('filament/resources/expanse-resource.table.columns.payment_method'))
                    ->badge()
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('filament/resources/expanse-resource.table.columns.description'))
                    ->searchable(),

                TextColumn::make('amount')
                    ->label(__('filament/resources/expanse-resource.table.columns.amount'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('filament/resources/expanse-resource.table.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament/resources/expanse-resource.table.columns.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListExpanses::route('/'),
            'create' => CreateExpanse::route('/create'),
            'edit' => EditExpanse::route('/{record}/edit'),
        ];
    }
}
