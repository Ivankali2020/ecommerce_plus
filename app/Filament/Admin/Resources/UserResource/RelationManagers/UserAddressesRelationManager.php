<?php

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class UserAddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('region_id')->live()
                    ->required()->searchable()->preload()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('township_id', null))
                    ->relationship('region', 'mm_name'),

                Forms\Components\Select::make('township_id')
                    ->hidden(fn (Forms\Get $get) => blank($get('region_id')))
                    ->required()->searchable()->preload()
                    ->relationship('township', 'mm_name', modifyQueryUsing: fn ($query, Forms\Get $get) => $query->where('region_id', $get('region_id'))),

                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('address')
            ->columns([
                Tables\Columns\TextColumn::make('region.mm_name'),
                Tables\Columns\TextColumn::make('township.mm_name'),
                Tables\Columns\TextColumn::make('address'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
}
