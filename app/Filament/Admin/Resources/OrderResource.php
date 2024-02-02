<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Admin\Resources\OrderResource\RelationManagers;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Exchange;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()->schema([

                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make()->schema([

                            Forms\Components\Select::make('seller_id')
                                ->searchable()
                                ->required()->relationship('seller','name')->preload(),

                            Forms\Components\Select::make('user_id')
                                ->searchable()
                                ->required()->relationship('user','name')->preload(),

                            Forms\Components\Select::make('payment_id')
                                ->searchable()
                                ->required()->relationship('payment','name')->preload(),

                            Forms\Components\Select::make('user_address_id')
                                ->searchable()
                                ->required()->relationship('address','address')->preload(),

                            Forms\Components\TextInput::make('status')
                                ->required()
                                ->maxLength(255),
                        ])->columns(2),


                        Forms\Components\Repeater::make('products')
                            ->relationship('products')->reorderable()
                            ->schema([

                                Forms\Components\Grid::make()->schema([
                                    Forms\Components\Group::make()->schema([
                                        Forms\Components\TextInput::make('price')
                                            ->numeric()
                                            ->required(fn(Forms\Get $get)=> !$get('has_variant'))
                                            ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native),

                                        Forms\Components\TextInput::make('quantity')
                                            ->required(fn(Forms\Get $get)=> !$get('has_variant'))
                                            ->numeric(),

                                        Forms\Components\TextInput::make('discount')
                                            ->numeric()->prefix('%')->minValue(0)->integer()->maxValue(100)
                                            ->live()
                                            ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                                //this is auto change discount price
                                                if($get('discount') != null && $get('discount') != 0 &&  !blank($get('price'))){

                                                    $discountCalculate =  $get('price') - ($get('discount') / $get('price') * 100);
                                                    $set('discount_price',$discountCalculate);
                                                }
                                            })
                                            ->default(0),

                                        Forms\Components\TextInput::make('discount_price')
                                            ->numeric()->label('Discount Price')
                                            ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native)
                                            ->default(0.0000),
                                    ])->columns(2),

                                    Forms\Components\Group::make()->schema([

                                        Forms\Components\Repeater::make('attributes')
                                            ->reorderableWithButtons()
                                            ->schema([
                                                Forms\Components\Section::make()->schema([

                                                    Forms\Components\Select::make('attribute_id')->live()
                                                        ->required()->searchable()->preload()
                                                        ->afterStateUpdated(fn(Forms\Set $set)=>  $set('attribute_value_id',null))
                                                        ->options(Attribute::pluck('name')),

                                                    Forms\Components\Select::make('attribute_value_id')
                                                        ->hidden(fn(Forms\Get $get)=> blank($get('attribute_id')))
                                                        ->required()->searchable()->preload()
                                                        ->options(fn(Forms\Get $get) => AttributeValue::where('attribute_id',$get('attribute_id'))->pluck('value')),

                                                ])->columns(2),
                                            ]),
                                    ])
                                ])
                            ])


                    ])->columnSpan(2),

                    Forms\Components\Group::make()->schema([

                        Forms\Components\Section::make('Price Infomation')->schema([
                            Forms\Components\TextInput::make('sub_total')
                                ->helperText('product price total price')
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('delivery_fees')
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('total_price')
                                ->helperText('All total price')
                                ->required()
                                ->numeric(),
                        ])->columns(1),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('slip')
                            ->collection('slips')
                            ->imagePreviewHeight('700px')
                            ->image()->imageEditor(),


                    ])->columnSpan(1)

                ])->columns(3)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment.name')
                    ->description(fn(Order $order)=> $order->payment->currency->name)
                    ->sortable(),

                Tables\Columns\TextColumn::make('address.region.mm_name')
                    ->description(fn(Order $order) => $order->address->township->mm_name.' , '.$order->address->address)
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sub_total')
                    ->numeric()
                    ->description(fn(Order $order) => $order->delivery_fees)
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()->weight(FontWeight::Bold)->color('success')
                    ->suffix(fn(Order $order)=> ' '.$order->payment->currency->symbol_native)
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
//                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
//                    Tables\Actions\ForceDeleteBulkAction::make(),
//                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
//                SoftDeletingScope::class,
            ]);
    }
}
