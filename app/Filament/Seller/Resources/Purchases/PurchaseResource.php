<?php

namespace App\Filament\Seller\Resources\Purchases;

use App\Filament\Seller\Resources\Purchases\Pages\CreatePurchase;
use App\Filament\Seller\Resources\Purchases\Pages\EditPurchase;
use App\Filament\Seller\Resources\Purchases\Pages\ListPurchases;
use App\Helper\CheckFilamentSellerUser;
use App\Helper\PurchaseStatus;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Exchange;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $modelLabel = null;

    protected static ?string $pluralModelLabel = null;

    protected static ?string $navigationLabel = null;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static function getModelLabel(): string
    {
        return __('filament/resources/purchase-resource.navigation.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/purchase-resource.navigation.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/purchase-resource.navigation.label');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('seller_id', CheckFilamentSellerUser::getSellerId());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(4)->schema([

                    Group::make()->schema([

                        Repeater::make('products')
                            ->relationship('products')
                            ->collapsible()->reorderable(true)->reorderableWithButtons()
                            ->itemLabel(function (array $state): ?string {
                                $purchase = PurchaseProduct::where('product_id', $state['product_id'])->with('product')->first();
                                if ($purchase) {
                                    return $purchase->product->name.' - '.$purchase->stocks.' pcs - '.$purchase->buy_price;
                                }

                                return '';
                            })
                            ->schema([

                                Grid::make()->schema([

                                    Group::make()->schema([

                                        Select::make('product_id')
                                            ->label(__('filament/resources/purchase-resource.form.products.item.product'))
                                            ->autofocus(fn (Page $livewire) => $livewire instanceof CreateRecord)
                                            ->relationship(
                                                'product',
                                                'name',
                                                modifyQueryUsing: fn ($query) => $query->when(CheckFilamentSellerUser::getSellerId() != null, function ($q) {
                                                    $q->where('seller_id', CheckFilamentSellerUser::getSellerId());
                                                })->whereNull('deleted_at')
                                            )
                                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                                if ($state) {
                                                    $product = Product::where('id', $state)->with('exchange')->first();
                                                    $data = [];

                                                    if (! $product->has_variant) {
                                                        $set('buy_price', $product->buy_price);
                                                        $set('sell_price', $product->price);

                                                        foreach ($product->attributes as $att) {

                                                            $data[] = [
                                                                'attribute' => Attribute::where('id', $att['attribute_id'])->pluck('name') ?? '',
                                                                //                        'value' => implode(',', AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray()) ??  '',
                                                                'value' => AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray() ?? '',
                                                            ];

                                                        }
                                                        // dd($data);

                                                        $set('attributes', $data);
                                                    }
                                                } else {

                                                    $set('attributes', []);
                                                    $set('buy_price', 0);
                                                    $set('sell_price', 0);
                                                }

                                            })->live()->columnSpan(2)
                                            ->searchable()->preload()->required(),

                                        Select::make('variant_id')
                                            ->label(__('filament/resources/purchase-resource.form.products.item.variant'))
                                            ->required()
                                            ->hidden(fn (Get $get) => blank($get('product_id')) || ! Product::where('id', $get('product_id'))->select('has_variant')->first()->has_variant)
                                            ->searchable()->preload()->live()
                                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                                if ($state) {
                                                    $variant = Product::where('id', $get('product_id'))->first()->variants()->where('id', $state)->first();
                                                    $set('buy_price', $variant->buy_price);
                                                    $set('sell_price', $variant->price);

                                                    foreach ($variant->attributes as $att) {

                                                        $data[] = [
                                                            'attribute' => Attribute::where('id', $att['attribute_id'])->pluck('name') ?? '',
                                                            //                        'value' => implode(',', AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray()) ??  '',
                                                            'value' => AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray() ?? '',
                                                        ];

                                                    }
                                                    $set('attributes', $data);
                                                }
                                            })
                                            ->relationship('variant', 'sku', modifyQueryUsing: function (Get $get, $query) {
                                                return $query->where('product_id', $get('product_id'));
                                            })->columnSpan(2),

                                        TextInput::make('stocks')
                                            ->label(__('filament/resources/purchase-resource.form.products.item.stocks'))
                                            ->live(onBlur: true)
                                            ->integer()->required(),

                                        TextInput::make('buy_price')
                                            ->label(__('filament/resources/purchase-resource.form.products.item.buy_price'))
                                            ->live(onBlur: true)
                                            ->numeric()->required(),

                                        TextInput::make('sell_price')
                                            ->label(__('filament/resources/purchase-resource.form.products.item.sell_price'))
                                            ->live(onBlur: true)
                                            ->placeholder(function (Get $get, Set $set) {
                                                if (! blank($get('stocks')) && ! blank($get('buy_price'))) {
                                                    $set('total', $get('stocks') * $get('buy_price'));
                                                }
                                            })
                                            ->numeric()->required(),

                                        TextInput::make('total')->live(onBlur: true)->required()
                                            ->suffix(fn (Get $get) => blank($get('product_id')) ? '' : Product::where('id', $get('product_id'))->with('exchange.currency')->first()->exchange->currency->symbol_native),

                                    ])->columnSpan(4)->columns(columns: 4),

                                    Group::make()->schema([

                                        Repeater::make('attributes')
                                            ->collapsible(false)->reorderable(false)
                                            ->addable(false)->deletable(false)
                                            ->schema([

                                                Group::make()->schema([
                                                    TextInput::make('attribute')
                                                        ->label(__('filament/resources/purchase-resource.form.products.item.attributes.attribute'))
                                                        ->required()
                                                        ->label('Attribute'),

                                                    Select::make('value')
                                                        ->label(__('filament/resources/purchase-resource.form.products.item.attributes.value'))
                                                        ->placeholder(__('filament/resources/purchase-resource.form.products.item.attributes.select'))
                                                        ->multiple()
                                                        ->hiddenJs(<<<'JS'
                                                            !$get('attribute')
                                                        JS)
                                                        ->required()->searchable()->preload()->label('Value')->placeholder('Select')
                                                        //                                                           ->default(fn(Forms\Get $get) => AttributeValue::pluck('value','id'))
                                                        ->options(fn (Get $get) => AttributeValue::pluck('value', 'value')),

                                                ])->columns(columns: 1),

                                            ]),

                                    ])->columnSpan(2),

                                ])->columns(6),

                            ])->columnSpanFull(),

                    ])->columnSpan(3),

                    Group::make()->schema([

                        Select::make('supplier_id')
                            ->searchable()->preload()
                            ->relationship('supplier', 'name', modifyQueryUsing: fn ($query) => $query->when(CheckFilamentSellerUser::getSellerId() != null, function ($q) {
                                $q->where('seller_id', CheckFilamentSellerUser::getSellerId());
                            }))
                            ->label(__('filament/resources/purchase-resource.form.supplier_id'))
                            ->required(),

                        ToggleButtons::make('status')
                            ->inline()
                            ->required()->options(PurchaseStatus::class)
                            ->label(__('filament/resources/purchase-resource.form.status'))
                            ->default('processing'),

                        TextInput::make('total_amount')
                            ->placeholder(function (Get $get, Set $set) {
                                self::getTotalPrice($get, $set);
                            })
                            ->label(__('filament/resources/purchase-resource.form.total_amount'))
                            ->required()
                            ->numeric(),

                        TextInput::make('paid_amount')
                            ->label(__('filament/resources/purchase-resource.form.paid_amount'))
                            ->required()
                            ->numeric()
                            ->default(0.00),

                        TextInput::make('remaining_amount')
                            ->label(__('filament/resources/purchase-resource.form.remaining_amount'))
                            ->required()
                            ->numeric()
                            ->default(0.00),
                    ]),

                ])->columnSpanFull(),

            ]);
    }

    public static function getTotalPrice(Get $get, Set $set): void
    {
        $total = 0;
        if (! blank($get('products'))) {
            foreach ($get('products') as $product) {
                if (! blank($product['stocks']) && ! blank($product['buy_price']) && $product['stocks'] != 0 && $product['buy_price'] != 0) {
                    $total += $product['buy_price'] * $product['stocks'];
                }
            }
        }

        $set('total_amount', $total);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('seller.name')
                    ->label(__('filament/resources/purchase-resource.table.columns.seller'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label(__('filament/resources/purchase-resource.table.columns.supplier'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament/resources/purchase-resource.table.columns.status'))
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remaining_amount')
                    ->numeric()
                    ->sortable(),
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
                //
            ])
            ->recordActions([
                Action::make('Invoice')
                    ->label(__('filament/resources/purchase-resource.table.actions.invoice'))
                    ->modalContent(fn (Purchase $record) => view(
                        'livewire.purchase_invoice',
                        ['record' => $record]
                    ))
                    ->modalWidth('2xl')
                    ->color('success')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->icon('heroicon-o-viewfinder-circle'),

                Action::make('importStock')
                    ->label(__('filament/resources/purchase-resource.table.actions.import_stock'))
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Purchase $record) {
                        foreach ($record->products as $purchaseProduct) {
                            if ($purchaseProduct->variant_id) {
                                // Update variant stock and prices
                                $variant = $purchaseProduct->variant;
                                $variant->increment('quantity', $purchaseProduct->stocks);

                                // Update variant prices if buy price has changed
                                if ($variant->buy_price != $purchaseProduct->buy_price) {
                                    $exchange = $purchaseProduct->product->exchange;
                                    $updateData = [
                                        'buy_price' => $purchaseProduct->buy_price,
                                        'price' => $purchaseProduct->sell_price,
                                    ];

                                    // If currency is not MMK, calculate MMK prices
                                    if ($exchange && $exchange->currency->code !== 'MMK') {
                                        $mmkPrice = Exchange::where('seller_id', $exchange->seller_id)->whereRelation('currency', 'code', 'MMK')->first();
                                        $updateData['mmk_buy_price'] = $purchaseProduct->buy_price / $exchange->rate * $mmkPrice->rate;
                                        $updateData['mmk_price'] = $purchaseProduct->sell_price / $exchange->rate * $mmkPrice->rate;
                                        $updateData['mmk_discount_price'] = $variant->discount_price / $exchange->rate * $mmkPrice->rate;
                                    } else {

                                        $updateData['mmk_buy_price'] = $purchaseProduct->buy_price;
                                        $updateData['mmk_price'] = $purchaseProduct->sell_price;
                                        $updateData['mmk_discount_price'] = $variant->discount_price;
                                    }

                                    $variant->update($updateData);
                                }
                            } else {
                                // Update product stock and prices
                                $product = $purchaseProduct->product;
                                $product->increment('quantity', $purchaseProduct->stocks);

                                if ($product->buy_price != $purchaseProduct->buy_price) {
                                    $exchange = $product->exchange;
                                    $updateData = [
                                        'buy_price' => $purchaseProduct->buy_price,
                                        'price' => $purchaseProduct->sell_price,
                                    ];

                                    // If currency is not MMK, calculate MMK prices
                                    if ($exchange && $exchange->currency->code !== 'MMK') {
                                        $mmkPrice = Exchange::where('seller_id', $exchange->seller_id)->whereRelation('currency', 'code', 'MMK')->first();
                                        $updateData['mmk_buy_price'] = $purchaseProduct->buy_price / $exchange->rate * $mmkPrice->rate;
                                        $updateData['mmk_price'] = $purchaseProduct->sell_price / $exchange->rate * $mmkPrice->rate;
                                        $updateData['mmk_discount_price'] = $product->discount_price / $exchange->rate * $mmkPrice->rate;

                                    } else {
                                        // If currency is MMK, set MMK prices same as original prices
                                        $updateData['mmk_buy_price'] = $purchaseProduct->buy_price;
                                        $updateData['mmk_price'] = $purchaseProduct->sell_price;
                                        $updateData['mmk_discount_price'] = $product->discount_price;
                                    }

                                    $product->update($updateData);
                                }
                            }
                        }

                        // Update purchase status to completed
                        $record->update(['status' => PurchaseStatus::Complete]);

                        Notification::make()
                            ->title(__('filament/resources/purchase-resource.actions.import_stock_success'))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Purchase $record): bool => $record->status !== PurchaseStatus::Complete),

                EditAction::make()
                    ->label(__('filament/resources/purchase-resource.table.actions.edit')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('filament/resources/purchase-resource.table.actions.delete')),
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
            'index' => ListPurchases::route('/'),
            'create' => CreatePurchase::route('/create'),
            'edit' => EditPurchase::route('/{record}/edit'),
        ];
    }
}
