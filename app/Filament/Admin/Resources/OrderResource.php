<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Admin\Resources\OrderResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Helper\OrderStatus;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Exchange;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Variant;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
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
                                ->searchable()->hidden(fn()=>Filament::getAuthGuard() == 'seller')
                                ->required()->relationship('seller','name')->preload(),

                            Forms\Components\Select::make('user_id')
                                ->searchable()
                                ->required()->relationship('user','name')->preload(),

                            Forms\Components\Select::make('payment_id')
                                ->label('Payment')
                                ->searchable()->disabled(fn(Page $livewire) => $livewire instanceof EditRecord)
                                ->required()->options(Payment::where('seller_id',CheckFilamentSellerUser::getSellerId())->pluck('name','id'))->preload(),

                            Forms\Components\Select::make('user_address_id')
                                ->searchable()
                                ->required()->relationship('address','address')->preload(),

                            Forms\Components\Select::make('status')
                                ->native(false)
                                ->options(OrderStatus::class)->required(),

                            Forms\Components\DateTimePicker::make('created_at')
                                ->native(false)->label('Purchase Date')
                                ->maxDate(now())->default(now())
                                ->minDate(now()->subMonth())

                        ])->columns(2),


                        Forms\Components\Repeater::make('products')
                            ->relationship('products')->reorderable()
                            ->schema([

                                Forms\Components\Grid::make()->schema([
                                    Forms\Components\Group::make()->schema([

                                        Forms\Components\Select::make('product_id')->required()
                                            ->searchable()->preload()
                                            ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                                $product = Product::where('id',$get('product_id'))->first();
                                                if ($product){
                                                    if (!$product->has_variant){
                                                        $set('price',$product->price);
                                                        $set('discount',$product->discount);
                                                        $set('discount_price',$product->discount_price);
                                                        $data = [];
                                                        foreach ($product->attributes as $att){

                                                            $data[] = [
                                                                'attribute' => Attribute::where('id', $att['attribute_id'])->first()->name ?? '',
                                                                'value' => AttributeValue::where('id', $att['attribute_value_id'])->first()->value ?? '',
                                                            ];

                                                        }
                                                        $set('attributes',$data);

                                                    }

                                                    self::thisIsAutoChangeDiscountPrice($get, $set);

                                                }
                                                $set('variant_id',null);
                                             })
                                            ->live()
                                            ->relationship('product','name',modifyQueryUsing: function(Forms\Get $get,$query){
                                                return $query->where('seller_id',CheckFilamentSellerUser::getSellerId());
                                            }),

                                        Forms\Components\Select::make('variant_id')->required()
                                            ->hidden(fn(Forms\Get $get)=> blank($get('product_id')) || !Product::where('id',$get('product_id'))->select('has_variant')->first()->has_variant)
                                            ->searchable()->preload()->live()
                                            ->afterStateUpdated(function (Forms\Get $get,Forms\Set $set){
                                                if (!blank($get('variant_id'))){ // check if variant id is have

                                                    $variant = Variant::where('id',$get('variant_id'))->first();
                                                    $set('price',$variant->price);
                                                    $set('discount',$variant->discount);
                                                    $set('discount_price',$variant->discount_price);
                                                    $data = [];
                                                    foreach ($variant->attributes as $key=>$att){

                                                        $data[] = [
                                                            'attribute' => Attribute::where('id', $att['attribute_id'])->first()->name ?? '',
                                                            'value' => AttributeValue::where('id', $att['attribute_value_id'])->first()->value ?? '',
                                                        ];

                                                    }
                                                    self::thisIsAutoChangeDiscountPrice($get, $set);

                                                    $set('attributes',$data);
                                                }
                                            })
                                            ->relationship('variant','sku',modifyQueryUsing: function(Forms\Get $get,$query){
                                                return $query->where('product_id',$get('product_id'));
                                            }),

                                        Forms\Components\TextInput::make('price')
                                            ->numeric()->live()
                                            ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                                //this is auto change discount price
                                                self::thisIsAutoChangeDiscountPrice($get, $set);
                                            })
                                            ->required()
                                            ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native),

                                        Forms\Components\TextInput::make('quantity')
                                            ->required()->default(1)
                                            ->live()
                                            ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                                //this is auto change discount price
                                               if (!blank($get('quantity'))){
                                                   self::thisIsAutoChangeDiscountPrice($get, $set);
                                               }
                                            })
                                            ->numeric(),

                                        Forms\Components\TextInput::make('discount')
                                            ->numeric()->prefix('%')->minValue(0)->integer()->maxValue(100)
                                            ->live()->integer()
                                            ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                                //this is auto change discount price
                                                self::thisIsAutoChangeDiscountPrice($get, $set);
                                            })
                                            ->default(0),

                                        Forms\Components\TextInput::make('discount_price')
                                            ->numeric()->label('Discount Price')->readOnly()
                                            ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native)
                                            ->default(0.0000),

                                        Forms\Components\TextInput::make('sub_price')
                                            ->required()
                                            ->numeric(),


                                    ])->columns(2),

                                    Forms\Components\Group::make()->schema([

                                        Forms\Components\Repeater::make('attributes')
                                            ->reorderableWithButtons()
                                            ->schema([
                                                Forms\Components\Section::make()->schema([

                                                    Forms\Components\TextInput::make('attribute')->live()
                                                        ->readOnly()
                                                        ->required()
                                                        ->label('Attribute'),

                                                    Forms\Components\TextInput::make('value')->readOnly()
                                                        ->required()
                                                        ->label('Value'),

                                                ])->columns(2),
                                            ])->addable(false),
                                    ])
                                ])
                            ])

                    ])->columnSpan(2),

                    Forms\Components\Group::make()->schema([

                        Forms\Components\Section::make('Price Information')->schema([
                            Forms\Components\TextInput::make('sub_total')
                                ->helperText('product price total price')
                                ->required()->default(0)->readOnly()
                                ->placeholder(function (Forms\Get $get,Forms\Set $set){
                                    $total = 0;
                                   foreach ($get('products') as $product){
                                       if (!blank($product['quantity']) && !blank($product['discount_price']) && $product['quantity'] != 0 && $product['discount_price'] != 0){
                                           $total += $product['discount_price']  * $product['quantity'] ;
                                       }
                                       else if (!blank($product['quantity']) && !blank($product['price']) && $product['quantity'] != 0 && $product['price'] != 0){
                                           $total += $product['price'] * $product['quantity'];
                                       }
                                   }
                                   $set('sub_total',$total);
                                   $set('total_price',$total + ($get('delivery_fees') ?? 0));
                                })
                                ->numeric(),

                            Forms\Components\TextInput::make('delivery_fees')
                                ->required()->default(0)->live()
                                ->numeric(),

                            Forms\Components\TextInput::make('total_price')
                                ->helperText('All total price')
                                ->required()->default(0)->readOnly()
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
                    ->description(fn(Order $order)=> $order->user->phone)
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('payment.name')
                    ->description(fn(Order $order)=> $order->payment->currency->name ?? 'Other Person Just For Testing')
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('address.region.mm_name')
                    ->description(fn(Order $order) => $order->address->township->mm_name.' , '.$order->address->address)
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('sub_total')
                    ->numeric()
                    ->description(fn(Order $order) => $order->delivery_fees)
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()->weight(FontWeight::Bold)->color('success')
                    ->suffix(fn(Order $order)=> ' '.$order->payment?->currency?->symbol_native ?? 'Other Person Just For Testing')
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

    /**
     * @param Forms\Get $get
     * @param Forms\Set $set
     * @return void
     */
    public static function thisIsAutoChangeDiscountPrice(Forms\Get $get, Forms\Set $set): void
    {
        if (!blank($get('discount') ) && !blank($get('price'))) {
            if ($get('discount') == 0){
                $set('sub_price', ($get('price') * $get('quantity')));
                $set('discount_price', 0);
            }else{
                $discountCalculate = $get('price') - ($get('discount') / 100 * $get('price'));
                $set('discount_price', $discountCalculate);
                $set('sub_price', $discountCalculate * $get('quantity'));
            }
        }

    }
}
