<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages\CreateUser;
use App\Filament\Admin\Resources\UserResource\Pages\EditUser;
use App\Filament\Admin\Resources\UserResource\Pages\ListUsers;
use App\Filament\Admin\Resources\UserResource\RelationManagers\UserAddressesRelationManager;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string|\UnitEnum|null $navigationGroup = 'User Section';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('seller_id')
                    ->relationship('seller', 'name')
                    ->required()->hidden(Filament::getAuthGuard() == 'seller')
                    ->searchable()->preload(),

                TextInput::make('firebase_uid')
                    ->hidden(fn (Page $livewire) => $livewire instanceof CreateRecord)
                    ->maxLength(255),
                TextInput::make('google_id')
                    ->hidden(fn (Page $livewire) => $livewire instanceof CreateRecord)
                    ->maxLength(255),
                TextInput::make('name')
                    ->label(__('filament/resources/user-resource.form.name.label'))
                    ->placeholder(__('filament/resources/user-resource.form.name.placeholder'))
                    ->maxLength(255)->required(),
                TextInput::make('email')
                    ->label(__('filament/resources/user-resource.form.email.label'))
                    ->placeholder(__('filament/resources/user-resource.form.email.placeholder'))
                    ->email()
                    ->maxLength(255)->required(),
                TextInput::make('phone')
                    ->label(__('filament/resources/user-resource.form.phone.label'))
                    ->placeholder(__('filament/resources/user-resource.form.phone.placeholder'))
                    ->maxLength(255)->required(),

                SpatieMediaLibraryFileUpload::make('profile_photo')
                    ->collection('profile_photos')->imageEditor()
                    ->image()->rules('required'),

                DateTimePicker::make('email_verified_at'),

                TextInput::make('points')->default(0)->numeric(),

                TextInput::make('password')
                    ->label(__('filament/resources/user-resource.form.password.label'))
                    ->placeholder(__('filament/resources/user-resource.form.password.placeholder'))
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (Page $livewire) => $livewire instanceof CreateRecord)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('thumb')->collection('profile_photos'),
                TextColumn::make('name')
                    ->label(__('filament/resources/user-resource.table.columns.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament/resources/user-resource.table.columns.email'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('filament/resources/user-resource.table.columns.phone'))
                    ->searchable(),

                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
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
            UserAddressesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
