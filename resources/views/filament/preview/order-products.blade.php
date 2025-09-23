<style>
    .preview-modal-content {
        max-height: 70vh;
        overflow-y: auto;
    }
    .preview-table th {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>

<div class="space-y-6">
    {{-- Header Info --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-content p-6">
            <div class="flex justify-between items-center  gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Active Tab:</span>
                    <span class="ml-2 inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ring-primary-600/10 bg-primary-50 text-primary-700 dark:bg-primary-400/10 dark:text-primary-400 capitalize">{{ $activeTab }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Total Records:</span>
                    <span class="ml-2 font-bold text-gray-950 dark:text-white">{{ $grouped->count() }}</span>
                </div>
                @if(!empty($filters['created_at']['From']) || !empty($filters['created_at']['To']))
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Date Range:</span>
                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ !empty($filters['created_at']['From']) ? \Carbon\Carbon::parse($filters['created_at']['From'])->format('M d, Y') : 'Start' }}
                        -
                        {{ !empty($filters['created_at']['To']) ? \Carbon\Carbon::parse($filters['created_at']['To'])->format('M d, Y') : 'End' }}
                    </span>
                </div>
                @endif
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
                    Product Details
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
                                            Product Name
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-end sortable-header cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors" data-sort="quantity">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-end">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Quantity
                                        </span>
                                        <span class="sort-icon text-gray-400">
                                           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-end sortable-header cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors" data-sort="buy_price">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-end">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Buy Price (ကျပ် )
                                        </span>
                                        <span class="sort-icon text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-end sortable-header cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors" data-sort="sale_price">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-end">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Sale Price (ကျပ် )
                                        </span>
                                        <span class="sort-icon text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 text-end sortable-header cursor-pointer hover:bg-gray-100 dark:hover:bg-white/10 transition-colors" data-sort="profit">
                                    <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-end">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Profit (ကျပ် )
                                        </span>
                                        <span class="sort-icon text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </span>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5" id="products-tbody">
                            @foreach($grouped as $item)
                            <tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75 hover:bg-gray-50 dark:hover:bg-white/5" 
                                data-buy-price="{{ $item['total_buy_price'] }}" 
                                data-sale-price="{{ $item['total_price'] }}" 
                                data-profit="{{ $item['total_profit'] }}"
                                data-quantity="{{ $item['total_quantity'] }}">
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white font-medium">
                                                            {{ $item['product_name'] }}
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
                                                            {{ number_format($item['total_quantity']) }}
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
                                                            ကျပ် {{ number_format($item['total_buy_price']) }}
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
                                                            ကျပ် {{ number_format($item['total_price']) }}
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
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 font-medium {{ $item['total_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                            ကျပ် {{ number_format($item['total_profit']) }}
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
                        <tfoot class="divide-y divide-gray-200 dark:divide-white/5">
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                                                <div class="flex">
                                                    <div class="flex max-w-max">
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white font-bold">
                                                            TOTAL
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
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white font-bold">
                                                            {{ number_format($totalQuantity) }}
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
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white font-bold">
                                                            ကျပ် {{ number_format($totalBuyPrice) }}
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
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 text-gray-950 dark:text-white font-bold">
                                                            ကျပ် {{ number_format($totalRevenue) }}
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
                                                        <div class="fi-ta-text-item inline-flex items-center gap-1.5 text-sm leading-6 font-bold {{ $totalProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                            ကျပ် {{ number_format($totalProfit) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header flex flex-col gap-3 px-6 py-4">
            <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                Financial Summary
            </h3>
        </div>
        <div class="fi-section-content p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Revenue & Costs --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Total Revenue</span>
                        <span class="font-bold text-green-600 dark:text-green-400">ကျပ် {{ number_format($totalRevenue) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Total Buy Price (Cost of Goods)</span>
                        <span class="font-semibold text-primary-600 dark:text-primary-400">ကျပ် {{ number_format($totalBuyPrice) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Total Delivery Fees</span>
                        <span class="font-semibold text-primary-600 dark:text-primary-400">ကျပ် {{ number_format($deliveryFees) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Total Expenses</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">ကျပ် {{ number_format($totalExpenses) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Total Refunded</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">ကျပ် {{ number_format($totalRefunded) }}</span>
                    </div>
                </div>

                {{-- Profit Analysis --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Gross Profit (Before Expenses)</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">ကျပ် {{ number_format($totalProfit) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Net Profit (After All Costs)</span>
                        <span class="font-bold text-lg {{ $netProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            ကျပ် {{ number_format($netProfit) }}
                        </span>
                    </div>
                    @if($totalRevenue > 0)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Cost of Goods (% of Revenue)</span>
                        <span class="font-semibold text-gray-950 dark:text-white">{{ number_format(($totalBuyPrice / $totalRevenue) * 100, 1) }}%</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Gross Profit Margin</span>
                        <span class="font-semibold text-gray-950 dark:text-white">{{ number_format(($totalProfit / $totalRevenue) * 100, 1) }}%</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-white/10">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Net Profit Margin</span>
                        <span class="font-bold {{ $netProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ number_format(($netProfit / $totalRevenue) * 100, 1) }}%
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats Cards --}}
    <div class="flex gap-4 justify-around ">
        <div class="fi-section rounded-xl w-full  bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-4 text-center">
                <div class="text-2xl mb-2 font-bold text-primary-600 dark:text-primary-400">{{ $grouped->count() }}</div>
                <div class="text-sm text-primary-600 dark:text-primary-400 font-medium">Products</div>
            </div>
        </div>
         <div class="fi-section rounded-xl w-full  bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-4 text-center">
                <div class="text-2xl mb-2 font-bold text-primary-600 dark:text-primary-400">{{ $totalOrderCount }}</div>
                <div class="text-sm text-primary-600 dark:text-primary-400 font-medium">Total Orders</div>
            </div>
        </div>
        <div class="fi-section rounded-xl w-full  bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-4 text-center">
                <div class="text-2xl mb-2 font-bold text-green-600 dark:text-green-400">{{ number_format($totalQuantity) }}</div>
                <div class="text-sm text-green-600 dark:text-green-400 font-medium">Total Quantity</div>
            </div>
        </div>
        <div class="fi-section rounded-xl w-full  bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-4 text-center">
                <div class="text-2xl mb-2 font-bold text-info-600 dark:text-info-400">ကျပ် {{ number_format($totalRevenue) }}</div>
                <div class="text-sm text-info-600 dark:text-info-400 font-medium">Revenue</div>
            </div>
        </div>
        <div class="fi-section rounded-xl w-full  bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-4 text-center">
                <div class="text-2xl mb-2 font-bold text-info-600 dark:text-info-400">ကျပ် {{ number_format($totalRefunded) }}</div>
                <div class="text-sm text-info-600 dark:text-info-400 font-medium">Total Refunded</div>
            </div>
        </div>
        <div class="fi-section rounded-xl w-full  bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-4 text-center">
                <div class="text-2xl mb-2 font-bold {{ $netProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">ကျပ် {{ number_format($netProfit) }}</div>
                <div class="text-sm {{ $netProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">Net Profit</div>
            </div>
        </div>
    </div>  
    
    @if(isset($debug))
    {{-- <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-content p-4">
            <h4 class="text-sm font-medium text-gray-950 dark:text-white mb-2">Debug Information:</h4>
            <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                <div><strong>Total Records Found:</strong> {{ $debug['total_records'] }}</div>
                <div><strong>Active Tab:</strong> {{ $debug['active_tab'] }}</div>
                <div><strong>Has Filters:</strong> {{ $debug['has_filters'] ? 'Yes' : 'No' }}</div>
                @if($debug['first_record'])
                <div><strong>First Record:</strong></div>
                <div class="ml-4">
                    <div>ID: {{ $debug['first_record']['id'] }}</div>
                    <div>Product Name: {{ $debug['first_record']['product_name'] ?? 'NULL' }}</div>
                    <div>Has Product: {{ $debug['first_record']['has_product'] ? 'Yes' : 'No' }}</div>
                    <div>Has Order: {{ $debug['first_record']['has_order'] ? 'Yes' : 'No' }}</div>
                </div>
                @endif
            </div>
        </div>
    </div> --}}
    @endif
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Sorting styles */
    .sortable-header {
        position: relative;
        user-select: none;
        background-color: rgba(59, 130, 246, 0.05);
        border-radius: 4px;
    }
    
    .sortable-header:hover {
        background-color: rgba(59, 130, 246, 0.1) !important;
    }
    
    .sortable-header.sort-asc .sort-icon {
        color: #3b82f6 !important;
        transform: rotate(0deg);
    }
    
    .sortable-header.sort-desc .sort-icon {
        color: #3b82f6 !important;
        transform: rotate(180deg);
    }
    
    .sort-icon {
        transition: transform 0.2s ease, color 0.2s ease;
        opacity: 0.7;
    }
    
    .sortable-header:hover .sort-icon {
        color: #3b82f6 !important;
        opacity: 1;
    }
    
    /* Add sorting animation */
    #products-tbody {
        transition: opacity 0.15s ease;
    }
    
    /* Debug styles */
    .sortable-header::after {
        content: ' (sortable)';
        font-size: 10px;
        color: #6b7280;
        font-weight: normal;
    }
</style>

{{-- JS moved to Vite asset: resources/js/filament/order-products-sorting.js --}}