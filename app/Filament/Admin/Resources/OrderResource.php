<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Admin\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Grid::make()->schema([

                   Forms\Components\Group::make()->schema([
                      Forms\Components\Section::make()->schema([
                          Forms\Components\Select::make('user_id')
                              ->searchable()
                              ->required()->relationship('user','name')->preload(),

                          Forms\Components\Select::make('payment_id')
                              ->searchable()
                              ->required()->relationship('payment','name')->preload(),

                          Forms\Components\Select::make('user_address_id')
                              ->searchable()
                              ->required()->relationship('address','address')->preload(),

                          Forms\Components\TextInput::make('status')
                              ->required()
                              ->maxLength(255),
                          Forms\Components\TextInput::make('sub_total')
                              ->required()
                              ->numeric(),
                          Forms\Components\TextInput::make('delivery_fees')
                              ->required()
                              ->numeric(),
                          Forms\Components\TextInput::make('total_price')
                              ->required()
                              ->numeric(),
                      ])->columns(2)
                   ])->columnSpan(2),

                   Forms\Components\Group::make()->schema([
                       Forms\Components\SpatieMediaLibraryFileUpload::make('slip')
                           ->collection('slips')
                           ->imagePreviewHeight('700px')
                           ->image()->imageEditor(),
                   ])->columnSpan(1)

               ])->columns(3)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_address_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_fees')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
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
//                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
//                    Tables\Actions\ForceDeleteBulkAction::make(),
//                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
//                SoftDeletingScope::class,
            ]);
    }
}
