<div class="min-h-screen bg-gray-100 py-4 sm:py-6 {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }}" >
    <div class="max-w-4xl mx-auto bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Invoice Header -->
        <div class="p-4 sm:p-8 border-b relative ">
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 sm:gap-0">
                <!-- Company/Store Info -->
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <img src="{{ $order->seller->getFirstMediaUrl('logos')}}" class="w-12 h-12 sm:w-16 sm:h-16" alt="">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $order->seller->name }}</h1>
                </div>

                <div class="w-full sm:w-auto">
                    <!-- Status Badge -->
                    <div class="text-end mb-3 ">
                        <p class="inline uppercase  px-3 py-1 rounded-full text-xs font-medium
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
                            {{ __('messages.order_detail.status.' . strtolower($order->status->value)) }}
                        </p>
                    </div>
                    <p class="text-sm text-gray-600 my-2 ">
                        {{ __('messages.order_detail.invoice_number') }}{{ $order->invoice_number }}
                    </p>
                    <p class="text-sm text-gray-600 ">
                        {{ __('messages.order_detail.date') }}: {{ $order->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="p-4 sm:p-8">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-sm font-semibold text-gray-600 ">
                                {{ __('messages.order_detail.item') }}
                            </th>
                            <th class="text-center py-2 text-sm font-semibold text-gray-600">
                                {{ __('messages.order_detail.quantity') }}
                            </th>
                            <th class="text-right py-2 text-sm font-semibold text-gray-600">
                                {{ __('messages.order_detail.price') }}
                            </th>
                            <th class="text-right py-2 text-sm font-semibold text-gray-600">
                                {{ __('messages.order_detail.total') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 divide-dotted">
                        @foreach($order->products as $item)
                            <tr>
                                <td class="py-3">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <img src="{{ $item->product->productImg }}" alt="{{ $item->product->name }}"
                                             class="w-10 h-10 sm:w-12 sm:h-12 object-cover hidden lg:block  rounded">
                                        <div  class="">
                                            <p class="text-sm font-medium text-gray-800 line-clamp-2">{{ $item->product->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->product->category->name }}</p>
                                            @if($item->variant)
                                                <p class="text-xs text-gray-500">{{ $item->variant->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-sm text-gray-600">{{ $item->quantity }}</td>
                                <td class="text-right text-sm text-gray-600">{{ number_format($item->price) }} Ks</td>
                                <td class="text-right text-sm text-gray-600">{{ number_format($item->price * $item->quantity) }} Ks</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="mt-4 sm:mt-6 border-t border-dotted space-y-2 pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">
                        {{ __('messages.order_detail.subtotal') }}
                    </span>
                    <span class="text-sm text-gray-600">{{ number_format($order->products->sum(fn($item) => $item->price * $item->quantity)) }} Ks</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">
                        {{ __('messages.order_detail.delivery_fee') }}
                    </span>
                    <span class="text-sm text-gray-600">{{ number_format($order->delivery_fees) }} Ks</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t-2 border-slate-700">
                    <span class="text-sm font-bold text-gray-800">
                        {{ __('messages.order_detail.total') }}
                    </span>
                    <span class="text-lg sm:text-xl font-bold text-gray-800">{{ number_format($order->total_price) }} Ks</span>
                </div>
            </div>
        </div>

        <!-- Customer and Payment Info -->
        <div class="p-4 sm:p-8 border-t">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-8">
                <div>
                    <h2 class="text-xl sm:text-2xl text-gray-800 mb-2 font-bebas  ">
                        {{ __('messages.order_detail.customer_details') }}
                    </h2>
                    <p class="text-sm text-gray-600">{{ auth()->user()->name }}</p>
                    <p class="text-sm my-2 text-gray-600">{{ $order->address?->address }}</p>
                    <p class="text-sm text-gray-600">{{ $order->address?->township?->mm_name }}, {{ $order->address?->region?->mm_name }}</p>
                </div>
                <div class="text-start sm:text-end">
                    <h2 class="text-xl sm:text-2xl text-gray-800 mb-2 font-bebas">
                        {{ __('messages.order_detail.payment_method') }}
                    </h2>
                    @if($order->is_cod)
                        <p class="text-sm text-gray-600">
                            {{ __('messages.order_detail.cash_on_delivery') }}
                        </p>
                    @else
                        <p class="text-sm text-gray-600 font-bold">{{ $order->payment?->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->paymentAccount?->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Seller Address -->
        <div class="p-4 sm:p-8 bg-gray-50 border-t">
            <div class="max-w-3xl mx-auto text-center">
                @foreach ($order->seller->addresses as $addre)
                    <p class="text-sm mb-2 text-gray-600">{{ $addre->address }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>
