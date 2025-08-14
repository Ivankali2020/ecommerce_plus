<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\SupplierResource\Pages;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    // protected static ?string $navigationGroup = 'Management';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/supplier-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/supplier-resource.navigation.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([

                    Forms\Components\TextInput::make('name')
                        ->label(__('filament/resources/supplier-resource.form.name'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label(__('filament/resources/supplier-resource.form.email'))
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('filament/resources/supplier-resource.form.phone'))
                        ->tel()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('about')
                        ->label(__('filament/resources/supplier-resource.form.about'))
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\Select::make('township_id')
                        ->label(__('filament/resources/supplier-resource.form.township_id'))
                        ->searchable()->preload()
                        ->relationship('township', 'mm_name')
                        ->required(),

                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->label(__('filament/resources/supplier-resource.table.columns.seller.name'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament/resources/supplier-resource.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament/resources/supplier-resource.table.columns.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament/resources/supplier-resource.table.columns.phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('township.mm_name')
                    ->label(__('filament/resources/supplier-resource.table.columns.township.mm_name'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament/resources/supplier-resource.table.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament/resources/supplier-resource.table.columns.updated_at'))
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id', CheckFilamentSellerUser::getSellerId())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
