<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Admin\Resources\OrderResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Helper\OrderStatus;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Variant;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

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

                        Forms\Components\Repeater::make('products')
                            ->relationship('products')->reorderable()
                            ->reorderableWithButtons()
                            ->helperText('* မြန်မာကျပ် နဲ့ တွက်ပြီးသားပါပြသထားပါသည် *')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->compact()->collapsed()->description('Details Information')
                                    ->schema([
                                    Forms\Components\Group::make()->schema([
                                        Forms\Components\TextInput::make('original_buy_price')->label('Buy Price')->readOnly(),
                                        Forms\Components\TextInput::make('original_price')->label('Old Price')->readOnly(),
                                        Forms\Components\TextInput::make('original_discount_price')->label('Old Dis Price')->readOnly(),
                                        Forms\Components\TextInput::make('exchange_rate')->readOnly(),
                                        Forms\Components\Select::make('currency_id')
                                            ->disabled()
                                            ->relationship('currency','name')
                                    ])->columns(5),
                                ]),
                                Forms\Components\Grid::make()->schema([

                                    Forms\Components\Group::make()->schema([

                                       Forms\Components\Group::make()->schema([
                                           Forms\Components\Select::make('product_id')->required()
                                               ->searchable()->preload()
                                               ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                                   self::updateAttributes($get,$set);
                                               })
                                               ->live(true,500)
                                               ->relationship('product','name',modifyQueryUsing: function(Forms\Get $get,$query){
                                                   return $query->when(CheckFilamentSellerUser::getSellerId() != null,function ($q){
                                                      $q->where('seller_id',CheckFilamentSellerUser::getSellerId());
                                                   })->whereNull('deleted_at');
                                               })->columnSpan(2),

                                           Forms\Components\TextInput::make('quantity')
                                               ->required()->default(1)->minValue(1)
                                               ->rules(['required',function (Forms\Get $get){
                                                  return function (string $attribute, $value, \Closure $fail) use ($get){
                                                      if (!blank($get('product_id'))) {
                                                          $product = Product::where('id',$get('product_id'))->first();
                                                          if ($product->has_variant && !blank($get('variant_id'))) {
                                                              $variant = Variant::where('id',$get('variant_id'))->first();
                                                              if ($variant->quantity < $value){
                                                                  $fail($variant->quantity .' သာကျန်ရှိပါတော့သည်');
                                                              }
                                                          }else{
                                                              if ($product->quantity < $value){
                                                                  $fail($product->quantity .' သာကျန်ရှိပါတော့သည်');
                                                              }
                                                          }
                                                          return true;
                                                      }

                                                     return $fail('ထည့်ပေးပါရန်');
                                                  };
                                               }])
                                               ->live(true,500)
                                               ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                                   //this is auto change discount price
                                                   if (!blank($get('quantity'))){
                                                       self::thisIsAutoChangeDiscountPrice($get, $set);
                                                   }
                                               })
                                               ->numeric(),

                                           Forms\Components\Select::make('variant_id')->required()
                                               ->hidden(fn(Forms\Get $get)=> blank($get('product_id')) || !Product::where('id',$get('product_id'))->select('has_variant')->first()->has_variant)
                                               ->searchable()->preload()->live(true,500)
                                               ->afterStateUpdated(self::vartiantSectionCalculate())->columnSpan(2)
                                               ->relationship('variant','sku',modifyQueryUsing: function(Forms\Get $get,$query){
                                                   return $query->where('product_id',$get('product_id'));
                                               }),

                                           Forms\Components\TextInput::make('price')
                                               ->numeric()->live(true,500)
                                               ->afterStateUpdated(function(Forms\Get $get,Forms\Set $set){
                                                   //this is auto change discount price
                                                   self::thisIsAutoChangeDiscountPrice($get, $set);
                                               })
                                               ->required()
                                               ->prefix(fn(Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id',$get('exchange_id'))->with('currency')->first()->currency->symbol_native),


                                           Forms\Components\TextInput::make('discount')
                                               ->numeric()->prefix('%')->minValue(0)->integer()->maxValue(100)
                                               ->live(true,500)->integer()
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
                                               ->required()->suffix('ကျပ်')
                                               ->columnSpan(2)
                                               ->numeric(),

                                       ])->columns(3)

                                    ])->columnSpan(2),

                                    Forms\Components\Group::make()->schema([

                                        Forms\Components\Repeater::make('attributes')
                                            ->collapsible()->reorderable(false)
                                            ->addable(false)
                                            ->schema([

                                                   Forms\Components\Group::make()->schema([
                                                       Forms\Components\Hidden::make('attribute')
                                                           ->required()
                                                           ->label('Attribute'),

//                                                       Forms\Components\TextInput::make('value')
//                                                           ->required()
//                                                           ->label('Value'),

                                                       Forms\Components\Select::make('value')
                                                           ->multiple()
                                                           ->hidden(fn(Forms\Get $get)=> blank($get('attribute')))
                                                           ->required()->searchable()->preload()->label('Value')->placeholder('Select')
//                                                           ->default(fn(Forms\Get $get) => AttributeValue::pluck('value','id'))
                                                           ->options(fn(Forms\Get $get) => AttributeValue::pluck('value','id')),

                                                   ])->columns(1)

                                            ]),
                                    ])
                                ])->columns(3)
                            ])

                    ])->columnSpan(2),

                    Forms\Components\Group::make()->schema([

                        Forms\Components\Section::make()->schema([

                            Forms\Components\TextInput::make('invoice_number')->default(Str::upper('INV-'.uniqid()))->columnSpanFull(),

                            Forms\Components\Select::make('seller_id')
                                ->searchable()->hidden(fn()=>Filament::getAuthGuard() == 'seller')
                                ->required()->relationship('seller','name')->preload(),

                            Forms\Components\Toggle::make('walk-in-customer')
                                ->live(onBlur: true)
                                ->label('Walk In')->helperText('customer walk in type'),

                           Forms\Components\Group::make()->schema([
                               Forms\Components\Select::make('user_id')
                                   ->searchable()->createOptionForm(self::customerFormFields())->label('Customer Name')
                                   ->required()->relationship('user','name')->preload()->columnSpanFull(),

                               Forms\Components\Select::make('user_address_id')
                                   ->searchable()->columnSpanFull()
                                   ->required()->relationship('address','address')->preload(),
                           ])->columnSpanFull()->hidden(fn(Forms\Get $get) => $get('walk-in-customer')),

                            Forms\Components\Select::make('payment_id')
                                ->label('Payment')
                                ->searchable()->disabled(fn(Page $livewire) => $livewire instanceof EditRecord)
                                ->required()->options(Payment::where('seller_id',CheckFilamentSellerUser::getSellerId())->pluck('name','id'))->preload(),

                            Forms\Components\Select::make('status')
                                ->default(OrderStatus::New)
                                ->native(false)
                                ->options(OrderStatus::class)->required(),

                            Forms\Components\DateTimePicker::make('created_at')
                                ->native(false)->label('Purchase Date')
                                ->maxDate(now())->default(now())
                                ->minDate(now()->subMonth())->columnSpanFull()

                        ])->columns(2),

                        Forms\Components\Section::make('Price Information')->schema([

                            Forms\Components\Group::make()->schema([
                                Forms\Components\TextInput::make('sub_total')
                                    ->helperText('product price total price')
                                    ->required()->default(0)->readOnly()
                                    ->placeholder(function (Forms\Get $get,Forms\Set $set){
                                        self::getTotalPrice($get,$set);
                                    })
                                    ->numeric(),

                                Forms\Components\TextInput::make('delivery_fees')->suffix('ကျပ်')
                                    ->required()->default(0)->live(true,500)
                                    ->numeric(),
                                ])->columns(2),


                           Forms\Components\Group::make()->schema([

                               Forms\Components\TextInput::make('tax')
                                   ->nullable()->default(0)->live(true,500)->suffix('%')
                                   ->helperText('base on sub total')
                                   ->numeric(),

                               Forms\Components\TextInput::make('tax_price')
                                   ->nullable()->default(0)->suffix('ကျပ်')
                                   ->helperText('amount of tax ')->readOnly()
                                   ->numeric(),
                           ])->columns(2),

                            Forms\Components\TextInput::make('total_price')
                                ->helperText('All total price')->suffix('ကျပ်')
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
            ->groupsOnly()
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
                    ->badge()->description(fn(Order $order) => 'Deli ' . $order->delivery_fees .' ks')
                    ->searchable()
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('tax_price')->alignEnd()
                    ->description(fn(Order $order)=> 'tax : '. $order->tax.' %' ?? ''),

                Tables\Columns\TextColumn::make('sub_total')
                    ->numeric()->alignEnd()
                    ->description(fn(Order $order) => $order->delivery_fees)
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_price')
                    ->alignEnd()
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sub_total')->alignEnd()
                    ->summarize(Tables\Columns\Summarizers\Sum::make('sub_total')->money('mmk')->label('Total Amount')),

                Tables\Columns\TextColumn::make('total_price')->alignEnd()
                    ->summarize(Tables\Columns\Summarizers\Sum::make('total_price')->money('mmk')->label('Total Amount')),

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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Invoice')
                        ->modalContent(fn (Order $record): View => view(
                            'livewire.invoice',['record' => $record]
                        ))
                        ->modalWidth('2xl')
                        ->color('success')
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)

                        ->icon('heroicon-o-viewfinder-circle'),

                    Tables\Actions\ViewAction::make()->slideOver(),
                    Tables\Actions\EditAction::make(),
                ])
            ])
            ->defaultSort('id','desc')
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
                $set('discount_price', 0);
                $set('sub_price', ($get('price') * $get('quantity')));
                self::getTotalPrice($get,$set);
            }else{
                $discountCalculate = $get('price') - ($get('discount') / 100 * $get('price'));
                $set('discount_price', $discountCalculate);
                $set('sub_price', $discountCalculate * $get('quantity'));
                self::getTotalPrice($get,$set);
            }
        }

    }

    /**
     * @param Forms\Get $get
     * @param Forms\Set $set
     * @return void
     */
    public  static  function getTotalPrice(Forms\Get $get, Forms\Set $set): void
    {
        $total = 0;
        if (!blank($get('products'))){
            foreach ($get('products') as $product) {
                if (!blank($product['quantity']) && !blank($product['discount_price']) && $product['quantity'] != 0 && $product['discount_price'] != 0) {
                    $total += $product['discount_price'] * $product['quantity'];
                } else if (!blank($product['quantity']) && !blank($product['price']) && $product['quantity'] != 0 && $product['price'] != 0) {
                    $total += $product['price'] * $product['quantity'];
                }
            }
        }


        $set('sub_total', $total);
        if (blank($get('tax')) || $get('tax') == 0){
            $superTotal = $total + ($get('delivery_fees') ?? 0);
            $set('total_price', (float)$superTotal );
        }else{
            //calcuate for tax price and total
            $grandTotalByTax = $get('tax') / 100 * $total;
            $superTotal = ($total + $grandTotalByTax) + ($get('delivery_fees') ?? 0);
            $set('total_price', (float)$superTotal);
            $set('tax_price', (float)$grandTotalByTax);
        }
    }

    /**
     * @param Forms\Get $get
     * @param Forms\Set $set
     * @return void
     */
    public static function updateAttributes(Forms\Get $get, Forms\Set $set): void
    {

        if (!blank($get('product_id'))) {
            $product = Product::where('id', $get('product_id'))->with('exchange')->first();

            if (!$product->has_variant) {

                if ($product->exchange->currency->code == 'MMK'){
                    $set('price', $product->price);
                    $set('discount', $product->discount);
                    $set('discount_price', $product->discount_price);


                    $set('original_buy_price', $product->buy_price);
                    $set('original_price', $product->price);
                    $set('original_discount_price', $product->discount_price);

                }else{
                    $set('price', $product->mmk_price);
                    $set('discount', $product->discount);
                    $set('discount_price', $product->mmk_discount_price);

                    $set('original_buy_price', $product->mmk_buy_price);
                    $set('original_price', $product->mmk_price);
                    $set('original_discount_price', $product->mmk_discount_price);
                }

                $set('exchange_rate',$product->exchange->rate);
                $set('currency_id',$product->exchange->currency_id);

                $data = [];
                foreach ($product->attributes as $att) {

                    $data[] = [
                        'attribute' => Attribute::where('id', $att['attribute_id'])->pluck('name') ?? '',
//                        'value' => implode(',', AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray()) ??  '',
                        'value' => AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray() ??  '',
                    ];

                }
                $set('attributes', $data);
              if ($get('discount') > 0){
                  //has discount
                  $set('sub_price',$get('discount_price') * $get('quantity'));
              }else{
                  $set('sub_price',$get('price') * $get('quantity'));
              }
            }else{
                $set('variant_id', null);
            }

        }
    }

    /**
     * @return array
     */
    protected static function customerFormFields(): array
    {
        return [
            Forms\Components\Grid::make()->schema([

                Forms\Components\Group::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->live()
                        ->maxLength(255)->required(),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255)->nullable(),
                    Forms\Components\TextInput::make('phone')
                        ->maxLength(255)->required(),

                    Forms\Components\Hidden::make('seller_id')
                        ->default(CheckFilamentSellerUser::getSellerId()),

                    Forms\Components\Hidden::make('password')
                        ->default(fn(Forms\Get $get) => Hash::make($get('name'))),
                ]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Repeater::make('addresses')
                        ->relationship('addresses')
                        ->schema([
                            Forms\Components\Select::make('region_id')->live()
                                ->required()->searchable()->preload()
                                ->afterStateUpdated(fn(Forms\Set $set)=>  $set('township_id',null))
                                ->relationship('region','mm_name'),

                            Forms\Components\Select::make('township_id')
                                ->hidden(fn(Forms\Get $get)=> blank($get('region_id')))
                                ->required()->searchable()->preload()
                                ->relationship('township','mm_name',modifyQueryUsing: fn($query,Forms\Get $get)=> $query->where('region_id',$get('region_id'))),

                            Forms\Components\TextInput::make('address')
                                ->required(),
                        ])
                ])


            ])
        ];
    }

    /**
     * @return \Closure
     */
    protected static function vartiantSectionCalculate(): \Closure
    {
        return function (Forms\Get $get, Forms\Set $set) {
            if (!blank($get('variant_id'))) { // check if variant id is have

                $variant = Variant::where('id', $get('variant_id'))->with('product:id,exchange_id')->first();

                if ($variant->product->exchange->currency->code == 'MMK') {
                    $set('price', $variant->price);
                    $set('discount', $variant->discount);
                    $set('discount_price', $variant->discount_price);

                    $set('original_buy_price', $variant->buy_price);
                    $set('original_price', $variant->price);
                    $set('original_discount_price', $variant->discount_price);
                } else {
                    $set('price', $variant->mmk_price);
                    $set('discount', $variant->discount);
                    $set('discount_price', $variant->mmk_discount_price);

                    $set('original_buy_price', $variant->mmk_buy_price);
                    $set('original_price', $variant->mmk_price);
                    $set('original_discount_price', $variant->mmk_discount_price);
                }

                $set('exchange_rate',$variant->product->exchange->rate);
                $set('currency_id',$variant->product->exchange->currency_id);

                $data = [];
                foreach ($variant->attributes as $key => $att) {

                    $data[] = [
                        'attribute' => Attribute::where('id', $att['attribute_id'])->first()->name ?? '',
                        'value' => AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray() ??  '',
//                        'value' => implode(',', AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray()) ?? '',
                    ];

                }
                self::thisIsAutoChangeDiscountPrice($get, $set);

                $set('attributes', $data);
            }
        };
    }

}
