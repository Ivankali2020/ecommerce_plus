<?php

namespace App\Filament\Seller\Resources\OrderReturns\Schemas;

use App\Helper\CheckFilamentSellerUser;
use App\Helper\ReturnStatus;
use App\Models\Order;
use App\Models\OrderProduct;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class OrderReturnForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Grid::make(2)->schema([

                    Group::make()->schema([

                        Select::make('order_id')
                            ->relationship('order', 'invoice_number')
                            ->label(__('filament/resources/order-return-resource.form.order_info.order_number'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $set('invoice_number', 'RET_' . $state);

                                $order = Order::find($state);
                                // update user
                                if ($order && $order->user_id != null) {
                                    $set('user_id', $order->user_id);
                                } else {
                                    $set('user_id', null);
                                }
                            })
                            ->preload()->searchable(),

                        TextInput::make('invoice_number')
                            ->label(__('filament/resources/order-return-resource.form.order_info.return_number'))
                            ->required(),

                        Select::make('user_id')
                            ->label(__('filament/resources/order-return-resource.form.order_info.customer'))
                            ->hiddenJs(<<<'JS'
                                $get('order_id') == null
                            JS)
                            ->disabled()
                            ->relationship('user', 'name')->preload()->searchable(),

                        Select::make('status')
                            ->label(__('filament/resources/order-return-resource.form.order_info.status.label'))
                            ->options(ReturnStatus::class)
                            ->default(ReturnStatus::Completed)
                            ->preload()->searchable()
                            ->required(),

                        Textarea::make('reason')
                            ->label(__('filament/resources/order-return-resource.form.order_info.return_reason'))
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('admin_notes')
                            ->label(__('filament/resources/order-return-resource.form.order_info.admin_notes'))
                            ->columnSpanFull(),

                        Select::make('restocking_decision')
                            ->label(__('filament/resources/order-return-resource.form.order_info.restocking_decision.label'))
                            ->options([
                                'restock' => __('filament/resources/order-return-resource.form.order_info.restocking_decision.options.restock'),
                                'damage' => __('filament/resources/order-return-resource.form.order_info.restocking_decision.options.damage'),
                                'wrong_product' => __('filament/resources/order-return-resource.form.order_info.restocking_decision.options.wrong_product'),
                                'other' => __('filament/resources/order-return-resource.form.order_info.restocking_decision.options.other'),
                            ]),

                        FileUpload::make('payment_slip')
                            ->label(__('filament/resources/order-return-resource.form.order_info.payment_slip'))
                            ->columnSpanFull(),

                        DateTimePicker::make('requested_at')
                            ->label(__('filament/resources/order-return-resource.form.order_info.requested_at'))
                            ->required()->default(now()),
                        // Hidden fields
                        Hidden::make('seller_id')
                            ->default(CheckFilamentSellerUser::getSellerId()),
                        Hidden::make('tenant_id')
                            ->default(tenant('id')),

                    ])->columns(2),

                    Group::make()->schema([

                        TextInput::make('total_refund_amount')
                            ->label(__('filament/resources/order-return-resource.form.order_info.total_refund_amount'))
                            ->required()
                            ->numeric()
                            ->default(0.0)
                            ->reactive()
                            ->suffix(__('filament/resources/order-return-resource.form.order_info.total_refund_amount_suffix'))
                            ->afterStateHydrated(function ($component, $state, $get) {
                                // Calculate total from repeater items when form loads
                                $returnProducts = $get('returnProducts') ?? [];
                                $total = collect($returnProducts)->sum('total_refund_amount');
                                $component->state($total);
                            }),

                        Repeater::make('returnProducts')
                            ->relationship('returnProducts')
                            ->label(__('filament/resources/order-return-resource.form.return_products.label'))
                            ->afterStateUpdated(function ($state, $set) {
                                // Recalculate total when items are added/removed
                                $total = collect($state ?? [])->sum('total_refund_amount');
                                $set('../total_refund_amount', $total);
                            })
                            ->live()
                            ->schema([
                                Select::make('order_product_id')
                                    ->relationship(name: 'orderProduct', modifyQueryUsing: function ($query, $get) {
                                        return $query
                                            //  ->where('is_returned',false)
                                            ->whereHas('product', fn($query) => $query->where('is_return', true))
                                            ->whereHas('order', fn($query) => $query->where('id', $get('../../order_id')));
                                    })
                                    ->label(__('filament/resources/order-return-resource.form.return_products.order_product'))
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(fn(Model $record) => $record->product?->name ?? 'Product not found')
                                    ->searchable()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        $orderProduct = OrderProduct::find($state);
                                        $set('quantity_requested', 0);
                                        $set('unit_refund_amount', 0);
                                        if (! $orderProduct) {
                                            return;
                                        }

                                        // set quantity_requested
                                        $set('quantity_requested', $orderProduct->getAvailableReturnQuantity());

                                        if ($orderProduct->discount > 0) {
                                            $set('unit_refund_amount', $orderProduct->discount_price);
                                        } else {
                                            $set('unit_refund_amount', $orderProduct->price);
                                        }
                                    })
                                    ->required(),

                                TextInput::make('quantity_requested')
                                    ->label(__('filament/resources/order-return-resource.form.return_products.quantity_requested'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(1),

                                TextInput::make('quantity_approved')
                                    ->label(__('filament/resources/order-return-resource.form.return_products.quantity_approved'))
                                    ->numeric()
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        // Calculate this item's total
                                        $itemTotal = $state * $get('unit_refund_amount');
                                        $set('total_refund_amount', $itemTotal);

                                        // Update the main total_refund_amount
                                        $returnProducts = $get('../../returnProducts') ?? [];
                                        $grandTotal = collect($returnProducts)->sum('total_refund_amount');
                                        $set('../../total_refund_amount', $grandTotal);
                                    })
                                    ->live(onBlur: true)
                                    ->minValue(0)
                                    ->maxValue(fn($get) => $get('quantity_requested'))
                                    ->default(0),

                                TextInput::make('unit_refund_amount')
                                    ->label(__('filament/resources/order-return-resource.form.return_products.unit_refund_amount'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.0)
                                    ->default(0.0)
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        // Calculate this item's total
                                        $itemTotal = $state * $get('quantity_approved');
                                        $set('total_refund_amount', $itemTotal);

                                        // Update the main total_refund_amount
                                        $returnProducts = $get('../../returnProducts') ?? [];
                                        $grandTotal = collect($returnProducts)->sum('total_refund_amount');
                                        $set('../../total_refund_amount', $grandTotal);
                                    })
                                    ->live(onBlur: true),

                                TextInput::make('total_refund_amount')
                                    ->label(__('filament/resources/order-return-resource.form.return_products.total_refund_amount'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.0)
                                    ->default(0.0)
                                    ->readonly()
                                    ->afterStateUpdated(function ($state, $get, $set) {
                                        // Update the main total_refund_amount when any item changes
                                        $returnProducts = $get('../../returnProducts') ?? [];
                                        $total = collect($returnProducts)->sum('total_refund_amount');
                                        $set('../../total_refund_amount', $total);
                                    })
                                    ->live(onBlur: true),

                                Toggle::make('is_restocked')
                                    ->label(__('filament/resources/order-return-resource.form.return_products.is_restocked'))
                                    ->helperText(__('filament/resources/order-return-resource.form.return_products.helper_text'))
                                    ->required(),

                            ])
                            ->hiddenJs(<<<'JS'
                                $get('order_id') == null
                            JS)
                            ->columns(2)
                            ->minItems(1)
                            ->columnSpanFull()
                            ->required(),

                    ]),

                ])->columnSpanFull(),

            ]);
    }
}
