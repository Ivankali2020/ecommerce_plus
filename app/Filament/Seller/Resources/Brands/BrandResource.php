<?php

namespace App\Filament\Seller\Resources\Brands;

use App\Filament\Seller\Resources\Brands\Pages\CreateBrand;
use App\Filament\Seller\Resources\Brands\Pages\EditBrand;
use App\Filament\Seller\Resources\Brands\Pages\ListBrands;
use App\Models\Brand;
use Filament\Facades\Filament;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Business Section';

    public static function getModelLabel(): string
    {
        return __('filament/resources/brand-resource.navigation.label');
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return __("filament/resources/brand-resource.navigation.group");
    // }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
               
                    Section::make()->schema([

                        TextInput::make('name')
                            ->helperText(__('filament/resources/brand-resource.form.name_helper'))
                            ->required()
                            ->maxLength(255)
                            ->label(__('filament/resources/brand-resource.form.name')),

                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logos')->imageEditor()
                            ->image()->rules('required')
                            ->label(__('filament/resources/brand-resource.form.logo')),

                        Textarea::make('description')
                            ->maxLength(16777215)
                            ->columnSpanFull()
                            ->label(__('filament/resources/brand-resource.form.description')),
                    ]),
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\Brands\BrandResource::table($table);
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
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id', Filament::auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
