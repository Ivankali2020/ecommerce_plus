<div class="space-y-6">
    {{-- Header Info --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-content p-6">
            <div class="flex justify-between items-center gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Total Products:</span>
                    <span class="ml-2 font-bold text-gray-950 dark:text-white">{{ number_format($totalProducts) }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Total Stock:</span>
                    <span class="ml-2 font-bold text-gray-950 dark:text-white">{{ number_format($totalStock) }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Total Value:</span>
                    <span class="ml-2 font-bold text-gray-950 dark:text-white">¥{{ number_format($totalSellValue) }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Generated:</span>
                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ now()->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header flex flex-col gap-3 px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    Product Details ({{ number_format($products->count()) }} items)
                </h3>
            </div>
        </div>
        <div class="fi-section-content">
            <div class="overflow-hidden border-t border-gray-200 dark:border-white/10">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                        <thead class="divide-y divide-gray-200 dark:divide-white/5">
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Code
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-start">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Product Name
                                        </span>
                                    </span>
                                </th>
                               
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-end">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-end">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Stock
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-end">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-end">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Buy Price (¥)
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-end">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-end">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Sell Price (¥)
                                        </span>
                                    </span>
                                </th>
                                 <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-end">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-end">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                          Total  Buy Price (¥)
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-end">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-end">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                           Total Sell Price (¥)
                                        </span>
                                    </span>
                                </th>
                                 <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-end">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-end">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Estimate Profit (¥)
                                        </span>
                                    </span>
                                </th>
                                
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                            @foreach($products as $product)
                            <tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white font-medium">
                                                            {{ $product->code }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white font-medium">
                                                            {{ $product->name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                               
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-end text-end">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex justify-end">
                                                    <div class="flex max-w-max">
                                                        <span class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $product->stock < 5 ? 'bg-red-50 text-red-700 ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20' : ($product->stock < 10 ? 'bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20' : 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20') }}">
                                                            {{ number_format($product->stock) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-end text-end">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex justify-end">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                            ¥{{ number_format($product->buy_price) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-end text-end">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex justify-end">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                            ¥{{ number_format($product->price) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                 <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-end text-end">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex justify-end">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                            ¥{{ number_format($product->buy_price * $product->stock) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-end text-end">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex justify-end">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                            ¥{{ number_format($product->price  * $product->stock) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                 <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-end text-end">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex justify-end">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white">
                                                            ¥{{ number_format($product->price - $product->buy_price ) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                               
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header flex flex-col gap-3 px-6 py-4">
            <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                Financial Analysis
            </h3>
        </div>
        <div class="fi-section-content p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Stock & Inventory --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Total Inventory Value</span>
                        <span class="font-bold text-success-600 dark:text-success-400">¥{{ number_format($totalSellValue) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Total Cost of Goods</span>
                        <span class="font-semibold text-warning-600 dark:text-warning-400">¥{{ number_format($totalBuyValue) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Out of Stock (< 10)</span>
                        <span class="font-semibold text-danger-600 dark:text-danger-400">{{ number_format($outOfStockCount) }} items</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Empty Stock (0)</span>
                        <span class="font-semibold text-danger-600 dark:text-danger-400">{{ number_format($emptyStockCount) }} items</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Frozen Products</span>
                        <span class="font-semibold text-info-600 dark:text-info-400">{{ number_format($frozenProductCount) }} items</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Rice Products</span>
                        <span class="font-semibold text-warning-600 dark:text-warning-400">{{ number_format($riceProductCount) }} items</span>
                    </div>
                </div>

                {{-- Profit Analysis --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Potential Profit</span>
                        <span class="font-bold text-lg {{ $totalPotentialProfit >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                            ¥{{ number_format($totalPotentialProfit) }}
                        </span>
                    </div>
                    @if($totalSellValue > 0)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Cost Ratio (% of Inventory)</span>
                        <span class="font-semibold text-gray-950 dark:text-white">{{ number_format(($totalBuyValue / $totalSellValue) * 100, 1) }}%</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Profit Margin</span>
                        <span class="font-bold {{ $totalPotentialProfit >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                            {{ number_format(($totalPotentialProfit / $totalSellValue) * 100, 1) }}%
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Recommended Products</span>
                        <span class="font-semibold text-success-600 dark:text-success-400">{{ number_format($recommendedCount) }} items</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats Cards --}}
    <div class="flex gap-4 justify-around">
        <div class="fi-section rounded-xl w-full bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-4 text-center">
                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ number_format($totalProducts) }}</div>
                <div class="text-sm text-primary-600 dark:text-primary-400 font-medium">Total Products</div>
            </div>
        </div>
        <div class="fi-section rounded-xl w-full bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-4 text-center">
                <div class="text-2xl font-bold text-success-600 dark:text-success-400">{{ number_format($totalStock) }}</div>
                <div class="text-sm text-success-600 dark:text-success-400 font-medium">Total Stock</div>
            </div>
        </div>
        <div class="fi-section rounded-xl w-full bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-4 text-center">
                <div class="text-2xl font-bold text-info-600 dark:text-info-400">¥{{ number_format($totalSellValue) }}</div>
                <div class="text-sm text-info-600 dark:text-info-400 font-medium">Inventory Value</div>
            </div>
        </div>
        <div class="fi-section rounded-xl w-full bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-4 text-center">
                <div class="text-2xl font-bold {{ $totalPotentialProfit >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">¥{{ number_format($totalPotentialProfit) }}</div>
                <div class="text-sm {{ $totalPotentialProfit >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }} font-medium">Potential Profit</div>
            </div>
        </div>
    </div>

    {{-- Applied Filters --}}
    @if(!empty($filters))
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header flex flex-col gap-3 px-6 py-4">
            <h4 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">Applied Filters</h4>
        </div>
        <div class="fi-section-content p-6">
            <div class="flex flex-wrap gap-2">
                @foreach($filters as $filterKey => $filterValue)
                    @if(!empty($filterValue))
                        <span class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-primary-50 text-primary-700 ring-primary-600/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/20">
                            {{ ucfirst(str_replace('_', ' ', $filterKey)) }}: 
                            @if(is_array($filterValue))
                                {{ implode(', ', array_filter($filterValue, fn($v) => !empty($v))) }}
                            @else
                                {{ $filterValue }}
                            @endif
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
