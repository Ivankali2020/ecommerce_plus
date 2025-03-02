<div class="container mx-auto px-4 py-8">
    <!-- Loading indicator -->
    <div wire:loading.delay.long class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-50">
        <div class="bg-white p-4 rounded-lg shadow-lg">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-900"></div>
        </div>
    </div>

    <h1 class="text-3xl font-bold mb-8 font-bebas">Order History</h1>

    <!-- Group orders by seller -->
    @forelse($groupedOrders as $sellerName => $sellerOrders)
        <div class="mb-8">
            <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                {{ $sellerName }}
            </h2>



            <div class="space-y-4">
                @foreach($sellerOrders as $order)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div class="space-y-2">
                                    <p class="text-sm text-gray-500">Order ID: {{ $order->invoice_number }}</p>
                                    <p class="text-sm text-gray-500">Date: {{ $order->created_at->format('M d, Y') }}</p>

                                    <!-- Order Status Badge -->

                                </div>


                                <div class="text-right">
                                    <p class="text-lg font-bold">{{ number_format($order->total_price) }} Ks</p>
                                    <p class="inline-flex items-center my-3  px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                        Delivery Fee: {{ number_format($order->delivery_fees) }} Ks
                                    </span>
                                    <p class="text-sm text-gray-500">{{ $order->products->count() }} items</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 flex justify-between items-center">
                                <div class="flex gap-2">
                                    @if($order->is_cod)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            COD
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
                                            @case(\App\Helper\OrderStatus::Confirmed)
                                                bg-green-100 text-green-800
                                                @break
                                            @case(\App\Helper\OrderStatus::Cancelled)
                                                bg-red-100 text-red-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ $order->status }}
                                    </div>

                                </div>

                                <a href="{{ route('order.detail', $order) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-slate-900 hover:bg-slate-600 transition-all focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-slate-500">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No orders yet</h3>
            <p class="mt-1 text-sm text-gray-500">Start shopping to see your orders here.</p>
            <div class="mt-6">
                <a href="{{ route('welcome') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
                    Start Shopping
                </a>
            </div>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
</div>
