<div class="container mx-auto px-4 py-8 {{ app()->currentLocale() == 'my' ? 'font-mm' : 'font-jost'}}">
    <div class="max-w-7xl mx-auto">
        <!-- Loading indicator -->
        <div wire:loading.delay.long class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-50">
            <div class="bg-white p-4 rounded-lg shadow-lg">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-900"></div>
            </div>
        </div>

        <h1 class="text-lg md:text-2xl 2xl:text-3xl font-bold mb-6 ">
            {{ __('messages.orders.history') }}
        </h1>

        <!-- Orders List -->
        <div class="space-y-4">
            @forelse($this->orders as $order)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="p-4 sm:p-6">
                        <!-- Order Header -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 sm:gap-0">
                            <!-- Order Info -->
                            <div class="space-y-2">
                                <p class="text-sm text-gray-500">
                                    {{ __('messages.orders.order_id') }}: <span class="hidden sm:inline">{{ $order->invoice_number }}</span><span class="sm:hidden">{{ Str::limit($order->invoice_number, 20, '...') }}</span>
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ __('messages.orders.date') }}: {{ $order->created_at->format('M d, Y') }}
                                </p>
                                @if($order->seller)
                                <p class="text-sm text-gray-600 font-medium">
                                    {{ $order->seller->name }}
                                </p>
                                @endif
                            </div>

                            <!-- Price Info -->
                            <div class="text-left sm:text-right flex-row lg:flex-col flex justify-between items-center lg:items-end">
                                <p class="text-lg font-bold">{{ number_format($order->total_price) }} Ks</p>
                                <div class="">
                                    <p class="inline-flex items-center my-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ __('messages.orders.delivery_fee') }}: {{ number_format($order->delivery_fees) }} Ks
                                    </p>
                                    <p class="text-sm text-end text-gray-500">
                                        {{ $order->products->count() }} {{ __('messages.orders.items') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
                            <!-- Status Tags -->
                            <div class="flex flex-wrap gap-2">
                                @if($order->is_cod)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ __('messages.orders.cash_on_delivery') }}
                                    </span>
                                @endif

                                <div class="inline-flex items-center uppercase px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @switch($order->status)
                                        @case(\App\Helper\OrderStatus::New)
                                            bg-yellow-100 text-yellow-800
                                        @break
                                        @case(\App\Helper\OrderStatus::Processing)
                                            bg-blue-100 text-blue-800
                                        @break
                                        @case(\App\Helper\OrderStatus::Delivered)
                                            bg-green-100 text-green-800
                                        @break
                                        @case(\App\Helper\OrderStatus::Cancelled)
                                            bg-red-100 text-red-800
                                        @break
                                        @default
                                            bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ __('messages.orders.status.' . strtolower($order->status->value)) }}
                                </div>
                            </div>

                            <!-- View Details Button -->
                            <a href="{{ route('order.detail', $order) }}" wire:navigate
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-slate-900 hover:bg-slate-600 transition-all focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-slate-500">
                                {{ __('messages.orders.view_details') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white ">
                        {{ __('messages.orders.no_orders') }}
                    </h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400 ">
                        {{ __('messages.orders.start_shopping_message') }}
                    </p>
                    <div class="mt-6">
                    <a href="{{ route('welcome') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-slate-900 hover:bg-slate-600 focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-slate-500">
                            {{ __('messages.orders.start_shopping') }}
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6 sm:mt-8">
            {{ $this->orders->links() }}
        </div>
    </div>
</div>
