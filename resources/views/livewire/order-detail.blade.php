<div class="min-h-screen bg-gray-100 py-6 font-jost ">
    <div class="max-w-4xl mx-auto bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Invoice Header -->
        <div class="p-8 border-b">
            <div class="flex justify-between items-start">
                <!-- Company/Store Info -->
                <div class="flex items-center gap-4 ">
                    <img src="{{ $order->seller->getFirstMediaUrl('logos')}}" class="w-16 h-16" alt="">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $order->seller->name }}</h1>
                </div>

               <div class="">
                 <!-- Status Badge -->
                <div class="text-end">
                    <p class="inline  text-end w-full  uppercase px-3 py-1 rounded-full text-xs font-medium
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
                  </p>
                </div>
                <p class="text-sm text-gray-600 my-2">Invoice #{{ $order->invoice_number }}</p>
                 <p class="text-sm text-gray-600">Date: {{ $order->created_at->format('M d, Y') }}</p>
               </div>
            </div>
        </div>


        <!-- Order Items -->
        <div class="p-8">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 text-sm font-semibold text-gray-600">Item</th>
                        <th class="text-center py-2 text-sm font-semibold text-gray-600">Qty</th>
                        <th class="text-right py-2 text-sm font-semibold text-gray-600">Price</th>
                        <th class="text-right py-2 text-sm font-semibold text-gray-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 divide-dotted ">
                    @foreach($order->products as $item)
                        <tr>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->product->productImg }}" alt="{{ $item->product->name }}"
                                         class="w-12 h-12 object-cover rounded">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $item->product->name }}</p>
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
                <tfoot class="border-t border-dotted ">
                    <tr>
                        <td colspan="2" class="py-3"></td>
                        <td class="py-3 text-right text-sm font-medium text-gray-600">Subtotal</td>
                        <td class="py-3 text-right text-sm text-gray-600">{{ number_format($order->products->sum(fn($item) => $item->price * $item->quantity)) }} Ks</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td class="py-3 text-right text-sm font-medium text-gray-600">Delivery Fee</td>
                        <td class="py-3 text-right text-sm text-gray-600">{{ number_format($order->delivery_fees) }} Ks</td>
                    </tr>
                    <tr class="border-slate-700  border-t-2 ">
                        <td colspan="2"></td>
                        <td class="py-3 text-right text-sm font-bold text-gray-800">Total</td>
                        <td class="py-3 text-right text-xl font-bold text-gray-800">{{ number_format($order->total_price) }} Ks</td>
                    </tr>
                </tfoot>
            </table>
        </div>

          <!-- Billing Info -->
          <div class="p-8 border-t">
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h2 class="text-2xl text-gray-800 mb-2 font-bebas ">Customer Details</h2>
                    <p class="text-sm text-gray-600">{{ auth()->user()->name }}</p>
                    <p class="text-sm my-2 text-gray-600">{{ $order->address?->address }}</p>
                    <p class="text-sm text-gray-600">{{ $order->address?->township?->mm_name }}, {{ $order->address?->region?->mm_name }}</p>
                </div>
                <div class="text-end">
                    <h2 class="text-2xl text-gray-800 mb-2 font-bebas">Payment Method</h2>

                        @if($order->is_cod)
                            <p class="text-sm text-gray-600">
                                Cash on Delivery
                            </p>
                        @else
                            <p class="text-sm text-gray-600 font-bold "> {{ $order->payment?->name }}</p>
                            <p class="text-sm text-gray-500"> {{ $order->paymentAccount?->name }}</p>
                        @endif

                </div>
            </div>
        </div>


        <!-- Seller Address & Notes -->
        <div class="p-8 bg-gray-50 border-t">
            <div class="max-w-3xl mx-auto text-center">
                @foreach ($order->seller->addresses as $addre)
                    <p class="text-sm mb-2  text-gray-600">{{ $addre->address }}</p>
                @endforeach

            </div>
        </div>
    </div>
</div>
