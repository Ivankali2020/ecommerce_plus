<?php

namespace App\Filament\Seller\Resources;

use App\Helper\PaymentMethodExpanse;
use App\Filament\Seller\Resources\ExpanseResource\Pages;
use App\Filament\Seller\Resources\ExpanseResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Expanse;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpanseResource extends Resource
{
    
    protected static ?string $model = Expanse::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

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
        return parent::getEloquentQuery()->where('seller_id',CheckFilamentSellerUser::getSellerId());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Forms\Components\Select::make('expanse_category_id')
                    ->required()->searchable()->preload()
                    ->label(__('filament/resources/expanse-resource.form.expanse_category_id.label'))
                    ->relationship('category', 'name', modifyQueryUsing: function (Builder $query) {
                        $query->where('seller_id', CheckFilamentSellerUser::getSellerId());
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        // Forms\Components\TextInput::make('icon')
                        //     ->maxLength(255),
                        Forms\Components\Hidden::make('seller_id')
                            ->required()->default(CheckFilamentSellerUser::getSellerId()),
                    ])->editOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                        ]),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->label(__('filament/resources/expanse-resource.form.amount.label')),

                ToggleButtons::make('payment_method')
                    ->label(__('filament/resources/expanse-resource.form.payment_method.label'))->grouped()
                    ->options(PaymentMethodExpanse::class)
                    ->required(),

                // Forms\Components\Select::make('payment_method')
                //     ->required()->options(PaymentMethodExpanse::class),

                Forms\Components\Hidden::make('seller_id')
                    ->required()->default(CheckFilamentSellerUser::getSellerId()),

                Forms\Components\Textarea::make('description')
                    ->label(__('filament/resources/expanse-resource.form.description.label'))
                    ->columnSpanFull(),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('filament/resources/expanse-resource.table.columns.category_name'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('filament/resources/expanse-resource.table.columns.payment_method'))
                    ->badge()
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('filament/resources/expanse-resource.table.columns.description'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament/resources/expanse-resource.table.columns.amount'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament/resources/expanse-resource.table.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament/resources/expanse-resource.table.columns.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListExpanses::route('/'),
            'create' => Pages\CreateExpanse::route('/create'),
            'edit' => Pages\EditExpanse::route('/{record}/edit'),
        ];
    }
}
