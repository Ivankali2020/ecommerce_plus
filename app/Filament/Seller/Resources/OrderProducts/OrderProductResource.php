<?php

namespace App\Filament\Seller\Resources\OrderProducts;

use App\Filament\Seller\Resources\OrderProductResource\Pages;
use App\Filament\Seller\Resources\OrderProducts\Pages\ListOrderProducts;
use App\Helper\CheckFilamentSellerUser;
use App\Models\OrderProduct;
use App\Services\ReturnService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderProductResource extends Resource
{
    protected static ?string $model = OrderProduct::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Order Section';

    public static function updateGrandTotal($livewire): void
    {
        $returnItems = $livewire->data['return_items'] ?? [];
        $grandTotal = 0;

        foreach ($returnItems as $item) {
            $grandTotal += $item['total_refund_amount'] ?? 0;
        }

        $livewire->data['total_refund_amount'] = $grandTotal;
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/resources/order-product-resource.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/resources/order-product-resource.navigation.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/resources/order-product-resource.navigation.plural');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    //    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['order', 'product', 'returnProducts']))
            ->groups([
                'order.invoice_number',
                'product.name',
                Group::make('created_at')
                    ->label(__('filament/resources/order-product-resource.columns.created_at'))
                    ->date(),
            ])
            ->defaultGroup('order.invoice_number')
            ->groupingSettingsInDropdownOnDesktop()
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('filament/resources/order-product-resource.columns.product.name'))
                    ->searchable()
                    ->description(fn (OrderProduct $orderProduct) => $orderProduct->product->category->name),

                TextColumn::make('attributes')
                    ->label(__('filament/resources/order-product-resource.columns.attributes'))
                    ->getStateUsing(function ($record) {
                        $data = '';
                        foreach ($record->attributes as $attribute) {
                            $array = implode(' - ', collect($attribute['value'] ?? [])->toArray());
                            $data .= "<span class='border text-xs text-orange-500 dark:text-orange-400 border-gray-500 dark:border-gray-700 py-1  px-2 rounded '>".$array.' </span>';
                        }

                        return $data;
                    })->color('orange')
                    ->html()
                    ->formatStateUsing(fn ($state) => $state),

                TextColumn::make('original_buy_price')
                    ->label(__('filament/resources/order-product-resource.columns.original_buy_price'))
                    ->alignEnd(),
                // ->description(fn(OrderProduct $orderProduct) => $orderProduct->original_price),

                TextColumn::make('price')
                    ->label(__('filament/resources/order-product-resource.columns.selling_price'))
                    ->alignEnd(),

                TextColumn::make('discount_price')
                    ->label(__('filament/resources/order-product-resource.columns.discount_price'))
                    ->alignEnd()
                    ->description(fn (OrderProduct $orderProduct) => $orderProduct->discount.' %'),

                TextColumn::make('quantity')
                    ->label(__('filament/resources/order-product-resource.columns.quantity'))
                    ->alignCenter(),

                TextColumn::make('sub_price')
                    ->label(__('filament/resources/order-product-resource.columns.sub_price'))
                    ->alignEnd()
                    ->summarize(
                        Sum::make('sub_price')
                            ->money('mmk')
                            ->label(__('filament/resources/order-product-resource.columns.total_amount'))
                    ),

                TextColumn::make('profit')
                    ->label(__('filament/resources/order-product-resource.columns.profit_loss'))
                    ->alignEnd()
                    ->summarize(
                        Sum::make('profit')
                            ->money('mmk')
                            ->label(__('filament/resources/order-product-resource.columns.profit_loss'))
                    ),

                TextColumn::make('order.status')
                    ->label('Order Status'),

                TextColumn::make('is_returned')
                    ->label('Return Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state, OrderProduct $record): string => $state
                            ? "Returned ({$record->returned_quantity}/{$record->quantity})"
                            : ($record->canDoRet() ? 'Returnable' : 'Not Returnable')
                    )
                    ->color(fn (bool $state, OrderProduct $record): string => $state
                            ? 'success'
                            : ($record->canDoRet() ? 'warning' : 'gray')
                    ),

                TextColumn::make('return_eligible')
                    ->label('Return Eligible')
                    ->badge()
                    ->getStateUsing(fn (OrderProduct $record): string => $record->order->canDoRet() ? 'Yes' : 'No'
                    )
                    ->color(fn (string $state): string => $state == 'Yes' ? 'success' : 'danger'
                    ),
            ])
            ->defaultSort('id', 'desc')
            ->filters([

                Filter::make('created_at')
                    ->schema([
                        Flex::make([
                            DatePicker::make('created_from')
                                ->label(__('filament/resources/order-product-resource.filters.created_from')),
                            DatePicker::make('created_until')
                                ->label(__('filament/resources/order-product-resource.filters.created_until')),
                        ])->columns(2)->columnSpanFull(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_products.created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_products.created_at', '<=', $date),
                            );
                    })->columnSpan(2),

                SelectFilter::make('product_id')
                    ->label(__('filament/resources/order-product-resource.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query) => $query->whereDate('order_products.created_at', today()))
                    ->indicator('Filter by today\'s date')
                    ->toggle(),

                // add search order invoice number
                SelectFilter::make('order_id')
                    ->label(__('filament/resources/order-product-resource.filters.order'))
                    ->relationship('order', 'invoice_number')
                    ->searchable()
                    ->preload()
                    ->multiple(),

            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()
                    ->label(__('filament/resources/order-product-resource.actions.edit')),

                Action::make('return')
                    ->label('Return Product')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('warning')
                    ->modalWidth(Width::FiveExtraLarge)
                    ->modalHeading(fn (OrderProduct $record): string => $record->product->name)
                    ->visible(fn (OrderProduct $record): bool => $record->order->canDoRet() &&
                        $record->canDoRet() &&
                        ! $record->order->hasActiveReturn()
                    )
                    ->schema([

                        Section::make('Admin Controls')
                            ->columns(3)->compact()
                            ->schema([

                                TextInput::make('quantity')
                                    ->label('Quantity to Return')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->afterStateUpdated(function ($get, $set, $state) {
                                        $set('total_refund_amount', $state * $get('unit_refund_amount'));
                                    })
                                    ->live(onBlur: true)
                                    ->maxValue(fn (OrderProduct $record): int => $record->getAvailableReturnQuantity())
                                    ->default(fn (OrderProduct $record): int => $record->getAvailableReturnQuantity()),

                                TextInput::make('unit_refund_amount')
                                    ->label('Unit Refund Amount')
                                    ->numeric()
                                    ->prefix('MMK')
                                    ->required()
                                    ->minValue(0)
                                    ->default(fn (OrderProduct $record): int => $record->discount > 0 ? $record->discount_price : $record->price),

                                TextInput::make('total_refund_amount')
                                    ->label('Total Refund Amount')
                                    ->numeric()
                                    ->prefix('MMK')
                                    ->default(fn ($get) => $get('unit_refund_amount') * $get('quantity'))
                                    ->placeholder('Enter refund amount...'),

                                Toggle::make('should_restock')
                                    ->label('Should Restock')
                                    ->helperText('Enable to automatically increment stock when return is completed')
                                    ->default(false)
                                    ->reactive(),

                                Textarea::make('reason')
                                    ->label('Reason for Return')
                                    ->required()
                                    ->placeholder('Please provide a reason for this return...')
                                    ->maxLength(500),

                                Select::make('restocking_decision')
                                    ->label('Restocking Decision')
                                    ->options([
                                        'restock' => 'Restock - Item is in good condition',
                                        'damage' => 'Damaged - Cannot be resold',
                                        'wrong_product' => 'Wrong Product - Customer error',
                                        'other' => 'Other - See notes',
                                    ])
                                    ->placeholder('Select restocking decision...')
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, $state) {
                                        // Auto-set should_restock based on decision
                                        $set('should_restock', $state === 'restock');
                                    }),

                                Textarea::make('admin_notes')
                                    ->label('Admin Notes')
                                    ->placeholder('Internal notes for this return...')
                                    ->rows(3)
                                    ->maxLength(1000),

                                FileUpload::make('payment_slip')
                                    ->label('Payment Slip')
                                    ->image()
                                    ->directory('return-payment-slips')
                                    ->visibility('private')
                                    ->helperText('Upload payment slip for refund processing'),

                                Toggle::make('auto_complete')
                                    ->label('Auto Complete Return')
                                    ->helperText('Complete the return immediately after creation')
                                    ->default(true),
                            ])
                            ->collapsible()
                            ->collapsed(false),
                    ])
                    ->action(function (OrderProduct $record, array $data): void {
                        try {
                            $returnService = app(ReturnService::class);

                            $returnItems = [[
                                'order_product_id' => $record->id,
                                'quantity' => $data['quantity'],
                                'reason' => $data['reason'],
                                'unit_refund_amount' => $data['unit_refund_amount'],
                                'total_refund_amount' => $data['total_refund_amount'],
                            ]];

                            // Prepare admin controls
                            $adminControls = [];
                            if (isset($data['restocking_decision'])) {
                                $adminControls['restocking_decision'] = $data['restocking_decision'];
                            }
                            if (isset($data['should_restock'])) {
                                $adminControls['should_restock'] = $data['should_restock'];
                            }
                            if (isset($data['admin_notes'])) {
                                $adminControls['admin_notes'] = $data['admin_notes'];
                            }
                            if (isset($data['total_refund_amount'])) {
                                $adminControls['total_refund_amount'] = $data['total_refund_amount'];
                            }
                            if (isset($data['payment_slip'])) {
                                $adminControls['payment_slip'] = $data['payment_slip'];
                            }
                            if (isset($data['auto_complete'])) {
                                $adminControls['auto_complete'] = $data['auto_complete'];
                            }

                            $orderReturn = $returnService->createReturn(
                                $record->order,
                                "Return request for product: {$record->product->name}",
                                $returnItems,
                                auth()->id(),
                                $adminControls
                            );

                            // If auto_complete is requested, use instant complete method
                            if (isset($data['auto_complete']) && $data['auto_complete']) {

                                Notification::make()
                                    ->title('Return Completed Successfully')
                                    ->body('Return request has been completed instantly. '.
                                           ($adminControls['should_restock'] ?? false ? 'Stock has been updated.' : 'No stock changes made.'))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Return Request Submitted')
                                    ->body("Return request #{$orderReturn->invoice_number} has been created successfully.")
                                    ->success()
                                    ->send();
                            }

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error Creating Return')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalSubmitActionLabel('Submit Return Request'),
            ])
            // ->filtersFormWidth(MaxWidth::Full)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('filament/resources/order-product-resource.actions.delete')),

                    BulkAction::make('bulk_return')
                        ->label('Return Selected Products')
                        ->icon('heroicon-m-arrow-uturn-left')
                        ->color('warning')
                        ->deselectRecordsAfterCompletion()
                        ->modalWidth(Width::FiveExtraLarge)
                        ->schema([
                            Textarea::make('general_reason')
                                ->label('General Return Reason')
                                ->required()
                                ->placeholder('Please provide a general reason for these returns...')
                                ->maxLength(500),

                            Repeater::make('return_items')
                                ->label('Products to Return')
                                ->afterStateUpdated(function ($state, $set) {
                                    // Recalculate total when items are added/removed
                                    $total = collect($state ?? [])->sum('total_refund_amount');
                                    $set('total_refund_amount', $total);
                                })
                                ->live()
                                ->schema([
                                    TextInput::make('product_name')
                                        ->label('Product')
                                        ->disabled(),

                                    TextInput::make('quantity')
                                        ->label('Quantity to Return')
                                        ->numeric()
                                        ->required()
                                        ->minValue(1)
                                        ->afterStateUpdated(function ($get, $set, $state, $livewire) {
                                            $unitAmount = $get('unit_refund_amount') ?? 0;
                                            $set('total_refund_amount', $state * $unitAmount);

                                            // static::updateGrandTotal($livewire);
                                        })
                                        ->live(onBlur: true),

                                    TextInput::make('unit_refund_amount')
                                        ->label('Unit Refund Amount')
                                        ->numeric()
                                        ->prefix('MMK')
                                        ->required()
                                        ->minValue(0)
                                        ->afterStateUpdated(function ($get, $set, $state, $livewire) {
                                            $quantity = $get('quantity') ?? 0;
                                            $set('total_refund_amount', $state * $quantity);

                                            // Update grand total
                                            // static::updateGrandTotal($livewire);
                                        })
                                        ->live(onBlur: true),

                                    TextInput::make('total_refund_amount')
                                        ->label('Total Refund Amount')
                                        ->numeric()
                                        ->prefix('MMK')
                                        ->disabled()
                                        ->default(fn ($get) => ($get('unit_refund_amount') ?? 0) * ($get('quantity') ?? 0)),

                                    Textarea::make('individual_reason')
                                        ->label('Specific Reason (Optional)')
                                        ->placeholder('Specific reason for this product...')
                                        ->columnSpanFull()
                                        ->maxLength(300),

                                    Toggle::make('is_restocked')
                                        ->label('Is Restocked')
                                        ->default(true)
                                        ->helperText('Enable to restock this item'),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->grid(2)
                                ->columnSpanFull(),

                            Section::make('Admin Controls')
                                ->description('Administrative controls for bulk return processing')
                                ->columns(2)
                                ->compact()
                                ->schema([
                                    Select::make('restocking_decision')
                                        ->label('Restocking Decision')
                                        ->options([
                                            'restock' => 'Restock - Items are in good condition',
                                            'damage' => 'Damaged - Cannot be resold',
                                            'wrong_product' => 'Wrong Product - Customer error',
                                            'other' => 'Other - See notes',
                                        ])
                                        ->placeholder('Select restocking decision...')
                                        ->reactive()
                                        ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                            $shouldRestock = $state === 'restock';
                                            $set('should_restock', $shouldRestock);

                                            // Update all is_restocked toggles in the repeater
                                            $returnItems = $get('return_items') ?? [];
                                            foreach ($returnItems as $index => $item) {
                                                $set("return_items.{$index}.is_restocked", $shouldRestock);
                                            }
                                        }),

                                    Toggle::make('should_restock')
                                        ->label('Should Restock')
                                        ->default(true)
                                        ->helperText('Enable to automatically increment stock for returned items')
                                        ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                            // Update all is_restocked toggles in the repeater
                                            $returnItems = $get('return_items') ?? [];
                                            foreach ($returnItems as $index => $item) {
                                                $set("return_items.{$index}.is_restocked", $state);
                                            }
                                        })
                                        ->live(),

                                    Textarea::make('admin_notes')
                                        ->label('Admin Notes')
                                        ->placeholder('Administrative notes for this bulk return...')
                                        ->maxLength(1000)
                                        ->columnSpanFull(),

                                    TextInput::make('total_refund_amount')
                                        ->label('Total Refund Amount')
                                        ->numeric()
                                        ->prefix('MMK')
                                        ->disabled()
                                        ->placeholder('Auto-calculated from items above')
                                        ->helperText('This is automatically calculated from the repeater items'),

                                    FileUpload::make('payment_slip')
                                        ->label('Payment Slip')
                                        ->acceptedFileTypes(['pdf', 'jpg', 'jpeg', 'png'])
                                        ->maxSize(5120),

                                    Toggle::make('auto_complete')
                                        ->default(true)
                                        ->label('Auto Complete Return')
                                        ->helperText('Enable to complete the return instantly')
                                        ->columnSpanFull(),
                                ])
                                ->collapsible()
                                ->collapsed(false),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            try {
                                // Validate all records belong to the same order
                                $orderIds = $records->pluck('order_id')->unique();
                                if ($orderIds->count() > 1) {
                                    throw new \InvalidArgumentException('All selected products must belong to the same order.');
                                }

                                $order = $records->first()->order;

                                // Validate order can be returned
                                if (! $order->canDoRet()) {
                                    throw new \InvalidArgumentException('This order is not eligible for returns.');
                                }

                                if ($order->hasActiveReturn()) {
                                    throw new \InvalidArgumentException('This order already has an active return request.');
                                }

                                // Validate all products can be returned
                                foreach ($records as $record) {
                                    if (! $record->canDoRet()) {
                                        throw new \InvalidArgumentException("Product '{$record->product->name}' cannot be returned.");
                                    }
                                }

                                $returnService = app(ReturnService::class);

                                $returnItems = [];
                                $calculatedTotalRefund = 0;

                                foreach ($data['return_items'] as $index => $item) {
                                    $record = $records->skip($index)->first();

                                    if ($item['quantity'] > $record->getAvailableReturnQuantity()) {
                                        throw new \InvalidArgumentException("Quantity for '{$record->product->name}' exceeds available return quantity.");
                                    }

                                    // Calculate total refund amount from each item
                                    $itemTotal = $item['total_refund_amount'] ?? ($item['unit_refund_amount'] * $item['quantity']);
                                    $calculatedTotalRefund += $itemTotal;

                                    $returnItems[] = [
                                        'order_product_id' => $record->id,
                                        'quantity' => $item['quantity'],
                                        'unit_refund_amount' => $item['unit_refund_amount'],
                                        'total_refund_amount' => $itemTotal,
                                        'reason' => $item['individual_reason'] ?: $data['general_reason'],
                                        'is_restocked' => $item['is_restocked'] ?? false,
                                    ];
                                }

                                // Prepare admin controls
                                $adminControls = [];
                                if (isset($data['restocking_decision'])) {
                                    $adminControls['restocking_decision'] = $data['restocking_decision'];
                                }
                                if (isset($data['should_restock'])) {
                                    $adminControls['should_restock'] = $data['should_restock'];
                                }
                                if (isset($data['admin_notes'])) {
                                    $adminControls['admin_notes'] = $data['admin_notes'];
                                }
                                // Use calculated total refund amount instead of form input
                                $adminControls['total_refund_amount'] = $calculatedTotalRefund;

                                if (isset($data['payment_slip'])) {
                                    $adminControls['payment_slip'] = $data['payment_slip'];
                                }
                                if (isset($data['auto_complete'])) {
                                    $adminControls['auto_complete'] = $data['auto_complete'];
                                }

                                $orderReturn = $returnService->createReturn(
                                    $order,
                                    $data['general_reason'],
                                    $returnItems,
                                    auth()->id(),
                                    $adminControls
                                );

                                // If auto_complete is requested, use instant complete method
                                if (isset($data['auto_complete']) && $data['auto_complete']) {
                                    // $returnService->instantComplete($orderReturn, $adminControls);

                                    Notification::make()
                                        ->title('Bulk Return Completed Successfully')
                                        ->body("Bulk return request has been completed instantly for {$records->count()} products. ".
                                               ($adminControls['should_restock'] ?? false ? 'Stock has been updated.' : 'No stock changes made.'))
                                        ->success()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->title('Bulk Return Request Created')
                                        ->body("Return request #{$orderReturn->invoice_number} has been submitted for {$records->count()} products.")
                                        ->success()
                                        ->send();
                                }

                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error Creating Bulk Return')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->fillForm(function (Collection $records): array {
                            // Pre-fill the form with selected products
                            $returnItems = [];
                            $grandTotal = 0;

                            foreach ($records as $record) {
                                $unitRefund = $record->discount > 0 ? $record->discount_price : $record->price;
                                $quantity = $record->getAvailableReturnQuantity();
                                $itemTotal = $unitRefund * $quantity;
                                $grandTotal += $itemTotal;

                                $returnItems[] = [
                                    'product_name' => $record->product->name,
                                    'unit_refund_amount' => $unitRefund,
                                    'total_refund_amount' => $itemTotal,
                                    'quantity' => $quantity,
                                    'individual_reason' => '',
                                    'is_restocked' => true, // Default to true, will be controlled by should_restock
                                ];
                            }

                            return [
                                'return_items' => $returnItems,
                                'total_refund_amount' => $grandTotal,
                                'should_restock' => true, // Default value
                            ];
                        })
                        ->modalSubmitActionLabel('Submit Bulk Return Request'),
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
            'index' => ListOrderProducts::route('/'),
            //            'create' => Pages\CreateOrderProduct::route('/create'),
            //            'edit' => Pages\EditOrderProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereRelation('order', 'seller_id', CheckFilamentSellerUser::getSellerId())
            ->withoutGlobalScopes([
                //                SoftDeletingScope::class,
            ]);
    }
}
