<?php

namespace App\Filament\Seller\Resources\Suppliers;

use App\Filament\Seller\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\Seller\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\Seller\Resources\Suppliers\Pages\ListSuppliers;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Supplier;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    // protected static ?string $navigationGroup = 'Management';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/supplier-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/supplier-resource.navigation.label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([

                    TextInput::make('name')
                        ->label(__('filament/resources/supplier-resource.form.name'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label(__('filament/resources/supplier-resource.form.email'))
                        ->email()
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->label(__('filament/resources/supplier-resource.form.phone'))
                        ->tel()
                        ->required()
                        ->maxLength(255),
                    Textarea::make('about')
                        ->label(__('filament/resources/supplier-resource.form.about'))
                        ->required()
                        ->columnSpanFull(),

                    Select::make('region_id')
                        ->label(__('filament/resources/supplier-resource.form.region_id'))
                        ->searchable()->preload()
                        ->relationship('region', 'mm_name')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn($set) => $set('township_id', null)),

                    Select::make('township_id')
                        ->label(__('filament/resources/supplier-resource.form.township_id'))
                        ->searchable()->preload()
                        ->hidden(fn($get) => blank($get('region_id')))
                        ->relationship('township', 'mm_name', modifyQueryUsing: fn($query, $get) => $query->where('region_id', $get('region_id')))
                        ->required(),

                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seller.name')
                    ->label(__('filament/resources/supplier-resource.table.columns.seller'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament/resources/supplier-resource.table.columns.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament/resources/supplier-resource.table.columns.email'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('filament/resources/supplier-resource.table.columns.phone'))
                    ->searchable(),
                TextColumn::make('township.mm_name')
                    ->label(__('filament/resources/supplier-resource.table.columns.township'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('region.mm_name')
                    ->label(__('filament/resources/supplier-resource.table.columns.region'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('filament/resources/supplier-resource.table.columns.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament/resources/supplier-resource.table.columns.updated_at'))
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
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
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
