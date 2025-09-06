<?php

namespace App\Filament\Admin\Resources\Domains;

use App\Filament\Admin\Resources\Domains\Pages\CreateDomain;
use App\Filament\Admin\Resources\Domains\Pages\EditDomain;
use App\Filament\Admin\Resources\Domains\Pages\ListDomains;
use App\Filament\Admin\Resources\Domains\Schemas\DomainForm;
use App\Filament\Admin\Resources\Domains\Tables\DomainsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Stancl\Tenancy\Database\Models\Domain as ModelsDomain;

class DomainResource extends Resource
{
    protected static ?string $model = ModelsDomain::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'domain';

    public static function form(Schema $schema): Schema
    {
        return DomainForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DomainsTable::configure($table);
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
            'index' => ListDomains::route('/'),
            'create' => CreateDomain::route('/create'),
            'edit' => EditDomain::route('/{record}/edit'),
        ];
    }
}
