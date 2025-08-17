<?php

namespace App\Filament\Seller\Resources\Users;

use App\Filament\Seller\Resources\Users\Pages\CreateUser;
use App\Filament\Seller\Resources\Users\Pages\EditUser;
use App\Filament\Seller\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/user-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/user-resource.navigation.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/user-resource.navigation.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Admin\Resources\Users\UserResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\Users\UserResource::table($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
