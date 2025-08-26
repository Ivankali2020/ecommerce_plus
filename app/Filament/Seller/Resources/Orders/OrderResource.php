<?php

namespace App\Filament\Seller\Resources\Orders;

use App\Filament\Seller\Resources\Orders\Pages\CreateOrder;
use App\Filament\Seller\Resources\Orders\Pages\EditOrder;
use App\Filament\Seller\Resources\Orders\Pages\ListOrders;
use App\Filament\Seller\Resources\Orders\Widgets\OrderChart;
use App\Helper\CheckFilamentSellerUser;
use App\Helper\OrderStatus;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Exchange;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Variant;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    //  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Order Section';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/order-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/order-resource.navigation.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/order-resource.navigation.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', OrderStatus::New)->count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([

                    Repeater::make('products')
                        ->label(__('filament/resources/order-resource.form.products.label'))
                        ->relationship('products')->reorderable()
                        ->reorderableWithButtons()
                        ->helperText(__('filament/resources/order-resource.form.products.helper_text'))
                        ->collapsible()->collapsed(fn (Page $livewire) => $livewire instanceof EditRecord)
                        ->itemLabel(function (array $state): ?string {
                            $product = Product::where('id', $state['product_id'])->with('brand:name,id')->first();
                            if ($product) {
                                return $product->name.' - '.$state['quantity'].' qty - '.$state['sub_price'];
                            }

                            return '';
                        })
                        ->schema([
                            Section::make()
                                ->compact()->collapsed()->description(__('filament/resources/order-resource.form.products.details.label'))
                                ->schema([
                                    Group::make()->schema([
                                        TextInput::make('original_buy_price')->label(__('filament/resources/order-resource.form.products.details.buy_price'))->readOnly(),
                                        TextInput::make('original_price')->label(__('filament/resources/order-resource.form.products.details.old_price'))->readOnly(),
                                        TextInput::make('original_discount_price')->label(__('filament/resources/order-resource.form.products.details.old_discount_price'))->readOnly(),
                                        TextInput::make('exchange_rate')->readOnly(),
                                        Select::make('currency_id')
                                            ->disabled()
                                            ->relationship('currency', 'name'),
                                    ])->columns(5),
                                ]),
                            Grid::make()->schema([

                                Group::make()->schema([

                                    Group::make()->schema([
                                        Select::make('product_id')->required()
                                            ->label(__('filament/resources/order-resource.form.products.selection.product'))
                                            ->searchable()->preload()->autofocus(fn (Page $livewire) => $livewire instanceof CreateRecord)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::updateAttributes($get, $set);
                                            })
                                            ->getSearchResultsUsing(function (string $search) {
                                                $products = Product::where(function ($query) use ($search) {
                                                    $query->where('name', 'like', "%$search%")
                                                        ->orWhere('sku', $search)
                                                        ->orWhereHas('brand', function ($q) use ($search) {
                                                            $q->where('name', 'like', "%$search%");
                                                        });
                                                })
                                                    ->whereNull('deleted_at')
                                                    ->limit(20)
                                                    ->with('brand:name,id')
                                                    ->get();
                                                $data = $products->mapWithKeys(function ($product) {
                                                    return [$product->getKey() => static::getCleanOptionString($product)];
                                                })->toArray();

                                                return $data;
                                                // $data = [];
                                                // foreach ($products as $product) {
                                                //     $data[$product->id] = $product->name.' - '.$product->brand->name.' - ('.$product->code.' )';
                                                // }

                                                // return $data;
                                            })
                                            ->allowHtml()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->live(true, 500)
                                            ->options(function ($state) {
                                                $products = Product::with('brand:name,id')
                                                    ->whereNull('deleted_at')
                                                    ->when($state, fn ($query) => $query->where('id', $state))
                                                    ->limit(11)
                                                    ->get();

                                                return $products->mapWithKeys(function ($product) {
                                                    return [$product->getKey() => static::getCleanOptionString($product)];
                                                })->toArray();
                                            })
                                            ->columnSpan(2),

                                        TextInput::make('quantity')
                                            ->required()->minValue(1)->default(1)
                                            ->label(__('filament/resources/order-resource.form.products.selection.quantity'))
                                            ->rules(['required', function (Get $get) {
                                                return function (string $attribute, $value, Closure $fail) use ($get) {
                                                    if (! blank($get('product_id'))) {
                                                        $product = Product::where('id', $get('product_id'))->first();
                                                        if ($product->has_variant && ! blank($get('variant_id'))) {
                                                            $variant = Variant::where('id', $get('variant_id'))->first();
                                                            if ($variant->quantity < $value) {
                                                                $fail($variant->quantity.' သာကျန်ရှိပါတော့သည်');
                                                            }
                                                        } else {
                                                            if ($product->quantity < $value) {
                                                                $fail($product->quantity.' သာကျန်ရှိပါတော့သည်');
                                                            }
                                                        }

                                                        return true;
                                                    }

                                                    return $fail('ထည့်ပေးပါရန်');
                                                };
                                            }])
                                            ->live(true, 500)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                // this is auto change discount price
                                                if (! blank($get('quantity'))) {
                                                    self::thisIsAutoChangeDiscountPrice($get, $set);
                                                }
                                            })
                                            ->numeric(),

                                        Select::make('variant_id')->required()
                                            ->hidden(fn (Get $get) => blank($get('product_id')) || ! Product::where('id', $get('product_id'))->select('has_variant')->first()->has_variant)
                                            ->label(__('filament/resources/order-resource.form.products.selection.variant'))
                                            ->searchable()->preload()->live(true, 500)
                                            ->afterStateUpdated(self::vartiantSectionCalculate())->columnSpan(2)
                                            ->relationship('variant', 'sku', modifyQueryUsing: function (Get $get, $query) {
                                                return $query->where('product_id', $get('product_id'));
                                            }),

                                        TextInput::make('price')
                                            ->numeric()->live(true, 500)
                                            ->label(__('filament/resources/order-resource.form.products.selection.price'))
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                // this is auto change discount price
                                                self::thisIsAutoChangeDiscountPrice($get, $set);
                                            })
                                            ->required()
                                            ->prefix(fn (Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id', $get('exchange_id'))->with('currency')->first()->currency->symbol_native),

                                        TextInput::make('discount')
                                            ->numeric()
                                            ->inputMode('decimal')->step(0.02)
                                            ->prefix('%')->minValue(0)->maxValue(100)
                                            ->label(__('filament/resources/order-resource.form.products.selection.discount'))
                                            ->live(true, 500)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                // this is auto change discount price
                                                self::thisIsAutoChangeDiscountPrice($get, $set);
                                            })
                                            ->default(0),

                                        TextInput::make('discount_price')
                                            ->numeric()->label(__('filament/resources/order-resource.form.products.selection.discount_price'))
                                            ->columnSpan(fn (Get $get) => blank($get('variant_id')) ? 1 : 2)
                                            ->live(true, 500)
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                // this is auto change discount percentage from discount price
                                                self::thisIsAutoChangeDiscountPercentage($get, $set);
                                            })
                                            ->default(0.0000),

                                        TextInput::make('sub_price')
                                            ->required()->suffix('ကျပ်')
                                            ->label(__('filament/resources/order-resource.form.products.selection.sub_price'))
                                            ->hint(self::getHtmlString())
                                            ->columnSpan(2)
                                            ->numeric(),

                                    ])->columns(3),

                                ])->columnSpan(2),

                                Group::make()->schema([

                                    Repeater::make('attributes')
                                        ->collapsible(false)->reorderable(false)
                                        ->addable(false)
                                        ->schema([

                                            Group::make()->schema([
                                                Hidden::make('attribute')
                                                    ->required()
                                                    ->label('Attribute'),

                                                Select::make('value')
                                                    ->multiple()
                                                    ->hiddenJs(<<<'JS'
                                                        !$get('attribute')
                                                    JS)
                                                    ->required()->searchable()->preload()->label('Value')->placeholder('Select')
                                                    ->options(function (Get $get) {
                                                        if (blank($get('attribute'))) {
                                                            return [];
                                                        }

                                                        // If attribute contains the actual attribute name (from updateAttributes)
                                                        if (is_array($get('attribute')) && ! empty($get('attribute'))) {
                                                            $attributeName = $get('attribute')[0] ?? '';
                                                            $attribute = Attribute::where('name', $attributeName)->first();
                                                            if ($attribute) {
                                                                return AttributeValue::where('attribute_id', $attribute->id)
                                                                    ->pluck('value', 'value'); // Use value as both key and display
                                                            }
                                                        }

                                                        return [];
                                                    }),

                                            ])->columns(1),

                                        ]),
                                ]),
                            ])->columns(3),
                        ])->columnSpan(2),

                    Group::make()->schema([

                        TextInput::make('invoice_number')->default(Str::upper('INV-'.uniqid()))->columnSpanFull()
                            ->label(__('filament/resources/order-resource.form.order_info.invoice_number')),

                        Toggle::make('walk_in_customer')
                            // ->live(onBlur: true)
                            ->columnSpanFull()
                            ->label(__('filament/resources/order-resource.form.order_info.walk_in.label'))
                            ->helperText(__('filament/resources/order-resource.form.order_info.walk_in.helper_text')),

                        Group::make()->schema([
                            Select::make('user_id')
                                ->searchable()->createOptionForm(self::customerFormFields())->label(__('filament/resources/order-resource.form.order_info.customer.name'))
                                ->afterStateUpdated(fn (Get $get, Set $set) => $set('user_address_id', null))
                                ->requiredIf('walk_in_customer', false)
                                ->relationship('user', 'name')->preload()->columnSpanFull(),

                            Select::make('user_address_id')
                                ->searchable()->columnSpanFull()
                                ->label(__('filament/resources/order-resource.form.order_info.customer.address'))
                                ->hiddenJs(<<<'JS'
                                    !$get('user_id')
                                JS)
                                ->requiredIf('walk_in_customer', false)
                                ->relationship('address', 'address', modifyQueryUsing: function (Get $get, $query) {
                                    return $query->where('user_id', $get('user_id'));
                                })->preload(),

                        ])->columnSpanFull()->hiddenJs(<<<'JS'
                            $get('walk_in_customer')
                        JS),

                        ToggleButtons::make('status')
                            ->default(OrderStatus::New->value)
                            ->columnSpanFull()->inline()
                            ->label(__('filament/resources/order-resource.form.order_info.status.label'))
                            ->options([
                                'new' => __('filament/resources/order-resource.form.order_info.status.options.new'),
                                'processing' => __('filament/resources/order-resource.form.order_info.status.options.processing'),
                            ])->required(),

                        DateTimePicker::make('created_at')
                            ->native(false)->label(__('filament/resources/order-resource.form.order_info.purchase_date'))
                            ->maxDate(now())->default(now())
                            ->minDate(now()->subMonth())->columnSpanFull(),

                        Section::make(__('filament/resources/order-resource.form.price_info.label'))
                            ->headerActions([
                                Action::make('တွက်မယ်')
                                    ->size('sm')->color('success')
                                    ->action(function (Get $get, Set $set) {
                                        self::getTotalPrice($get, $set);
                                    }),
                            ])
                            ->schema([

                                Group::make()->schema([
                                    TextInput::make('sub_total')
                                        ->label(__('filament/resources/order-resource.form.price_info.sub_total.label'))
                                        ->helperText(__('filament/resources/order-resource.form.price_info.sub_total.helper_text'))
                                        ->required()->default(0)->readOnly()
                                        ->placeholder(function (Get $get, Set $set) {
                                            self::getTotalPrice($get, $set);
                                        })
                                        ->numeric(),

                                    TextInput::make('delivery_fees')->suffix('ကျပ်')
                                        ->label(__('filament/resources/order-resource.form.price_info.delivery_fees'))
                                        ->required()->default(0)->live(true, 500)
                                        ->afterStateUpdated(function (Get $get, Set $set) {
                                            self::getTotalPrice($get, $set);
                                        })
                                        ->numeric(),
                                ])->columns(2),

                                Group::make()->schema([

                                    TextInput::make('tax')
                                        ->nullable()->default(0)->live(true, 500)->suffix('%')
                                        ->label(__('filament/resources/order-resource.form.price_info.tax.label'))
                                        ->helperText(__('filament/resources/order-resource.form.price_info.tax.helper_text'))
                                        ->afterStateUpdated(function (Get $get, Set $set) {
                                            self::getTotalPrice($get, $set);
                                        })
                                        ->numeric(),

                                    TextInput::make('tax_price')
                                        ->nullable()->default(0)->suffix('ကျပ်')
                                        ->label(__('filament/resources/order-resource.form.price_info.tax.amount'))
                                        ->helperText('amount of tax ')->readOnly()
                                        ->numeric(),
                                ])->columns(2),

                                TextInput::make('total_price')
                                    ->label(__('filament/resources/order-resource.form.price_info.total_price.label'))
                                    ->hint(self::getHtmlString())
                                    ->helperText(__('filament/resources/order-resource.form.price_info.total_price.helper_text'))
                                    ->suffix('ကျပ်')
                                    ->required()->default(0)->readOnly()
                                    ->numeric(),

                                TextInput::make('given_amount')
                                    ->label(__('filament/resources/order-resource.form.price_info.given_amount'))
                                    ->suffix('ကျပ်')
                                    ->numeric()
                                    ->live(true, 500)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calculateChangeAmount($get, $set);
                                    })
                                    ->default(0),

                                TextInput::make('change_amount')
                                    ->label(__('filament/resources/order-resource.form.price_info.change_amount'))
                                    ->suffix('ကျပ်')
                                    ->numeric()
                                    ->readOnly()
                                    ->default(0),

                            ])->columns(1),

                        Toggle::make('is_cod')
                            ->columnSpanFull()
                            ->label(__('filament/resources/order-resource.form.order_info.cod.label'))
                            ->helperText(__('filament/resources/order-resource.form.order_info.cod.helper_text')),

                        Group::make()->schema([

                            ToggleButtons::make('payment_id')
                                ->columnSpanFull()->inline()
                                ->label(__('filament/resources/order-resource.form.order_info.payment'))
                                ->grouped()
                                ->options(Payment::get()->pluck('name', 'id')),

                            Select::make('payment_account_id')
                                ->label(__('filament/resources/order-resource.form.order_info.payment_account'))
                                ->searchable()->preload()
                                ->hiddenJs(<<<'JS'
                                    !$get('payment_id')
                                JS)
                                ->relationship('paymentAccount', 'name', modifyQueryUsing: function (Get $get, $query) {
                                    return $query->where('payment_id', $get('payment_id'));
                                }),

                        ])->hiddenJs(<<<'JS'
                            $get('is_cod')
                        JS),

                        SpatieMediaLibraryFileUpload::make('slip')
                            ->collection('slips')
                            ->label(__('filament/resources/order-resource.form.payment_slip'))
                            ->imagePreviewHeight('700px')
                            ->image()->imageEditor(),

                    ]),

                ])->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\Orders\OrderResource::table($table);
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
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrderChart::class,
        ];
    }

    public static function thisIsAutoChangeDiscountPrice(Get $get, Set $set): void
    {
        if (! blank($get('discount')) && ! blank($get('price'))) {
            if ($get('discount') == 0) {
                $set('discount_price', 0);
                $set('sub_price', ($get('price') * $get('quantity')));
                self::getTotalPrice($get, $set);
            } else {
                $discountCalculate = $get('price') - ($get('discount') / 100 * $get('price'));
                $set('discount_price', $discountCalculate);
                $set('sub_price', $discountCalculate * $get('quantity'));
                self::getTotalPrice($get, $set);
            }
        }
    }

    public static function thisIsAutoChangeDiscountPercentage(Get $get, Set $set): void
    {
        if (! blank($get('discount_price')) && ! blank($get('price')) && $get('price') > 0) {
            if ($get('discount_price') == 0) {
                $set('discount', 100);
                $set('sub_price', 0);
                self::getTotalPrice($get, $set);
            } elseif ($get('discount_price') >= $get('price')) {
                // Discount price cannot be more than original price
                $set('discount', 0);
                $set('discount_price', $get('price'));
                $set('sub_price', $get('price') * $get('quantity'));
                self::getTotalPrice($get, $set);
            } else {
                $discountPercentage = (($get('price') - $get('discount_price')) / $get('price')) * 100;
                $set('discount', round($discountPercentage, 2));
                $set('sub_price', $get('discount_price') * $get('quantity'));
                self::getTotalPrice($get, $set);
            }
        }
    }

    public static function calculateChangeAmount(Get $get, Set $set): void
    {
        $givenAmount = (float) ($get('given_amount') ?? 0);
        $totalPrice = (float) ($get('total_price') ?? 0);

        $changeAmount = $givenAmount - $totalPrice;
        $set('change_amount', max(0, $changeAmount)); // Change amount cannot be negative
    }

    protected static function getHtmlString(): HtmlString
    {
        return new HtmlString(Blade::render('<x-filament::loading-indicator wire:loading class="h-5 w-5" />'));
    }

    public static function getTotalPrice(Get $get, Set $set): void
    {
        $total = 0;
        if (! blank($get('products'))) {
            foreach ($get('products') as $product) {
                if (! blank($product['quantity']) && ! blank($product['discount_price']) && $product['quantity'] != 0 && $product['discount_price'] != 0) {
                    $total += $product['discount_price'] * $product['quantity'];
                } elseif (! blank($product['quantity']) && ! blank($product['price']) && $product['quantity'] != 0 && $product['price'] != 0) {
                    $total += $product['price'] * $product['quantity'];
                }
            }
        }

        $set('sub_total', $total);
        if (blank($get('tax')) || $get('tax') == 0) {
            $superTotal = $total + ($get('delivery_fees') ?? 0);
            $set('total_price', (float) $superTotal);
        } else {
            // calcuate for tax price and total
            $grandTotalByTax = $get('tax') / 100 * $total;
            $superTotal = ($total + $grandTotalByTax) + ($get('delivery_fees') ?? 0);
            $set('total_price', (float) $superTotal);
            $set('tax_price', (float) $grandTotalByTax);
        }

        // Recalculate change amount when total price changes
        self::calculateChangeAmount($get, $set);
    }

    public static function updateAttributes(Get $get, Set $set): void
    {

        if (! blank($get('product_id'))) {
            $product = Product::where('id', $get('product_id'))->with('exchange')->first();

            if (! $product->has_variant) {

                if ($product->exchange->currency->code == 'MMK') {
                    $set('price', $product->price);
                    $set('discount', $product->discount);
                    $set('discount_price', $product->discount_price);

                    $set('original_buy_price', $product->buy_price);
                    $set('original_price', $product->price);
                    $set('original_discount_price', $product->discount_price);
                } else {
                    $set('price', $product->mmk_price);
                    $set('discount', $product->discount);
                    $set('discount_price', $product->mmk_discount_price);

                    $set('original_buy_price', $product->mmk_buy_price);
                    $set('original_price', $product->mmk_price);
                    $set('original_discount_price', $product->mmk_discount_price);
                }

                $set('exchange_rate', $product->exchange->rate);
                $set('currency_id', $product->exchange->currency_id);

                $data = [];
                foreach ($product->attributes as $att) {

                    $data[] = [
                        'attribute' => Attribute::where('id', $att['attribute_id'])->pluck('name')->toArray() ?? '',
                        'value' => AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray() ?? '',
                    ];
                }
                // dd($data);
                $set('attributes', $data);
                if ($get('discount') > 0) {
                    // has discount
                    $set('sub_price', $get('discount_price') * $get('quantity'));
                } else {
                    $set('sub_price', $get('price') * $get('quantity'));
                }
            } else {
                $set('variant_id', null);
            }
        }
    }

    public static function getCleanOptionString(Model $model): string
    {
        return view('filament.components.select-user-result')
            ->with('name', $model?->name)
            ->with('brand', $model?->brand?->name)
            ->with('sku', $model?->sku)
            ->with('category', $model?->category->name)
            ->render();
    }

    protected static function customerFormFields(): array
    {
        return [
            Grid::make()->schema([

                Group::make()->schema([
                    TextInput::make('name')
                        ->live()
                        ->maxLength(255)->required(),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(255)->nullable(),
                    TextInput::make('phone')
                        ->maxLength(255)->required(),

                    Hidden::make('seller_id')
                        ->default(CheckFilamentSellerUser::getSellerId()),

                    Hidden::make('password')
                        ->default(fn (Get $get) => Hash::make($get('name'))),
                ]),

                Group::make()->schema([
                    Repeater::make('addresses')
                        ->relationship('addresses')
                        ->schema([
                            Select::make('region_id')->live()
                                ->required()->searchable()->preload()
                                ->afterStateUpdated(fn (Set $set) => $set('township_id', null))
                                ->relationship('region', 'mm_name'),

                            Select::make('township_id')
                                ->hiddenJs(<<<'JS'
                                    !$get('region_id')
                                JS)
                                ->required()->searchable()->preload()
                                ->relationship('township', 'mm_name', modifyQueryUsing: fn ($query, Get $get) => $query->where('region_id', $get('region_id'))),

                            TextInput::make('address')
                                ->required(),
                        ]),
                ]),

            ]),
        ];
    }

    protected static function vartiantSectionCalculate(): Closure
    {
        return function (Get $get, Set $set) {
            if (! blank($get('variant_id'))) { // check if variant id is have

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

                $set('exchange_rate', $variant->product->exchange->rate);
                $set('currency_id', $variant->product->exchange->currency_id);

                $data = [];
                foreach ($variant->attributes as $key => $att) {

                    $data[] = [
                        'attribute' => Attribute::where('id', $att['attribute_id'])->first()->name ?? '',
                        //                        'value' => implode(',', AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray()) ??  '',
                        'value' => AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray() ?? '',
                    ];
                }
                self::thisIsAutoChangeDiscountPrice($get, $set);

                $set('attributes', $data);
            }
        };
    }
}
