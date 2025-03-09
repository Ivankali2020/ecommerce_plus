<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttributeResource\Pages;
use App\Filament\Admin\Resources\AttributeResource\RelationManagers;
use App\Helper\AttributeType;
use App\Models\Attribute;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $navigationGroup = 'Business Section';

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

    public static function form(Form $form): Form
    {
        $loading = false;

        return $form
            ->schema([
                Forms\Components\Grid::make()->schema([
                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\Select::make('seller_id')
                                ->hidden(Filament::getAuthGuard() == 'seller')
                                ->searchable()->preload()
                                ->required()->relationship('seller','name')
                                ->label(__('filament/resources/attribute-resource.form.seller_id')),

                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->helperText('ဉပမာ - Sizes, Colors, Unit, Weight, Type, ...')
                                ->maxLength(255)
                                ->label(__('filament/resources/attribute-resource.form.name')),

                            Forms\Components\Select::make('type')
                                ->options(AttributeType::class)
                                ->live(onBlur: true)
                                ->native(false)
                                ->required()
                                ->label(__('filament/resources/attribute-resource.form.type')),

                        ])
                    ])->columnSpan(1),
                    Forms\Components\Group::make()->schema([
                        Forms\Components\Repeater::make('values')
                            ->relationship('values')->reorderable()
                            ->label(__('filament/resources/attribute-resource.form.values'))
                            ->schema([

                                Forms\Components\ColorPicker::make('color_code')
                                    ->live()->label(__('filament/resources/attribute-resource.form.color_code'))
                                    ->afterStateUpdated(function ($state,Forms\Set $set){
                                        //  $response = Http::get('https://api.color.pizza/v1/?values='.Str::remove('#',$state)); will be update in v2
                                    })
                                    ->visible(fn(Forms\Get $get) => $get('../../type') == 'color')
                                    ->required(),

                                Forms\Components\TextInput::make('value')
                                    ->live()->label(__('filament/resources/attribute-resource.form.value'))
                                    ->placeholder(fn()=> $loading ? 'Searching Color Name' : '')
                                    ->visible(fn(Forms\Get $get) => $get('../../type') == 'color')
                                    ->required(),

                                Forms\Components\TextInput::make('value')
                                    ->visible(fn(Forms\Get $get) => $get('../../type') == 'text')
                                    ->helperText('ဉပမာ - Sizes ဆိုရင် (xl,l,md), Colors ဆိုရင် (red,green,...), ...')
                                    ->required()
                                    ->maxLength(255)
                                    ->label(__('filament/resources/attribute-resource.form.value')),

                            ])->columns(2)->hidden(fn(Forms\Get $get) => blank($get('type')))
                    ])
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->numeric()->hidden(Filament::getAuthGuard() == 'seller')
                    ->sortable()
                    ->label(__('filament/resources/attribute-resource.table.seller_name')),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('filament/resources/attribute-resource.table.name')),

                Tables\Columns\TextColumn::make('type')->badge()
                    ->searchable()
                    ->label(__('filament/resources/attribute-resource.table.type')),

                Tables\Columns\ColorColumn::make('values.color_code')
                    ->label(__('filament/resources/attribute-resource.table.values_color_code'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('values.value')->badge()->color('gray')
                    ->label(__('filament/resources/attribute-resource.table.values_value')),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/attribute-resource.table.deleted_at')),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/attribute-resource.table.created_at')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('filament/resources/attribute-resource.table.updated_at')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
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
