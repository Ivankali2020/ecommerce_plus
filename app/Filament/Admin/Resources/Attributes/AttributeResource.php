<?php

namespace App\Filament\Admin\Resources\Attributes;

use App\Filament\Admin\Resources\Attributes\Pages\CreateAttribute;
use App\Filament\Admin\Resources\Attributes\Pages\EditAttribute;
use App\Filament\Admin\Resources\Attributes\Pages\ListAttributes;
use App\Helper\AttributeType;
use App\Models\Attribute;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Business Section';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/attribute-resource.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/attribute-resource.navigation_label');
    }

    public static function getNavigationGroup(): string
    {
        return __(static::$navigationGroup);
    }

    public static function form(Schema $schema): Schema
    {
        $loading = false;

        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make([
                            Select::make('seller_id')
                                ->hidden(Filament::getAuthGuard() == 'seller')
                                ->searchable()->preload()
                                ->required()->relationship('seller', 'name')
                                ->label(__('filament/resources/attribute-resource.form.seller_id')),

                            TextInput::make('name')
                                ->required()
                                ->helperText('ဉပမာ - Sizes, Colors, Unit, Weight, Type, ...')
                                ->maxLength(255)
                                ->label(__('filament/resources/attribute-resource.form.name')),

                            Select::make('type')
                                ->options(AttributeType::class)
                                ->live(onBlur: true)
                                ->native(false)
                                ->required()
                                ->label(__('filament/resources/attribute-resource.form.type')),

                        ])->columnSpan(['default' => 1, 'lg' => 1]),

                        Section::make([
                            Repeater::make('values')
                                ->relationship('values')->reorderable()
                                ->label(__('filament/resources/attribute-resource.form.values'))
                                ->schema([

                                    ColorPicker::make('color_code')
                                        ->live()->label(__('filament/resources/attribute-resource.form.color_code'))
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            //  $response = Http::get('https://api.color.pizza/v1/?values='.Str::remove('#',$state)); will be update in v2
                                        })
                                        ->visible(function (Get $get) {
                                            // Try multiple approaches to access parent state
                                            $type = $get('../../type') ?? $get('../type') ?? $get('type');

                                            return $type !== AttributeType::Text;
                                        })
                                        ->required(),

                                    // TextInput::make('value')
                                    //     ->live()->label(__('filament/resources/attribute-resource.form.value'))
                                    //     ->placeholder(fn() => $loading ? 'Searching Color Name' : '')
                                    //     ->visible(function (Get $get) {
                                    //         // Try multiple approaches to access parent state
                                    //         $type = $get('../../type') ?? $get('../type') ?? $get('type');

                                    //         return $type !== AttributeType::Color;
                                    //     })
                                    //     ->required(),

                                    TextInput::make('value')
                                        // ->visible(function (Get $get) {
                                        //     // Try multiple approaches to access parent state
                                        //     $type = $get('../../type') ?? $get('../type') ?? $get('type');

                                        //     return $type === AttributeType::Text;
                                        // })
                                        ->helperText('ဉပမာ - Sizes ဆိုရင် (xl,l,md), Colors ဆိုရင် (red,green,...), ...')
                                        ->required()
                                        ->maxLength(255)
                                        ->label(__('filament/resources/attribute-resource.form.value')),

                                ])
                                ->columns(2),
                        ])
                            ->columnSpan(['default' => 1, 'lg' => 1])
                            ->hidden(fn(Get $get) => blank($get('type'))),
                            
                    ])->columnSpanFull()                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seller.name')
                    ->numeric()->hidden(Filament::getAuthGuard() == 'seller')
                    ->sortable()
                    ->label(__('filament/resources/attribute-resource.table.seller_name')),

                TextColumn::make('name')
                    ->searchable()
                    ->label(__('filament/resources/attribute-resource.table.name')),

                TextColumn::make('type')->badge()
                    ->searchable()
                    ->label(__('filament/resources/attribute-resource.table.type')),

                ColorColumn::make('values.color_code')
                    ->label(__('filament/resources/attribute-resource.table.values_color_code'))
                    ->searchable(),

                TextColumn::make('values.value')->badge()->color('gray')
                    ->label(__('filament/resources/attribute-resource.table.values_value')),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/attribute-resource.table.deleted_at')),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/attribute-resource.table.created_at')),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/attribute-resource.table.updated_at')),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
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
            'index' => ListAttributes::route('/'),
            'create' => CreateAttribute::route('/create'),
            'edit' => EditAttribute::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
