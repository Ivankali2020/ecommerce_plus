<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Admin\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Admin\Resources\ProductResource\Pages\ListProducts;
use App\Helper\CheckFilamentSellerUser;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Exchange;
use App\Models\Product;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Business Section';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()->schema([
                    Group::make()->schema([

                        Select::make('seller_id')
                            ->label(__('filament/resources/product-resource.form.seller.label'))
                            ->searchable()->preload()->live(onBlur: true, debounce: 200)
                            ->hidden(Filament::getAuthGuard() == 'seller')
                            ->afterStateUpdated(fn (Set $set) => $set('exchange_id', null))
                            ->required()->relationship('seller', 'name'),

                        Select::make('supplier_id')
                            ->label(__('filament/resources/product-resource.form.supplier.label'))
                            ->searchable()->preload()->live(onBlur: true, debounce: 200)
                            ->nullable()->relationship('supplier', 'name'),

                        Select::make('exchange_id')
                            ->label(__('filament/resources/product-resource.form.exchange.label'))
                            ->searchable()->preload()->label('Currency')
                            ->live(onBlur: true, debounce: 200)
                            ->hidden(fn (Get $get) => blank($get('seller_id')) && Filament::getAuthGuard() == 'admin')
                            ->options(function (Get $get) {
                                $seller_id = $get('seller_id') ?? CheckFilamentSellerUser::getSellerId();
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

                        Select::make('brand_id')
                            ->label(__('filament/resources/product-resource.form.brand.label'))
                            ->searchable()->preload()->live(onBlur: true, debounce: 200)
                            ->required()
                            ->options(function (Get $get) {
                                $seller_id = $get('seller_id') ?? CheckFilamentSellerUser::getSellerId();

                                return Brand::where('seller_id', $seller_id)->pluck('name', 'id');
                            }),

                        SelectTree::make('category_id')
                            ->label(__('filament/resources/product-resource.form.category.label'))
                            ->searchable()->label('Category')->searchable()
                            ->required()->enableBranchNode()->placeholder(__('filament/resources/product-resource.form.category.placeholder'))
                            ->relationship('category', 'name', 'parent_id', modifyQueryUsing: function (Get $get, $query) {
                                $seller_id = $get('seller_id') ?? CheckFilamentSellerUser::getSellerId();

                                return $query->where('seller_id', $seller_id);
                            }),

                        TextInput::make('name')
                            ->label(__('filament/resources/product-resource.form.name.label'))
                            ->live(onBlur: true, debounce: 200)
                            ->required()->placeholder(__('filament/resources/product-resource.form.name.placeholder'))
                            ->maxLength(255),

                        TextInput::make('sku')
                            ->default(Str::upper(uniqid()))
                            ->required(fn (Get $get) => ! $get('has_variant'))
                            ->hidden(fn (Get $get) => $get('has_variant'))
                            ->maxLength(255),

                        RichEditor::make('description')
                            ->required()->visible(fn (Get $get) => ! $get('has_variant') || blank($get('exchange_id')))
                            ->maxLength(16777215)
                            ->columnSpanFull(),

                        Toggle::make('has_variant')
                            ->label(__('filament/resources/product-resource.form.variants.label'))
                            ->onIcon('heroicon-m-squares-2x2')
                            ->offIcon('heroicon-m-stop')
                            ->live(onBlur: true, debounce: 200)->label(__('filament/resources/product-resource.form.variants.label'))->disabled(fn (Page $livewire) => $livewire instanceof EditRecord)
                            ->helperText(__('filament/resources/product-resource.form.variants.helper_text'))
                            ->required(),

                        Toggle::make('is_public')
                            ->label(__('filament/resources/product-resource.form.published.label'))
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-stop')
                            ->helperText(__('filament/resources/product-resource.form.published.helper_text'))
                            ->label('Published')->default(true)
                            ->required(),

                        Toggle::make('is_recommended')
                            ->label(__('filament/resources/product-resource.form.recommended.label'))
                            ->onIcon('heroicon-m-hand-thumb-up')
                            ->offIcon('heroicon-m-hand-thumb-down')
                            ->helperText(__('filament/resources/product-resource.form.recommended.helper_text'))
                            ->label('Recommend')
                            ->required(),

                        Section::make(__('filament/resources/product-resource.form.shipping.label'))
                            ->description(__('filament/resources/product-resource.form.shipping.description'))
                            ->schema([
                                Toggle::make('is_return')
                                    ->label(__('filament/resources/product-resource.form.shipping.return.label'))
                                    ->onIcon('heroicon-m-shield-check')
                                    ->offIcon('heroicon-m-stop')
                                    ->label('Return Product')->default(true)
                                    ->helperText(__('filament/resources/product-resource.form.shipping.return.helper_text'))
                                    ->required(),

                                Toggle::make('is_shipped')
                                    ->label(__('filament/resources/product-resource.form.shipping.shipped.label'))
                                    ->onIcon('heroicon-m-truck')
                                    ->offIcon('heroicon-m-stop')
                                    ->label('Shipping Product')->default(true)
                                    ->helperText(__('filament/resources/product-resource.form.shipping.shipped.helper_text'))
                                    ->required(),
                            ])->compact(),

                    ])->columns(2),

                    Group::make()->schema([

                        Section::make(__('filament/resources/product-resource.form.single_product.label'))
                            ->description(__('filament/resources/product-resource.form.single_product.description'))
                            ->compact()
                            ->schema([

                                TextInput::make('buy_price')
                                    ->label(__('filament/resources/product-resource.form.single_product.buy_price.label'))
                                    ->placeholder(__('filament/resources/product-resource.form.single_product.buy_price.placeholder'))
                                    ->helperText(__('filament/resources/product-resource.form.single_product.buy_price.helper_text'))
                                    ->live(onBlur: true)
                                    ->required(),

                                TextInput::make('price')
                                    ->label(__('filament/resources/product-resource.form.single_product.price.label'))
                                    ->placeholder(__('filament/resources/product-resource.form.single_product.price.placeholder'))
                                    ->numeric()->hidden(fn (Get $get) => blank($get('buy_price')))
                                    ->live(onBlur: true, debounce: 200)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        // this is auto change discount price
                                        self::getDiscountPrice($get, $set);
                                    })
                                    ->required(fn (Get $get) => ! $get('has_variant'))
                                    ->prefix(fn (Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id', $get('exchange_id'))->with('currency')->first()->currency->symbol_native),

                                TextInput::make('quantity')
                                    ->label(__('filament/resources/product-resource.form.single_product.quantity.label'))
                                    ->helperText(__('filament/resources/product-resource.form.single_product.quantity.helper_text'))
                                    ->numeric()->required(fn (Get $get) => ! $get('has_variant')),

                                TextInput::make('MOQ')
                                    ->label(__('filament/resources/product-resource.form.single_product.moq.label'))
                                    ->helperText(__('filament/resources/product-resource.form.single_product.moq.helper_text'))
                                    ->default(1)->numeric()
                                    ->required(fn (Get $get) => ! $get('has_variant')),

                                TextInput::make('discount')
                                    ->label(__('filament/resources/product-resource.form.single_product.discount.label'))
                                    ->numeric()->suffix('%')->minValue(0)
                                    ->integer()->rules('max:100')
                                    ->live(onBlur: true, debounce: 200)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        // this is auto change discount price
                                        self::getDiscountPrice($get, $set);
                                    })
                                    ->default(0),

                                TextInput::make('discount_price')
                                    ->label(__('filament/resources/product-resource.form.single_product.discount_price.label'))
                                    ->numeric()
                                    ->prefix(fn (Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id', $get('exchange_id'))->with('currency')->first()->currency->symbol_native)
                                    ->default(0.0000),

                                Grid::make()->schema([

                                    Group::make()->schema([

                                        Toggle::make('is_whole_sale')
                                            ->onIcon('heroicon-m-hand-thumb-up')
                                            ->offIcon('heroicon-m-hand-thumb-down')
                                            ->helperText('For your product`s wholesale')
                                            ->label('WholeSales')
                                            ->required(),
                                    ]),

                                    Group::make()->schema([
                                        Section::make('မြန်မာ ကျပ် ပြပုံစံ')
                                            ->compact()
                                            ->schema([
                                                TextInput::make('mmk_buy_price')
                                                    ->label('၀ယ်ရင်းစျေး')
                                                    ->readOnly(),

                                                TextInput::make('mmk_price')
                                                    ->label('ရောင်းစျေး')
                                                    ->readOnly(),

                                                TextInput::make('mmk_discount_price')
                                                    ->readOnly()->label('လျော့စျေး'),

                                            ])->columns(3)->visible(function (Get $get) {
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

                                Repeater::make('attributes')
                                    ->label(__('filament/resources/product-resource.form.variants.attributes.label'))
                                    ->reorderableWithButtons()
                                    ->schema([

                                        Group::make()->schema([
                                            Select::make('attribute_id')->live(onBlur: true, debounce: 200)
                                                ->required()->searchable()->preload()
                                                ->label(__('filament/resources/product-resource.form.variants.attributes.attribute.label'))
                                                ->placeholder(__('filament/resources/product-resource.form.variants.attributes.attribute.placeholder'))
                                                ->afterStateUpdated(fn (Set $set) => $set('attribute_value_id', null))
                                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                ->options(function (Get $get) {
                                                    if (Filament::getAuthGuard() == 'seller') {
                                                        return Attribute::where('seller_id', CheckFilamentSellerUser::getSellerId())->pluck('name', 'id');
                                                    }

                                                    return Attribute::where('seller_id', $get('../../seller_id'))->pluck('name', 'id');
                                                }),

                                            Select::make('attribute_value_id')
                                                ->multiple()
                                                ->label(__('filament/resources/product-resource.form.variants.attributes.value.label'))
                                                ->placeholder(__('filament/resources/product-resource.form.variants.attributes.value.placeholder'))
                                                ->hidden(fn (Get $get) => blank($get('attribute_id')))
                                                ->required()->searchable()->preload()
                                                ->options(fn (Get $get) => AttributeValue::where('attribute_id', $get('attribute_id'))->pluck('value', 'id')),

                                        ])->columns(2),
                                        //
                                    ])->columnSpanFull(),

                                SpatieMediaLibraryFileUpload::make('images')
                                    ->collection('images')->imageEditor()->multiple()
                                    ->panelLayout('grid')
                                    ->label(__('filament/resources/product-resource.form.variants.images.label'))
                                    ->image()->rules('required')->columnSpanFull(),

                            ])->columns(2),

                    ])->hidden(fn (Get $get) => $get('has_variant') || blank($get('exchange_id'))),

                    Group::make()->schema([
                        RichEditor::make('description')
                            ->required()
                            ->maxLength(16777215)
                            ->columnSpanFull(),
                    ])->hidden(fn (Get $get) => ! $get('has_variant') || blank($get('exchange_id'))),

                    Group::make()->schema([

                        Repeater::make('variants')
                            ->relationship('variants')
                            ->minItems(2)->defaultItems(2)
                            ->collapsible()->reorderable()->cloneable()
                            ->schema([
                                Grid::make()->schema([
                                    Group::make()->schema([
                                        TextInput::make('name')
                                            ->label(__('filament/resources/product-resource.form.variants.name.label'))
                                            ->placeholder(__('filament/resources/product-resource.form.variants.name.placeholder'))
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('sku')
                                            ->label(__('filament/resources/product-resource.form.variants.sku.label'))
                                            ->unique(ignoreRecord: true)
                                            ->required(),

                                        TextInput::make('buy_price')
                                            ->label(__('filament/resources/product-resource.form.variants.buy_price.label'))
                                            ->helperText(__('filament/resources/product-resource.form.variants.buy_price.helper_text'))
                                            ->live(onBlur: true)
                                            ->required(),

                                        TextInput::make('price')
                                            ->label(__('filament/resources/product-resource.form.variants.price.label'))
                                            ->placeholder(__('filament/resources/product-resource.form.variants.price.placeholder'))
                                            ->readOnly(fn (Get $get) => blank($get('buy_price')))
                                            ->numeric()->live(onBlur: true, debounce: 200)->required()
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                // this is auto change discount price
                                                self::getDiscountPrice($get, $set, true);
                                            })
                                            ->prefix(fn (Get $get) => blank($get('../../exchange_id')) ? '' : Exchange::where('id', $get('../../exchange_id'))->with('currency')->first()->currency->symbol_native),

                                        Group::make()->schema([
                                            TextInput::make('discount')
                                                ->label(__('filament/resources/product-resource.form.variants.discount.label'))
                                                ->numeric()->prefix('%')->integer()->maxValue(100)
                                                ->live(onBlur: true, debounce: 200)
                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                    // this is auto change discount price
                                                    self::getDiscountPrice($get, $set, true);
                                                })
                                                ->default(0),

                                            TextInput::make('discount_price')
                                                ->label(__('filament/resources/product-resource.form.variants.discount_price.label'))
                                                ->numeric()
                                                ->prefix(fn (Get $get) => blank($get('exchange_id')) ? '' : Exchange::where('id', $get('exchange_id'))->with('currency')->first()->currency->symbol_native)
                                                ->default(0.0000),

                                        ])->columns(2),

                                        TextInput::make('quantity')
                                            ->label(__('filament/resources/product-resource.form.variants.quantity.label'))
                                            ->placeholder(__('filament/resources/product-resource.form.variants.quantity.placeholder'))
                                            ->numeric()->required(),

                                        TextInput::make('MOQ')
                                            ->label(__('filament/resources/product-resource.form.variants.moq.label'))
                                            ->helperText(__('filament/resources/product-resource.form.variants.moq.helper_text'))
                                            ->default(1)
                                            ->numeric(),

                                    ]),

                                    Group::make()->schema([

                                        // this is myanmar section
                                        Section::make()
                                            ->description(__('filament/resources/product-resource.form.variants.mmk_section.description'))
                                            ->compact()
                                            ->schema([

                                                TextInput::make('mmk_buy_price')
                                                    ->label(__('filament/resources/product-resource.form.variants.mmk_section.buy_price'))
                                                    ->readOnly()->columnSpanFull(),

                                                TextInput::make('mmk_price')
                                                    ->label(__('filament/resources/product-resource.form.variants.mmk_section.price'))
                                                    ->readOnly()->columnSpanFull(),

                                                TextInput::make('mmk_discount_price')
                                                    ->label(__('filament/resources/product-resource.form.variants.mmk_section.discount_price'))
                                                    ->readOnly()->label('လျော့စျေး')->columnSpanFull(),

                                            ])->columns(2)->visible(function (Get $get) {
                                                if (! blank($get('../../exchange_id'))) {
                                                    $exchange = Exchange::where('id', $get('../../exchange_id'))->with('currency:id,code')->first();
                                                    if ($exchange->currency->code != 'MMK') {
                                                        return true;
                                                    }
                                                }

                                                return false;
                                            }),

                                        Repeater::make('attributes')
                                            ->label(__('filament/resources/product-resource.form.variants.attributes.label'))
                                            ->reorderableWithButtons()
                                            ->schema([

                                                Group::make()->schema([

                                                    Select::make('attribute_id')
                                                        ->label(__('filament/resources/product-resource.form.variants.attributes.attribute.label'))
                                                        ->placeholder(__('filament/resources/product-resource.form.variants.attributes.attribute.placeholder'))
                                                        ->live(onBlur: true, debounce: 200)
                                                        ->required()->searchable()->preload()->label('Attribute')->placeholder('Select')
                                                        ->afterStateUpdated(fn (Set $set) => $set('attribute_value_id', null))
                                                        ->options(function (Get $get) {
                                                            // ဒါက admin အတွက်စစ်တာပါ ဗျာ
                                                            if (Filament::getAuthGuard() == 'seller') {
                                                                return Attribute::where('seller_id', CheckFilamentSellerUser::getSellerId())->pluck('name', 'id');
                                                            }

                                                            return Attribute::where('seller_id', $get('../../../../seller_id'))->pluck('name', 'id');
                                                        }),

                                                    Select::make('attribute_value_id')
                                                        ->label(__('filament/resources/product-resource.form.variants.attributes.value.label'))
                                                        ->placeholder(__('filament/resources/product-resource.form.variants.attributes.value.placeholder'))
                                                        ->multiple()
                                                        ->hidden(fn (Get $get) => blank($get('attribute_id')))
                                                        ->required()->searchable()->preload()->label('Value')->placeholder('Select')
                                                        ->options(fn (Get $get) => AttributeValue::where('attribute_id', $get('attribute_id'))->pluck('value', 'id')),

                                                ])->columns(1),

                                            ])->columnSpanFull()->hidden(function (Get $get) {
                                                // ဒါက admin အတွက်စစ်တာပါ ဗျာ
                                                if (Filament::getAuthGuard() == 'admin') {
                                                    return blank($get('../../seller_id'));
                                                }

                                                return false;
                                            }),
                                    ])->columnSpan(1),

                                ])->columns(2),

                                SpatieMediaLibraryFileUpload::make('images')
                                    ->label(__('filament/resources/product-resource.form.variants.images.label'))
                                    ->helperText(__('filament/resources/product-resource.form.variants.images.helper_text'))
                                    ->collection('images')->imageEditor()->multiple()
                                    ->panelLayout('grid')
                                 //    ->helperText('Clone Action လုပ်ပါက images ကို နောက်ဆုံးမှ ထည့်ပေးပါရန် (သို့) edit မှာ clone ပေးပါရန်')
                                    ->rules('required')->columnSpanFull(),

                            ])
                            ->columns(3)->grid(2)
                            ->visible(fn (Get $get) => $get('has_variant') && ! blank($get('exchange_id'))),
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

    /**
     * @param  \Filament\Schemas\Components\Utilities\Get  $get
     * @param  \Filament\Schemas\Components\Utilities\Set  $set
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
            ->columns([
                TextColumn::make('category.name')->size('sm')
                    ->label(__('filament/resources/product-resource.table.columns.category'))
                    ->description(fn (Product $product) => $product->brand?->name)
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('filament/resources/product-resource.table.columns.name'))
                    ->description(fn (Product $product) => $product->sku)
                    ->searchable(),

                TextColumn::make('supplier.name')
                    ->label(__('filament/resources/product-resource.table.columns.supplier'))
                    ->searchable(),

                TextColumn::make('exchange.currency.code')
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

                IconColumn::make('is_public')
                    ->label(__('filament/resources/product-resource.table.columns.is_public'))
                    ->boolean(),

                IconColumn::make('is_recommended')
                    ->label(__('filament/resources/product-resource.table.columns.is_recommended'))
                    ->boolean(),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('brand_id')
                    ->label(__('filament/resources/product-resource.table.filters.brand'))
                    ->searchable()->preload()->label('Brand Name')
                    ->relationship('brand', 'name'),
                SelectFilter::make('category_id')
                    ->label(__('filament/resources/product-resource.table.filters.category'))
                    ->searchable()->preload()->label('Category Name')
                    ->relationship('category', 'name'),
                SelectFilter::make('supplier_id')
                    ->label(__('filament/resources/product-resource.table.filters.supplier'))
                    ->searchable()->preload()->label('Supplier Name')
                    ->relationship('supplier', 'name'),
                Filter::make('stock')
                    ->schema([
                        Toggle::make('out_stock')
                            ->label('Out Stock')
                            ->helperText('လက်ကျန်ပစွည်း ၁၀ ခုအောက်စစ်ရန်'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['out_stock'], function ($q) {
                            $q->where('stock', '<', 10);
                        });
                    }),

                Filter::make('exists_stock')
                    ->schema([
                        Toggle::make('exists_stocks')
                            ->label('Exists Stock')
                            ->helperText('လက်ကျန်ပစွည်း ကျန်ရှိသော ပစ္စည်းများ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['exists_stocks'], function ($q) {
                            $q->where('stock', '>', 0);
                        });
                    }),
                TrashedFilter::make(),

            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ReplicateAction::make()
                    ->modalWidth('sm')
                    ->schema([
                        TextInput::make('name'),
                        TextInput::make('sku'),
                        SpatieMediaLibraryFileUpload::make('images')->collection('images')->previewable(false)->multiple(),
                    ])
                    ->mountUsing(function (Schema $schema, Product $record) {
                        $schema->fill([
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
                EditAction::make(),
                ViewAction::make()->modalWidth(fn (Product $product) => $product->has_variant ? '5xl' : '4xl')->slideOver(),
            ])->defaultSort('id', 'desc')
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('variants')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
