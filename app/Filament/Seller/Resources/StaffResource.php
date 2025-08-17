<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Admin\Resources\SellerResource;
use App\Filament\Seller\Resources\StaffResource\Pages\CreateStaff;
use App\Filament\Seller\Resources\StaffResource\Pages\EditStaff;
use App\Filament\Seller\Resources\StaffResource\Pages\ListStaff;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Seller;
use App\Models\Staff;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StaffResource extends Resource
{
    protected static ?string $model = Seller::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    // protected static ?string $modelLabel = 'Staff';

    // protected static ?string $navigationLabel = 'Staff';

    public static function getModelLabel(): string
    {
        return __('filament/resources/staff-resource.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/staff-resource.model_label_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/staff-resource.model_label_plural');
    }

    public static function form(Schema $schema): Schema
    {
        return SellerResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return SellerResource::table($table);
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
            'index' => ListStaff::route('/'),
            'create' => CreateStaff::route('/create'),
            'edit' => EditStaff::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('parent_id', CheckFilamentSellerUser::getSellerId())
            ->whereNot('id', Filament::auth()->id());
    }
}
