<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\UserResource\Pages;
use App\Filament\Seller\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

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

    public static function form(Form $form): Form
    {
        return \App\Filament\Admin\Resources\UserResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\UserResource::table($table);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
