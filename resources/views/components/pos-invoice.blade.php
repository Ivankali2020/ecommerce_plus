@php use Milon\Barcode\DNS1D; use Milon\Barcode\DNS2D; @endphp
@props([
    'showReceipt' => null,
    'lastOrder' => null,
])
 <!-- Receipt Modal -->
@if($showReceipt && $lastOrder)
        <div class="fixed inset-0 bg-black/10 backdrop-blur-sm flex items-center justify-center z-50 " wire:click="closeReceipt">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto" wire:click.stop id>
                <!-- Receipt Content for Printing -->
                <div id="receipt-content" class="receipt-print font-mono">
                    <!-- Items -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Items:</h3>
                        @if($lastOrder->products)
                            @foreach($lastOrder->products as $item)
                                <div class="flex justify-between items-start text-sm mb-3 pb-2 border-b border-gray-200 dark:border-gray-600">
                                    <div class="flex-1">
                                        <p class="font-medium">{{ $item->product->name }}</p>
                                        @if($item->variant_name)
                                            <p class="text-xs text-gray-500">Variant: {{ $item->variant->name }}</p>
                                        @endif
                                        {{-- check has discount --}}
                                        @if($item->discount > 0)
                                            <p class="text-xs text-gray-500">Discount: {{ $item->quantity }} x {{ $item->discount_price }} MMK ({{ $item->discount }}%) </p>
                                            @else
                                            <p class="text-xs text-gray-500">{{ $item->quantity }} x {{ number_format($item->price) }} MMK</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">{{ number_format($item->sub_price) }} MMK</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        {{-- <div class="border-b/-2 border-gray-300 dark:border-gray-600 my-4"></div> --}}
                    </div>
                    
                    <!-- Totals -->
                    <div class="mb-6 space-y-2">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total Amount:</span>
                            <span>{{ number_format($lastOrder->total_price) }} MMK</span>
                        </div>
                        
                        @if($lastOrder->given_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Given:</span>
                                <span>{{ number_format($lastOrder->given_amount) }} MMK</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Change:</span>
                                <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($lastOrder->change_amount) }} MMK</span>
                            </div>
                        @endif
                        <div class="border-b-2 border-gray-300 dark:border-gray-600 my-4"></div>
                    </div>
                    
                    <!-- QR Code Section -->
                    <div class="text-center mb-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Scan for receipt verification</p>
                        <div class="flex justify-center mb-2">
                            @php $barcode = (new DNS2D())->getBarcodeHTML($lastOrder->invoice_number, 'QRCODE', 4, 4); @endphp
                            {!! $barcode !!}
                        </div>
                        <p class="text-xs text-gray-500">Invoice: {{ $lastOrder->invoice_number }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ $lastOrder->created_at->format('M d, Y H:i') }}</p>

                    </div>
                    
                    <!-- Footer -->
                    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                        <p>Thank you for your business!</p>
                    </div>
                </div>
                
                <!-- Modal Actions -->
                <div class="flex space-x-2 mt-6 print-hide" x-data="print">
                    <button wire:click="closeReceipt" 
                            class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500">
                        Close
                    </button>
                    <button @click="printDoc('receipt-content')" 
                            class="flex-1 px-4 py-2 bg-success-600 text-white rounded-lg hover:bg-success-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H3a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-1a2 2 0 00-2-2H7a2 2 0 00-2 2v1a2 2 0 002 2z" />
                        </svg>
                        Print Receipt
                    </button>
                </div>
            </div>
        </div>
    @endif

    @script
<script>
    Alpine.data('print', () => {
        return {
            printDoc(id) {
                var prtContent = document.getElementById(id);
                var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');

                WinPrint.document.write(
                   "<link href='https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css' rel='stylesheet'>"
                );
                WinPrint.document.write(
                    "<style> table,.header_invoice{font-size: 10px !important;} .above_margin{margin-top: 20px .receipt-print{padding: 20px}}   </style>"
                );
                WinPrint.document.write(prtContent.innerHTML);
                // WinPrint.document.body.style.fontSize = '10px !important';

                WinPrint.document.close();

                setTimeout(()=>{
                    WinPrint.print();
                    WinPrint.focus();
                    WinPrint.close();
                },300)

            },
        }
    })
</script>
@endscript