<?php

namespace App\Filament\Seller\Resources\OrderProducts\Pages;

use App\Exports\EnhancedOrderProductExport;
use App\Filament\Seller\Resources\OrderProducts\OrderProductResource;
use App\Helper\OrderStatus;
use App\Models\Expanse;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class ListOrderProducts extends ListRecords
{
    protected static string $resource = OrderProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview Data')
                ->icon('heroicon-o-eye')
                ->modal()
                ->modalHeading('Order Products Preview')
                ->modalDescription('Preview of filtered order products with summary')
                ->modalWidth('7xl')
                ->modalContent(function ($livewire) {
                    $filters = $livewire->tableFilters ?? [];
                    $activeTab = $livewire->activeTab ?? 'all';

                    return new HtmlString($this->getPreviewContent($filters, $activeTab)->render());
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->color('primary'),

            Action::make('export')
                ->label('Export Filtered')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function ($livewire) {
                    $filters = $livewire->tableFilters ?? [];
                    $activeTab = $livewire->activeTab ?? 'all';

                    // First, check the total count to decide which export method to use
                    $countQuery = \App\Models\OrderProduct::query();

                    // Apply tab filter based on order status
                    if ($activeTab !== 'all') {
                        $statusMap = [
                            'new' => OrderStatus::New,
                            'processing' => OrderStatus::Processing,
                            'shipping' => OrderStatus::Shipped,
                            'delivered' => OrderStatus::Delivered,
                            'cancel' => OrderStatus::Cancelled,
                            'refunded' => OrderStatus::Refunded,
                        ];

                        if (isset($statusMap[$activeTab])) {
                            $countQuery->whereRelation('order', 'status', $statusMap[$activeTab]);
                        }
                    }

                    // Apply filters to count query
                    if (! empty($filters['created_at']['From'])) {
                        $countQuery->where('created_at', '>=', $filters['created_at']['From']);
                    }
                    if (! empty($filters['created_at']['To'])) {
                        $countQuery->where('created_at', '<=', $filters['created_at']['To']);
                    }

                    // Apply Today filter if it's set
                    if ($filters['today']['isActive']) {
                        $today = now()->toDateString();
                        $countQuery->whereDate('created_at', $today);
                    }

                    // Apply Product Id values filter if it's set
                    if (! empty($filters['product_id']['values'])) {
                        $productIds = $filters['product_id']['values'];
                        $countQuery->whereIn('product_id', $productIds);
                    }

                    // Apply Order Id values filter if it's set
                    if (! empty($filters['order_id']['values'])) {
                        $orderIds = $filters['order_id']['values'];
                        $countQuery->whereIn('order_id', $orderIds);
                    }

                    $totalRecords = $countQuery->count();

                    // Generate dynamic filename based on filters
                    $filename = 'order_products';

                    if (! empty($filters['created_at']['created_from']) || ! empty($filters['created_at']['created_until'])) {
                        $fromDate = ! empty($filters['created_at']['created_from'])
                            ? \Carbon\Carbon::parse($filters['created_at']['created_from'])->format('Y-m-d')
                            : 'start';
                        $toDate = ! empty($filters['created_at']['created_until'])
                            ? \Carbon\Carbon::parse($filters['created_at']['created_until'])->format('Y-m-d')
                            : 'end';

                        $filename .= "_{$fromDate}_to_{$toDate}";
                    } else {
                        $filename .= '_all_records';
                    }

                    $filename .= '_'.now()->format('Y-m-d_H-i-s').'.xlsx';

                    // Use different export strategy based on data size
                    if ($totalRecords > 5000) {
                        // For large datasets, use memory-efficient database aggregation
                        // return Excel::download(
                        //     new EnhancedLargeOrderProductExport($filters, $activeTab),
                        //     $filename
                        // );
                    } else {
                        // For smaller datasets, use the collection-based approach
                        $query = \App\Models\OrderProduct::query()
                            ->with('product:id,name'); // Only load needed product fields

                        // Apply tab filter based on order status
                        if ($activeTab !== 'all') {
                            $statusMap = [
                                'new' => OrderStatus::New,
                                'processing' => OrderStatus::Processing,
                                'shipping' => OrderStatus::Shipped,
                                'delivered' => OrderStatus::Delivered,
                                'cancel' => OrderStatus::Cancelled,
                                'refunded' => OrderStatus::Refunded,
                            ];

                            if (isset($statusMap[$activeTab])) {
                                $query->whereRelation('order', 'status', $statusMap[$activeTab]);
                            }
                        }

                        // Apply created_at filter if set
                        if (! empty($filters['created_at']['created_from'])) {
                            $query->where('created_at', '>=', $filters['created_at']['created_from']);
                        }
                        if (! empty($filters['created_at']['created_until'])) {
                            $query->where('created_at', '<=', $filters['created_at']['created_until']);
                        }
                        // Apply Today filter if it's set
                        if ($filters['today']['isActive']) {
                            $today = now()->toDateString();
                            $query->whereDate('created_at', $today);
                        }

                        // Apply Product Id values filter if it's set
                        if (! empty($filters['product_id']['values'])) {
                            $productIds = $filters['product_id']['values'];
                            $query->whereIn('product_id', $productIds);
                        }

                        // Apply Order Id values filter if it's set
                        if (! empty($filters['order_id']['values'])) {
                            $orderIds = $filters['order_id']['values'];
                            $query->whereIn('order_id', $orderIds);
                        }

                        // Use chunking to avoid memory issues
                        $grouped = collect();
                        $query->chunk(1000, function ($records) use (&$grouped) {
                            $chunkGrouped = $records
                                ->groupBy(fn ($item) => $item->product->name)
                                ->map(function ($group, $productName) {
                                    return [
                                        'product_name' => $productName,
                                        'total_quantity' => $group->sum('quantity'),
                                        'total_buy_price' => $group->sum(function ($item) {
                                            return $item->original_buy_price * $item->quantity;
                                        }),
                                        'total_refunded' => $group->sum('total_refunded_amount') ?? 0,
                                        'total_price' => $group->sum('sub_price'),
                                        'total_profit' => $group->sum(function ($item) {
                                            return $item->profit;
                                        }),
                                    ];
                                });

                            // Merge with existing grouped data
                            foreach ($chunkGrouped as $productName => $data) {
                                if ($grouped->has($productName)) {
                                    $existing = $grouped->get($productName);
                                    $grouped->put($productName, [
                                        'product_name' => $productName,
                                        'total_quantity' => $existing['total_quantity'] + $data['total_quantity'],
                                        'total_buy_price' => $existing['total_buy_price'] + $data['total_buy_price'],
                                        'total_price' => $existing['total_price'] + $data['total_price'],
                                        'total_profit' => $existing['total_profit'] + $data['total_profit'],
                                        'total_refunded' => $existing['total_refunded'] + $data['total_refunded'],
                                    ]);
                                } else {
                                    $grouped->put($productName, $data);
                                }
                            }
                        });

                        // Convert to the format expected by the export
                        $finalData = $grouped->map(function ($item) {
                            return [
                                'Product Name' => $item['product_name'],
                                'Total Quantity' => $item['total_quantity'],
                                'Total Buy Price' => $item['total_buy_price'],
                                'Total Price' => $item['total_price'],
                                'Total Profit' => $item['total_profit'],
                                'Total Refunded' => $item['total_refunded'],
                            ];
                        })->values();

                        return Excel::download(
                            new EnhancedOrderProductExport($finalData, $filters, $activeTab),
                            $filename
                        );
                    }
                })
                ->color('success')
                ->requiresConfirmation(),

        ];
    }

    public function getTabs(): array
    {
        return [

            'all' => Tab::make()->icon('heroicon-o-squares-2x2'),

            'new' => Tab::make()
                ->icon('heroicon-o-bolt')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRelation('order', 'status', OrderStatus::New)),

            'processing' => Tab::make()
                ->icon('heroicon-o-ellipsis-horizontal-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRelation('order', 'status', OrderStatus::Processing)),

            'delivered' => Tab::make()
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRelation('order', 'status', OrderStatus::Delivered)),

            'cancel' => Tab::make()
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRelation('order', 'status', OrderStatus::Cancelled)),

            'refund' => Tab::make()
                ->icon('heroicon-o-shield-exclamation')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRelation('order', 'status', OrderStatus::Refunded)),
        ];
    }

    protected function getPreviewContent($filters, $activeTab)
    {
        try {
            // Get the data using similar logic to the table
            $query = \App\Models\OrderProduct::query()
                ->with('product:id,name')
                ->with('order');

            // Apply the same base filter as the main table (delivered orders only)
            // unless we're specifically filtering by tab
            if ($activeTab === 'all') {
                // For 'all' tab, use the same logic as the main table
                $query->whereHas('order', fn ($q) => $q->where('status', OrderStatus::Delivered));
            } else {
                // For specific tabs, filter by that status only
                $statusMap = [
                    'new' => OrderStatus::New,
                    'processing' => OrderStatus::Processing,
                    'shipping' => OrderStatus::Shipped,
                    'delivered' => OrderStatus::Delivered,
                    'cancel' => OrderStatus::Cancelled,
                    'refunded' => OrderStatus::Refunded,
                ];

                if (isset($statusMap[$activeTab])) {
                    $query->whereHas('order', fn ($q) => $q->where('status', $statusMap[$activeTab]));
                }
            }

            // Apply filters
            if (! empty($filters['created_at']['created_from'])) {
                $query->where('created_at', '>=', $filters['created_at']['created_from']);
            }
            if (! empty($filters['created_at']['created_until'])) {
                $query->where('created_at', '<=', $filters['created_at']['created_until']);
            }

            // Apply Today filter if it's set
            if ($filters['today']['isActive']) {
                $today = now()->toDateString();
                $query->whereDate('created_at', $today);
            }

            // Apply Product Id values filter if it's set
            if (! empty($filters['product_id']['values'])) {
                $productIds = $filters['product_id']['values'];
                $query->whereIn('product_id', $productIds);
            }

            // Apply Order Id values filter if it's set
            if (! empty($filters['order_id']['values'])) {
                $orderIds = $filters['order_id']['values'];
                $query->whereIn('order_id', $orderIds);
            }

            // Get grouped data
            $records = $query->get();

            // Add some debugging info
            $debugInfo = [
                'total_records' => $records->count(),
                'active_tab' => $activeTab,
                'has_filters' => ! empty($filters),
                'first_record' => $records->first() ? [
                    'id' => $records->first()->id,
                    'product_name' => $records->first()->product?->name,
                    'has_product' => ! is_null($records->first()->product),
                    'has_order' => ! is_null($records->first()->order),
                    'created_from' => $filters['created_at']['created_from'] ?? null,
                    'created_until' => $filters['created_at']['created_until'] ?? null,
                ] : null,
            ];

            // Check if we have any records
            if ($records->isEmpty()) {
                return view('filament.preview.order-products-empty', [
                    'activeTab' => $activeTab,
                    'filters' => $filters,
                    'message' => 'No order products found with the current filters.',
                    'debug' => $debugInfo,

                ]);
            }

            $grouped = $records
                ->groupBy(fn ($item) => $item->product->name ?? 'Unknown Product')
                ->map(function ($group, $productName) {
                    return [
                        'product_name' => $productName,
                        'total_quantity' => $group->sum('quantity'),
                        'total_buy_price' => $group->sum('original_buy_price') * $group->sum('quantity') ?? 0,
                        'total_price' => $group->sum('sub_price'),
                        'total_refunded' => $group->sum('total_refunded_amount') ?? 0,
                        'total_profit' => $group->sum('profit') ?? 0,
                    ];
                });

            // Calculate totals and expenses
            $totalRevenue = $grouped->sum('total_price');
            $totalBuyPrice = $grouped->sum('total_buy_price');
            $totalQuantity = $grouped->sum('total_quantity');
            $totalProfit = $grouped->sum('total_profit');
            $totalRefunded = $grouped->sum('total_refunded');

            // Calculate expenses
            $expenseQuery = Expanse::query();
            if (! empty($filters['created_at']['created_from'])) {
                $expenseQuery->where('created_at', '>=', $filters['created_at']['created_from']);
            }
            if (! empty($filters['created_at']['created_until'])) {
                $expenseQuery->where('created_at', '<=', $filters['created_at']['created_until']);
            }
            $totalExpenses = $expenseQuery->sum('amount') ?? 0;

            // Calculate order totals (fees)
            $orderQuery = \App\Models\Order::query();
            if (! empty($filters['created_at']['created_from'])) {
                $orderQuery->where('created_at', '>=', $filters['created_at']['created_from']);
            }
            if (! empty($filters['created_at']['created_until'])) {
                $orderQuery->where('created_at', '<=', $filters['created_at']['created_until']);
            }
            if ($activeTab !== 'all') {
                $statusMap = [
                    'new' => OrderStatus::New,
                    'processing' => OrderStatus::Processing,
                    'shipping' => OrderStatus::Shipped,
                    'delivered' => OrderStatus::Delivered,
                    'cancel' => OrderStatus::Cancelled,
                    'refunded' => OrderStatus::Refunded,
                ];
                if (isset($statusMap[$activeTab])) {
                    $orderQuery->where('status', $statusMap[$activeTab]);
                }
            }

            $orderTotals = $orderQuery->selectRaw('
                SUM(delivery_fees) as delivery_fees
            ')->first();

            $deliveryFees = $orderTotals->delivery_fees ?? 0;
            $totalOrderCount = $orderQuery->count();

            $netProfit = $totalProfit - ($totalExpenses + $deliveryFees);

            return view('filament.preview.order-products', [
                'grouped' => $grouped,
                'totalRevenue' => $totalRevenue,
                'totalBuyPrice' => $totalBuyPrice,
                'totalQuantity' => $totalQuantity,
                'totalProfit' => $totalProfit,
                'totalExpenses' => $totalExpenses,
                'totalOrderCount' => $totalOrderCount,
                'totalRefunded' => $totalRefunded,
                'deliveryFees' => $deliveryFees,
                'netProfit' => $netProfit,
                'activeTab' => $activeTab,
                'filters' => $filters,
                'debug' => $debugInfo,
            ]);
        } catch (\Exception $e) {
            return view('filament.preview.order-products-error', [
                'error' => $e->getMessage(),
                'activeTab' => $activeTab,
                'filters' => $filters,
            ]);
        }
    }
}
