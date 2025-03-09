<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\PurchaseResource\Pages;
use App\Filament\Seller\Resources\PurchaseResource\RelationManagers;
use App\Helper\CheckFilamentSellerUser;
use App\Helper\PurchaseStatus;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Exchange;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\View;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $modelLabel = null;
    protected static ?string $pluralModelLabel = null;
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()->schema([

                    Forms\Components\Group::make()->schema([

                        Forms\Components\Repeater::make('products')
                            ->relationship('products')
                            ->collapsible()->reorderable(true)->reorderableWithButtons()
                            ->itemLabel(function (array $state): ?string {
                                $purchase = PurchaseProduct::where('product_id', $state['product_id'])->with('product')->first();
                                if ($purchase) {
                                    return $purchase->product->name . ' - ' . $purchase->stocks . ' pcs - ' . $purchase->buy_price;
                                }
                                return '';
                            })
                            ->schema([

                                Grid::make()->schema([

                                    Group::make()->schema([

                                        Forms\Components\Select::make('product_id')
                                            ->label(__('filament/resources/purchase-resource.form.products.item.product'))
                                            ->autofocus(fn(Page $livewire) => $livewire instanceof CreateRecord)
                                            ->relationship(
                                                'product',
                                                'name',
                                                modifyQueryUsing: fn($query) => $query->when(CheckFilamentSellerUser::getSellerId() != null, function ($q) {
                                                    $q->where('seller_id', CheckFilamentSellerUser::getSellerId());
                                                })->whereNull('deleted_at')
                                            )
                                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                                if ($state) {
                                                    $product = Product::where('id', $state)->with('exchange')->first();
                                                    $data = [];

                                                    if (!$product->has_variant) {
                                                        $set('buy_price', $product->buy_price);
                                                        $set('sell_price', $product->price);

                                                        foreach ($product->attributes as $att) {

                                                            $data[] = [
                                                                'attribute' => Attribute::where('id', $att['attribute_id'])->pluck('name') ?? '',
                                                                //                        'value' => implode(',', AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray()) ??  '',
                                                                'value' => AttributeValue::whereIn('id', $att['attribute_value_id'])->pluck('value')->toArray() ?? '',
                                                            ];

                                                        }

                                                        $set('attributes', $data);
                                                    }
                                                } else {

                                                    $set('attributes', []);
                                                    $set('buy_price', 0);
                                                    $set('sell_price', 0);
                                                }

                                            })->live()->columnSpan(2)
                                            ->searchable()->preload()->required(),

                                        Forms\Components\Select::make('variant_id')
                                            ->label(__('filament/resources/purchase-resource.form.products.item.variant'))
                                            ->required()
                                            ->hidden(fn(Forms\Get $get) => blank($get('product_id')) || !Product::where('id', $get('product_id'))->select('has_variant')->first()->has_variant)
                                            ->searchable()->preload()->live()
                                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
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
                                            ->relationship('variant', 'sku', modifyQueryUsing: function (Forms\Get $get, $query) {
                                                return $query->where('product_id', $get('product_id'));
                                            })->columnSpan(2),

                                        Forms\Components\TextInput::make('stocks')
                                            ->label(__('filament/resources/purchase-resource.form.products.item.stocks'))
                                            ->live(onBlur: true)
                                            ->integer()->required(),

                                        Forms\Components\TextInput::make('buy_price')
                                            ->label(__('filament/resources/purchase-resource.form.products.item.buy_price'))
                                            ->live(onBlur: true)
                                            ->numeric()->required(),

                                        Forms\Components\TextInput::make('sell_price')
                                            ->label(__('filament/resources/purchase-resource.form.products.item.sell_price'))
                                            ->live(onBlur: true)
                                            ->placeholder(function (Forms\Get $get, Forms\Set $set) {
                                                if (!blank($get('stocks')) && !blank($get('buy_price'))) {
                                                    $set('total', $get('stocks') * $get('buy_price'));
                                                }
                                            })
                                            ->numeric()->required(),

                                        Forms\Components\TextInput::make('total')->live(onBlur: true)->required()
                                            ->suffix(fn(Forms\Get $get) => blank($get('product_id')) ? '' : Product::where('id', $get('product_id'))->with('exchange.currency')->first()->exchange->currency->symbol_native)
                                            ,

                                    ])->columnSpan(4)->columns(columns: 4),

                                    Group::make()->schema([

                                     Forms\Components\Repeater::make('attributes')
                                        ->collapsible(false)->reorderable(false)
                                        ->addable(false)->deletable(false)
                                        ->schema([

                                            Forms\Components\Group::make()->schema([
                                                Forms\Components\Hidden::make('attribute')
                                                    ->label(__('filament/resources/purchase-resource.form.products.item.attributes.attribute'))
                                                    ->required()
                                                    ->label('Attribute'),

                                                Forms\Components\Select::make('value')
                                                    ->label(__('filament/resources/purchase-resource.form.products.item.attributes.value'))
                                                    ->placeholder(__('filament/resources/purchase-resource.form.products.item.attributes.select'))
                                                    ->multiple()
                                                    ->hidden(fn(Forms\Get $get) => blank($get('attribute')))
                                                    ->required()->searchable()->preload()->label('Value')->placeholder('Select')
                                                    //                                                           ->default(fn(Forms\Get $get) => AttributeValue::pluck('value','id'))
                                                    ->options(fn(Forms\Get $get) => AttributeValue::pluck('value', 'id')),

                                            ])->columns(columns: 1)

                                        ]),

                                    ])->columnSpan(2),

                                ])->columns(6),




                            ])->columns(columns: 6)->columnSpanFull(),

                    ])->columnSpan(3)->columns(2),

                    Forms\Components\Group::make()->schema([
                        Forms\Components\Section::make()->schema([

                            Forms\Components\Select::make('supplier_id')
                                ->searchable()->preload()
                                ->relationship('supplier', 'name', modifyQueryUsing: fn($query) => $query->when(CheckFilamentSellerUser::getSellerId() != null, function ($q) {
                                    $q->where('seller_id', CheckFilamentSellerUser::getSellerId());
                                }))
                                ->label(__('filament/resources/purchase-resource.form.supplier_id'))
                                ->required(),

                            Forms\Components\ToggleButtons::make('status')
                                ->inline()
                                ->required()->options(PurchaseStatus::class)
                                ->label(__('filament/resources/purchase-resource.form.status'))
                                ->default('processing'),

                            Forms\Components\TextInput::make('total_amount')
                                ->placeholder(function (Forms\Get $get, Forms\Set $set) {
                                    self::getTotalPrice($get, $set);
                                })
                                ->label(__('filament/resources/purchase-resource.form.total_amount'))
                                ->required()
                                ->numeric(),

                            Forms\Components\TextInput::make('paid_amount')
                                ->label(__('filament/resources/purchase-resource.form.paid_amount'))
                                ->required()
                                ->numeric()
                                ->default(0.00),

                            Forms\Components\TextInput::make('remaining_amount')
                                ->label(__('filament/resources/purchase-resource.form.remaining_amount'))
                                ->required()
                                ->numeric()
                                ->default(0.00),
                        ])
                    ])

                ])->columns(4)

            ]);
    }

    public static function getTotalPrice(Forms\Get $get, Forms\Set $set): void
    {
        $total = 0;
        if (!blank($get('products'))) {
            foreach ($get('products') as $product) {
                if (!blank($product['stocks']) && !blank($product['buy_price']) && $product['stocks'] != 0 && $product['buy_price'] != 0) {
                    $total += $product['buy_price'] * $product['stocks'];
                }
            }
        }

        $set('total_amount', $total);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->label(__('filament/resources/purchase-resource.table.columns.seller'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label(__('filament/resources/purchase-resource.table.columns.supplier'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament/resources/purchase-resource.table.columns.status'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->numeric()
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
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Invoice')
                    ->label(__('filament/resources/purchase-resource.table.actions.invoice'))
                    ->modalContent(fn(Purchase $record) => view(
                        'livewire.purchase_invoice',
                        ['record' => $record]
                    ))
                    ->modalWidth('2xl')
                    ->color('success')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->icon('heroicon-o-viewfinder-circle'),

                Tables\Actions\Action::make('importStock')
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
                    ->visible(fn(Purchase $record): bool => $record->status !== PurchaseStatus::Complete),

                Tables\Actions\EditAction::make()
                    ->label(__('filament/resources/purchase-resource.table.actions.edit')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'edit' => Pages\EditPurchase::route('/{record}/edit'),
        ];
    }
}
