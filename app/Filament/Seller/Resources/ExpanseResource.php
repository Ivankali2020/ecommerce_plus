<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\ExpanseResource\Pages;
use App\Filament\Seller\Resources\ExpanseResource\RelationManagers;
use App\Models\Expanse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpanseResource extends Resource
{
    protected static ?string $model = Expanse::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

               Forms\Components\Group::make()->schema([
                  Forms\Components\Section::make()->schema([
                      Forms\Components\TextInput::make('name')
                          ->required()
                          ->maxLength(255),
                      Forms\Components\TextInput::make('amount')
                          ->required()
                          ->numeric(),
                      Forms\Components\Textarea::make('description')
                          ->required()
                          ->columnSpanFull(),
                  ])
               ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
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
