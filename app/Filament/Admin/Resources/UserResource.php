<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'User Section';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('seller_id')
                    ->relationship('seller','name')
                    ->required()->hidden(Filament::getAuthGuard() == 'seller')
                    ->searchable()->preload(),

                Forms\Components\TextInput::make('firebase_uid')
                    ->maxLength(255),
                Forms\Components\TextInput::make('device_uid')
                    ->maxLength(255),
                Forms\Components\TextInput::make('device_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)->required(),
                Forms\Components\TextInput::make('phone')
                    ->maxLength(255)->required(),

                Forms\Components\SpatieMediaLibraryFileUpload::make('profile_photo')
                    ->collection('profile_photos')->imageEditor()
                    ->image()->rules('required'),

                Forms\Components\DateTimePicker::make('email_verified_at'),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state)=>filled($state))
                    ->required(fn(Page $livewire) => $livewire instanceof CreateRecord)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\SpatieMediaLibraryImageColumn::make('thumb')->collection('profile_photos'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
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
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\UserAddressesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
