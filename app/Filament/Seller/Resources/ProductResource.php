<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\ProductResource\Pages;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Exchange;
use App\Models\Product;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Business Section';

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/product-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/product-resource.navigation.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/product-resource.navigation.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()->schema([
                    Forms\Components\Group::make()->schema([

                        Forms\Components\Select::make('supplier_id')
                            ->label(__('filament/resources/product-resource.form.supplier.label'))
                            ->searchable()->preload()->live(onBlur: true, debounce: 200)
                            ->nullable()->relationship('supplier', 'name'),

                        Forms\Components\Select::make('exchange_id')
                            ->label(__('filament/resources/product-resource.form.exchange.label'))
                            ->searchable()->preload()
                            ->live(onBlur: true, debounce: 200)
                            ->options(function (Forms\Get $get) {
                                $seller_id = CheckFilamentSellerUser::getSellerId();
                                $data = Exchange::where('seller_id', $seller_id)->with('currency')->get()->map(function ($item) {
                                    return [$item->id => $item->currency->code];
                                });
                                $currencies = [];
                                foreach ($data as $da) {
                                    $currencies += $da;
                                }

                                return $currencies;
                            })
                            ->required(),

                        Forms\Components\Select::make('brand_id')
                            ->label(__('filament/resources/product-resource.form.brand.label'))
                            ->searchable()->preload()->live(onBlur: true, debounce: 200)
                            ->required()
                            ->relationship('brand', 'name'),

                        SelectTree::make('category_id')
                            ->label(__('filament/resources/product-resource.form.category.label'))
                            ->searchable()->searchable()
                            ->required()->enableBranchNode()->placeholder(__('filament/resources/product-resource.form.category.placeholder'))
                            ->relationship('category', 'name', 'parent_id'),

                        Forms\Components\TextInput::make('name')
                            ->label(__('filament/resources/product-resource.form.name.label'))
                            ->live(onBlur: true, debounce: 200)
                            ->required()->placeholder(__('filament/resources/product-resource.form.name.placeholder'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('sku')
                            ->default(Str::upper(uniqid()))
                            ->required(fn (Forms\Get $get) => ! $get('has_variant'))
                            ->hidden(fn (Forms\Get $get) => $get('has_variant'))
                            ->maxLength(255),

                        TextInput::make('expired_date')
                            ->label(__('filament/resources/product-resource.form.expired_date.label'))
                            ->type('date')
                            ->placeholder(__('filament/resources/product-resource.form.expired_date.placeholder'))
                            ->helperText(__('filament/resources/product-resource.form.expired_date.helper_text'))
                            ->live(onBlur: true, debounce: 200)
                            ->nullable(),

                        Forms\Components\RichEditor::make('description')
                            ->required()->visible(fn (Forms\Get $get) => ! $get('has_variant') || blank($get('exchange_id')))
                            ->maxLength(16777215)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('has_variant')
                            ->label(__('filament/resources/product-resource.form.variants.label'))
                            ->onIcon('heroicon-m-squares-2x2')
                            ->offIcon('heroicon-m-stop')
                            ->live(onBlur: true, debounce: 200)->label(__('filament/resources/product-resource.form.variants.label'))->disabled(fn (Page $livewire) => $livewire instanceof EditRecord)
                            ->helperText(__('filament/resources/product-resource.form.variants.helper_text'))
                            ->required(),

                        Forms\Components\Toggle::make('is_public')
                            ->label(__('filament/resources/product-resource.form.published.label'))
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-stop')
                            ->helperText(__('filament/resources/product-resource.form.published.helper_text'))
                            ->label('Published')->default(true)
                            ->required(),

                        Forms\Components\Toggle::make('is_recommended')
                            ->label(__('filament/resources/product-resource.form.recommended.label'))
                            ->onIcon('heroicon-m-hand-thumb-up')
                            ->offIcon('heroicon-m-hand-thumb-down')
                            ->helperText(__('filament/resources/product-resource.form.recommended.helper_text'))
                            ->label('Recommend')
                            ->required(),

                        Forms\Components\Section::make(__('filament/resources/product-resource.form.shipping.label'))
                            ->description(__('filament/resources/product-resource.form.shipping.description'))
                            ->schema([
                                Forms\Components\Toggle::make('is_return')
                                    ->label(__('filament/resources/product-resource.form.shipping.return.label'))
                                    ->onIcon('heroicon-m-shield-check')
                                    ->offIcon('heroicon-m-stop')
                                    ->label('Return Product')->default(true)
                                    ->helperText(__('filament/resources/product-resource.form.shipping.return.helper_text'))
                                    ->required(),

                                Forms\Components\Toggle::make('is_shipped')
                                    ->label(__('filament/resources/product-resource.form.shipping.shipped.label'))
                                    ->onIcon('heroicon-m-truck')
                                    ->offIcon('heroicon-m-stop')
                                    ->label('Shipping Product')->default(true)
                                    ->helperText(__('filament/resources/product-resource.form.shipping.shipped.helper_text'))
                                    ->required(),
                            ])->compact(),

                    ])->columns(2),

                    Forms\Components\Group::make()->schema([

                        Forms\Components\Section::make(__('filament/resources/product-resource.form.single_product.label'))
                            ->description(__('filament/resources/product-resource.form.single_product.description'))
                            ->compact()
                            ->schema([

                                Forms\Components\TextInput::make('buy_price')
                                    ->label(__('filament/resources/product-resource.form.single_product.buy_price.label'))
                                    ->placeholder(__('filament/resources/product-resource.form.single_product.buy_price.placeholder'))
                                    ->helperText(__('filament/resources/product-resource.form.single_product.buy_price.helper_text'))
                                    ->live(onBlur: true)
                                    ->required(),

                                Forms\Components\TextInput::make('price')
                                    ->label(__('filament/resources/product-resource.form.single_product.price.label'))
                                    ->placeholder(__('filament/resources/product-resource.form.single_product.price.placeholder'))
                                    ->numeric()->hidden(fn (Forms\Get $get) => blank($get('buy_price')))
                                    ->live(onBlur: true, debounce: 200)
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        // this is auto change discount price
                                        self::getDiscountPrice($get, $set);
                                    })
                                    ->required(fn (Forms\Get $get) => ! $get('has_variant'))
                                    ->prefix(fn (Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id', $get('exchange_id'))->with('currency')->first()->currency->symbol_native),

                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('filament/resources/product-resource.form.single_product.quantity.label'))
                                    ->helperText(__('filament/resources/product-resource.form.single_product.quantity.helper_text'))
                                    ->numeric()->required(fn (Forms\Get $get) => ! $get('has_variant')),

                                Forms\Components\TextInput::make('MOQ')
                                    ->label(__('filament/resources/product-resource.form.single_product.moq.label'))
                                    ->helperText(__('filament/resources/product-resource.form.single_product.moq.helper_text'))
                                    ->default(1)->numeric()
                                    ->required(fn (Forms\Get $get) => ! $get('has_variant')),

                                Forms\Components\TextInput::make('discount')
                                    ->label(__('filament/resources/product-resource.form.single_product.discount.label'))
                                    ->numeric()->suffix('%')->minValue(0)
                                    ->integer()->rules('max:100')
                                    ->live(onBlur: true, debounce: 200)
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        // this is auto change discount price
                                        self::getDiscountPrice($get, $set);
                                    })
                                    ->default(0),

                                Forms\Components\TextInput::make('discount_price')
                                    ->label(__('filament/resources/product-resource.form.single_product.discount_price.label'))
                                    ->numeric()
                                    ->live(onBlur: true, debounce: 200)
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        // this is auto change discount percentage
                                        self::getDiscountPercentage($get, $set);
                                    })
                                    ->prefix(fn (Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id', $get('exchange_id'))->with('currency')->first()->currency->symbol_native)
                                    ->default(0.0000),

                                Forms\Components\Grid::make()->schema([

                                    Forms\Components\Group::make()->schema([

                                        Forms\Components\Toggle::make('is_whole_sale')
                                            ->onIcon('heroicon-m-hand-thumb-up')
                                            ->offIcon('heroicon-m-hand-thumb-down')
                                            ->helperText('For your product`s wholesale')
                                            ->label('WholeSales')
                                            ->required(),
                                    ]),

                                    Forms\Components\Group::make()->schema([
                                        Forms\Components\Section::make('မြန်မာ ကျပ် ပြပုံစံ')
                                            ->compact()
                                            ->schema([
                                                Forms\Components\TextInput::make('mmk_buy_price')
                                                    ->label('၀ယ်ရင်းစျေး')
                                                    ->readOnly(),

                                                Forms\Components\TextInput::make('mmk_price')
                                                    ->label('ရောင်းစျေး')
                                                    ->readOnly(),

                                                Forms\Components\TextInput::make('mmk_discount_price')
                                                    ->readOnly()->label('လျော့စျေး'),

                                            ])->columns(3)->visible(function (Forms\Get $get) {
                                                if (! blank($get('exchange_id'))) {
                                                    $exchange = Exchange::where('id', $get('exchange_id'))->with('currency:id,code')->first();
                                                    if ($exchange->currency->code != 'MMK') {
                                                        return true;
                                                    }
                                                }

                                                return false;
                                            }),
                                    ])->columnSpan(2),

                                ])->columns(3),

                                Forms\Components\Repeater::make('attributes')
                                    ->label(__('filament/resources/product-resource.form.variants.attributes.label'))
                                    ->reorderableWithButtons()
                                    ->schema([

                                        Forms\Components\Group::make()->schema([
                                            Forms\Components\Select::make('attribute_id')->live(onBlur: true, debounce: 200)
                                                ->required()->searchable()->preload()
                                                ->label(__('filament/resources/product-resource.form.variants.attributes.attribute.label'))
                                                ->placeholder(__('filament/resources/product-resource.form.variants.attributes.attribute.placeholder'))
                                                ->afterStateUpdated(fn (Forms\Set $set) => $set('attribute_value_id', null))
                                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                ->options(function (Forms\Get $get) {
                                                    if (Filament::getAuthGuard() == 'seller') {
                                                        return Attribute::where('seller_id', CheckFilamentSellerUser::getSellerId())->pluck('name', 'id');
                                                    }

                                                    return Attribute::where('seller_id', $get('../../seller_id'))->pluck('name', 'id');
                                                }),

                                            Forms\Components\Select::make('attribute_value_id')
                                                ->multiple()
                                                ->label(__('filament/resources/product-resource.form.variants.attributes.value.label'))
                                                ->placeholder(__('filament/resources/product-resource.form.variants.attributes.value.placeholder'))
                                                ->hidden(fn (Forms\Get $get) => blank($get('attribute_id')))
                                                ->required()->searchable()->preload()
                                                ->options(fn (Forms\Get $get) => AttributeValue::where('attribute_id', $get('attribute_id'))->pluck('value', 'id')),

                                        ])->columns(2),
                                        //
                                    ])->columnSpanFull(),

                                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                                    ->collection('images')->imageEditor()->multiple()
                                    ->panelLayout('grid')
                                    ->reorderable()
                                    ->label(__('filament/resources/product-resource.form.variants.images.label'))
                                    ->image()->rules('required')->columnSpanFull(),

                            ])->columns(2),

                    ])->hidden(fn (Forms\Get $get) => $get('has_variant') || blank($get('exchange_id'))),

                    Forms\Components\Group::make()->schema([
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->maxLength(16777215)
                            ->columnSpanFull(),
                    ])->hidden(fn (Forms\Get $get) => ! $get('has_variant') || blank($get('exchange_id'))),

                    Forms\Components\Group::make()->schema([

                        Forms\Components\Repeater::make('variants')
                            ->relationship('variants')
                            ->minItems(2)->defaultItems(2)
                            ->collapsible()->reorderable()->cloneable()
                            ->schema([
                                Forms\Components\Grid::make()->schema([
                                    Forms\Components\Group::make()->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('filament/resources/product-resource.form.variants.name.label'))
                                            ->placeholder(__('filament/resources/product-resource.form.variants.name.placeholder'))
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('sku')
                                            ->label(__('filament/resources/product-resource.form.variants.sku.label'))
                                            ->unique(ignoreRecord: true)
                                            ->required(),

                                        Forms\Components\TextInput::make('buy_price')
                                            ->label(__('filament/resources/product-resource.form.variants.buy_price.label'))
                                            ->helperText(__('filament/resources/product-resource.form.variants.buy_price.helper_text'))
                                            ->live(onBlur: true)
                                            ->required(),

                                        Forms\Components\TextInput::make('price')
                                            ->label(__('filament/resources/product-resource.form.variants.price.label'))
                                            ->placeholder(__('filament/resources/product-resource.form.variants.price.placeholder'))
                                            ->readOnly(fn (Forms\Get $get) => blank($get('buy_price')))
                                            ->numeric()->live(onBlur: true, debounce: 200)->required()
                                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                                // this is auto change discount price
                                                self::getDiscountPrice($get, $set, true);
                                            })
                                            ->prefix(fn (Forms\Get $get) => blank($get('../../exchange_id')) ? '' : Exchange::where('id', $get('../../exchange_id'))->with('currency')->first()->currency->symbol_native),

                                        Forms\Components\Group::make()->schema([
                                            Forms\Components\TextInput::make('discount')
                                                ->label(__('filament/resources/product-resource.form.variants.discount.label'))
                                                ->numeric()->prefix('%')->integer()->maxValue(100)
                                                ->live(onBlur: true, debounce: 200)
                                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                                    // this is auto change discount price
                                                    self::getDiscountPrice($get, $set, true);
                                                })
                                                ->default(0),

                                            Forms\Components\TextInput::make('discount_price')
                                                ->label(__('filament/resources/product-resource.form.variants.discount_price.label'))
                                                ->numeric()
                                                ->live(onBlur: true, debounce: 200)
                                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                                    // this is auto change discount percentage
                                                    self::getDiscountPercentage($get, $set, true);
                                                })
                                                ->prefix(fn (Forms\Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id', $get('exchange_id'))->with('currency')->first()->currency->symbol_native)
                                                ->default(0.0000),

                                        ])->columns(2),

                                        Forms\Components\TextInput::make('quantity')
                                            ->label(__('filament/resources/product-resource.form.variants.quantity.label'))
                                            ->placeholder(__('filament/resources/product-resource.form.variants.quantity.placeholder'))
                                            ->numeric()->required(),

                                        Forms\Components\TextInput::make('MOQ')
                                            ->label(__('filament/resources/product-resource.form.variants.moq.label'))
                                            ->helperText(__('filament/resources/product-resource.form.variants.moq.helper_text'))
                                            ->default(1)
                                            ->numeric(),

                                        Forms\Components\Toggle::make('is_whole_sale')
                                            ->onIcon('heroicon-m-hand-thumb-up')
                                            ->offIcon('heroicon-m-hand-thumb-down')
                                            ->helperText('For your product`s wholesale')
                                            ->label('WholeSales')
                                            ->required(),

                                    ]),

                                    Forms\Components\Group::make()->schema([

                                        // this is myanmar section
                                        Forms\Components\Section::make()
                                            ->description(__('filament/resources/product-resource.form.variants.mmk_section.description'))
                                            ->compact()
                                            ->schema([

                                                Forms\Components\TextInput::make('mmk_buy_price')
                                                    ->label(__('filament/resources/product-resource.form.variants.mmk_section.buy_price'))
                                                    ->readOnly()->columnSpanFull(),

                                                Forms\Components\TextInput::make('mmk_price')
                                                    ->label(__('filament/resources/product-resource.form.variants.mmk_section.price'))
                                                    ->readOnly()->columnSpanFull(),

                                                Forms\Components\TextInput::make('mmk_discount_price')
                                                    ->label(__('filament/resources/product-resource.form.variants.mmk_section.discount_price'))
                                                    ->readOnly()->label('လျော့စျေး')->columnSpanFull(),

                                            ])->columns(2)->visible(function (Forms\Get $get) {
                                                if (! blank($get('../../exchange_id'))) {
                                                    $exchange = Exchange::where('id', $get('../../exchange_id'))->with('currency:id,code')->first();
                                                    if ($exchange->currency->code != 'MMK') {
                                                        return true;
                                                    }
                                                }

                                                return false;
                                            }),

                                        Forms\Components\Repeater::make('attributes')
                                            ->label(__('filament/resources/product-resource.form.variants.attributes.label'))
                                            ->reorderableWithButtons()
                                            ->schema([

                                                Forms\Components\Group::make()->schema([

                                                    Forms\Components\Select::make('attribute_id')
                                                        ->label(__('filament/resources/product-resource.form.variants.attributes.attribute.label'))
                                                        ->placeholder(__('filament/resources/product-resource.form.variants.attributes.attribute.placeholder'))
                                                        ->live(onBlur: true, debounce: 200)
                                                        ->required()->searchable()->preload()->label('Attribute')->placeholder('Select')
                                                        ->afterStateUpdated(fn (Forms\Set $set) => $set('attribute_value_id', null))
                                                        ->options(function (Forms\Get $get) {
                                                            // ဒါက admin အတွက်စစ်တာပါ ဗျာ
                                                            if (Filament::getAuthGuard() == 'seller') {
                                                                return Attribute::where('seller_id', CheckFilamentSellerUser::getSellerId())->pluck('name', 'id');
                                                            }

                                                            return Attribute::where('seller_id', $get('../../../../seller_id'))->pluck('name', 'id');
                                                        }),

                                                    Forms\Components\Select::make('attribute_value_id')
                                                        ->label(__('filament/resources/product-resource.form.variants.attributes.value.label'))
                                                        ->placeholder(__('filament/resources/product-resource.form.variants.attributes.value.placeholder'))
                                                        ->multiple()
                                                        ->hidden(fn (Forms\Get $get) => blank($get('attribute_id')))
                                                        ->required()->searchable()->preload()->label('Value')->placeholder('Select')
                                                        ->options(fn (Forms\Get $get) => AttributeValue::where('attribute_id', $get('attribute_id'))->pluck('value', 'id')),

                                                ])->columns(1),

                                            ])->columnSpanFull()->hidden(function (Forms\Get $get) {
                                                // ဒါက admin အတွက်စစ်တာပါ ဗျာ
                                                if (Filament::getAuthGuard() == 'admin') {
                                                    return blank($get('../../seller_id'));
                                                }

                                                return false;
                                            }),
                                    ])->columnSpan(1),

                                ])->columns(2),

                                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                                    ->label(__('filament/resources/product-resource.form.variants.images.label'))
                                    ->helperText(__('filament/resources/product-resource.form.variants.images.helper_text'))
                                    ->collection('images')->imageEditor()->multiple()
                                    ->panelLayout('grid')
                                    //    ->helperText('Clone Action လုပ်ပါက images ကို နောက်ဆုံးမှ ထည့်ပေးပါရန် (သို့) edit မှာ clone ပေးပါရန်')
                                    ->rules('required')->columnSpanFull(),

                            ])
                            ->columns(3)->grid(2)
                            ->visible(fn (Forms\Get $get) => $get('has_variant') && ! blank($get('exchange_id'))),
                    ])->columnSpanFull(),

                ]),

            ]);
    }

    public static function getDiscountPrice($get, $set, $forVariants = false)
    {

        if ($get('discount') != null && ! blank($get('price'))) {
            $discountCalculate = $get('price') - ($get('discount') / 100 * $get('price'));
            $set('discount_price', (float) $discountCalculate);
        }

        if ($get('discount') == 0) {
            $set('discount_price', 0.00);
        }

        if (! blank($get('price'))) {
            self::getDiscountMmk($get, $set, $forVariants);
        }
    }

    public static function getDiscountPercentage($get, $set, $forVariants = false)
    {
        if ($get('discount_price') != null && ! blank($get('price')) && $get('price') > 0) {
            // Calculate discount percentage: ((original_price - discount_price) / original_price) * 100
            $discountPercentage = (($get('price') - $get('discount_price')) / $get('price')) * 100;

            // Ensure percentage is between 0 and 100
            $discountPercentage = (int) max(0, min(100, round($discountPercentage, 2)));

            $set('discount', $discountPercentage);
        }

        if ($get('discount_price') == 0 || $get('discount_price') >= $get('price')) {
            $set('discount', 0);
        }

        if (! blank($get('price'))) {
            self::getDiscountMmk($get, $set, $forVariants);
        }
    }

    /**
     * @param  Forms\Get  $get
     * @param  Forms\Set  $set
     */
    public static function getDiscountMmk($get, $set, $forVariants): void
    {

        if ($forVariants) {
            $exchangeID = $get('../../exchange_id');
        } else {
            $exchangeID = $get('exchange_id');
        }

        if ($exchangeID) {
            $exchange = Exchange::where('id', $exchangeID)->with('currency:id,code')->first();
            if ($exchange->currency->code != 'MMK') {
                $getMMKExchange = Exchange::whereHas('currency', function ($q) {
                    return $q->where('code', 'MMK');
                })->first();

                $getBuyMMKPriceFromSelectedCurrency = ($getMMKExchange->rate / $exchange->rate) * $get('buy_price');
                $removeLastTowDigitsToZeroForBuyPrice = self::modifyPrice($getBuyMMKPriceFromSelectedCurrency);
                $set('mmk_buy_price', $removeLastTowDigitsToZeroForBuyPrice);

                $getMMKPriceFromSelectedCurrency = ($getMMKExchange->rate / $exchange->rate) * $get('price');
                $removeLastTowDigitsToZero = self::modifyPrice($getMMKPriceFromSelectedCurrency);
                $set('mmk_price', $removeLastTowDigitsToZero);

                // calculate discount price mmk_price based
                $discountCalculateForMMK = $getMMKPriceFromSelectedCurrency - ($get('discount') / 100 * $getMMKPriceFromSelectedCurrency);
                $removeLastTowDigitsToZeroForMMKDiscountPrice = self::modifyPrice($discountCalculateForMMK);

                $set('mmk_discount_price', $removeLastTowDigitsToZeroForMMKDiscountPrice);
            }
        }
    }

    public static function modifyPrice($price)
    {
        // Round the price to the nearest hundred
        $roundedPrice = round($price, -2);

        // Ensure the last two digits are "00"
        $modifiedPrice = $roundedPrice - ($roundedPrice % 100);

        return $modifiedPrice;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['brand', 'category', 'supplier', 'exchange.currency']))
            ->columns([
                Tables\Columns\TextColumn::make('category.name')->size('sm')
                    ->label(__('filament/resources/product-resource.table.columns.category'))
                    ->description(fn (Product $product) => $product->brand?->name)
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament/resources/product-resource.table.columns.name'))
                    ->description(fn (Product $product) => $product->sku)
                    ->searchable(),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label(__('filament/resources/product-resource.table.columns.supplier'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('exchange.currency.code')
                    ->label(__('filament/resources/product-resource.table.columns.currency'))
                    ->description(fn (Product $product) => $product->exchange->currency->name)
                    ->sortable(),
                TextColumn::make('price')->numeric(),
                TextColumn::make('mmk_buy_price')->numeric(),
                TextColumn::make('quantity')->numeric(),
                TextColumn::make('total_price')
                    ->state(function (Product $record): float {
                        return $record->quantity * $record->mmk_buy_price;
                    })
                    ->numeric()
                    // ->money('mmk')
                    ->label('Total Capital'),

                //                Tables\Columns\IconColumn::make('has_variant')
                //                    ->boolean(),

                Tables\Columns\IconColumn::make('is_public')
                    ->label(__('filament/resources/product-resource.table.columns.is_public'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_recommended')
                    ->label(__('filament/resources/product-resource.table.columns.is_recommended'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label(__('filament/resources/product-resource.table.filters.brand'))
                    ->searchable()->preload()->label('Brand Name')
                    ->relationship('brand', 'name'),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('filament/resources/product-resource.table.filters.category'))
                    ->searchable()->preload()->label('Category Name')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label(__('filament/resources/product-resource.table.filters.supplier'))
                    ->searchable()->preload()->label('Supplier Name')
                    ->relationship('supplier', 'name'),
                Tables\Filters\Filter::make('stock')
                    ->form([
                        Forms\Components\Toggle::make('out_stock')
                            ->label('Out Stock')
                            ->helperText('လက်ကျန်ပစွည်း ၁၀ ခုအောက်စစ်ရန်'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['out_stock'], function ($q) {
                            $q->where('quantity', '<', 10)->orWhereHas('variants', function ($q) {
                                $q->where('quantity', '<', 10);
                            });
                        });
                    }),

                Tables\Filters\Filter::make('exists_stock')
                    ->form([
                        Forms\Components\Toggle::make('exists_stocks')
                            ->label('Exists Stock')
                            ->helperText('လက်ကျန်ပစွည်း ကျန်ရှိသော ပစ္စည်းများ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['exists_stocks'], function ($q) {
                            $q->where('quantity', '>', 0)->orWhereHas('variants', function ($q) {
                                $q->where('quantity', '>', 0);
                            });
                        });
                    }),

                Tables\Filters\Filter::make('is_whole_sale')
                    ->form([
                        Forms\Components\Toggle::make('is_whole_sale')
                            ->label('Whole Sale')
                            ->helperText('Whole Sale ပစွည်းများကို စစ်ရန်'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['is_whole_sale'], function ($q) {
                            $q->where('is_whole_sale', true)->orWhereHas('variants', function ($q) {
                                $q->where('is_whole_sale', true);
                            });
                        });
                    }),

                Tables\Filters\Filter::make('Empty Stock')
                    ->form([
                        Forms\Components\Toggle::make('empty_out_stock')
                            ->label('Empty Stock')
                            ->onColor('danger')
                            ->helperText('ကုန်နေသော ပစွည်း'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['empty_out_stock'], function ($q) {
                            $q->where('quantity', '=', 0)->orWhereHas('variants', function ($q) {
                                $q->where('quantity', '=', 0);
                            });
                        });
                    }),
                Tables\Filters\TrashedFilter::make(),

            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ReplicateAction::make()
                    ->modalWidth('sm')
                    ->form([
                        Placeholder::make('အကြောင်းအရာ')
                            ->content('ပစ္စည်းကူးယူရန် Copy ပွားနိုင်ပါသည်။'),
                        Forms\Components\TextInput::make('name'),
                        Forms\Components\TextInput::make('sku'),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('images')->collection('images')->previewable(false)->multiple(),
                    ])
                    ->mountUsing(function (Forms\ComponentContainer $form, Product $record) {
                        $form->fill([
                            'sku' => uniqid(),
                            'name' => $record->name,
                            'images' => $record->images,
                        ]);
                    })->beforeReplicaSaved(function (Model $replica, array $data) {
                        $replica->fill($data);
                    })->after(function (Model $replica, Product $product) {
                        $medias = $product->getMedia('images')->first();
                        if ($medias) {
                            $medias->copy($replica, 'images');
                        }
                    })
                    ->hidden(fn (Product $product) => $product->has_variant),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()->modalWidth(fn (Product $product) => $product->has_variant ? '5xl' : '4xl')->slideOver(),
            ])->defaultSort('id', 'desc')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id', CheckFilamentSellerUser::getSellerId())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
