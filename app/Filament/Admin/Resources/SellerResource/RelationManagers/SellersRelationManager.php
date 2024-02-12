<?php

namespace App\Filament\Admin\Resources\SellerResource\RelationManagers;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class SellersRelationManager extends RelationManager
{
    protected static string $relationship = 'staffs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([


                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state)=>filled($state))
                    ->maxLength(255),

                Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                    ->label('avatar')
                    ->collection('logos')->imageEditor()
                    ->image()->rules('required'),

                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name',modifyQueryUsing: fn($query) => $query->where('guard_name','seller'))
                    ->multiple()
                    ->preload()
                    ->searchable()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')->collection('logos'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->badge(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

            ])
            ->filters([
                //
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
                ]),
            ]);
    }
}
