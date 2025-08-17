
<div class=" rounded-lg" x-data="{ isSmall: false }" >
    <div id="invoice_id" x-show="!isSmall"  class="" >
        <div class="mx-auto  max-w-2xl above_margin" >
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4 ">

                    @if($record->seller->getFirstMediaUrl('logos'))
                    <img class="h-16 w-16 mr-2 rounded-full mb-4 " src="{{ $record->seller->getFirstMediaUrl('logos') }}" alt="{{ $record->seller->name}}" />
                    @endif
                    <div class="dark:text-slat-100 header_invoice">
                        <div class="dark:text-slat-100 font-bold text-lg"> {{ $record->seller->name}} </div>
                        <div class=" header_invoice"> {{ $record->seller?->phone }}</div>
                        <div class="text-sm header_invoice mb-2"> {{ $record->seller?->email }}</div>
                    </div>
                </div>
                <div class="dark:text-slat-100 header_invoice" id="">
                    <div class="font-bold text-xl mb-4 header_invoice">INVOICE</div>
                    <div class="text-sm header_invoice mb-2">Date: {{ $record->created_at->format('d M, Y H:i A') }}</div>
                    <div class="text-sm header_invoice mb-2">Invoice #: {{ $record->invoice_number }}</div>
                </div>
            </div>

            <table class="w-full text-left mb-8 mt-2 text-sm ">
                <thead>
                <tr>
                    <th class="dark:text-slat-100 font-bold header_invoice  py-2">Name</th>
                    <th class="dark:text-slat-100 font-bold header_invoice print:hidden py-2">Qty</th>
                    <th class="dark:text-slat-100 font-bold header_invoice  py-2">Price</th>
                    <th class="dark:text-slat-100 font-bold header_invoice  py-2">Dis</th>
                    <th class="dark:text-slat-100 font-bold header_invoice  py-2">Dis Price</th>
                    <th class="dark:text-slat-100 font-bold header_invoice  py-2 text-right ">Total</th>
                </tr>
                </thead>
                <tbody class="">
                @foreach($record->products as $detail)
                    <tr>
                        <td class="py-2 dark:text-slat-100 text-sm header_invoice">{{ $detail->product->name }}</td>
                        <td class="py-2 dark:text-slat-100">{{ $detail->quantity }}</td>
                        <td class="py-2 dark:text-slat-100">{{ $detail->price }}</td>
                        <td class="py-2 dark:text-slat-100">{{ $detail->discount }} %</td>
                        <td class="py-2 dark:text-slat-100">{{ $detail->discount_price }} </td>
                        <td class="py-2 dark:text-slat-100 text-right "> {{ $detail->sub_price }}</td>
                    </tr>
                @endforeach

                <tr >
                    <td colspan="3" class="py-2 text-right px-6 font-bold   dark:text-slat-100"></td>
                    <td colspan="2" class="py-2 text-left  font-bold   dark:text-slat-100">Subtotal:</td>
                    <td  class="py-2 dark:text-slat-100 text-right header_invoice text-base">{{ $record->sub_total }} </td>
                </tr>
                <tr >
                    <td colspan="3" class="py-2 text-right px-6 font-bold   dark:text-slat-100"></td>
                    <td colspan="2" class="py-2 text-left  font-bold   dark:text-slat-100">Tax <span class="font-light ">({{ $record->tax }} %)</span>:</td>
                    <td  class="py-2 dark:text-slat-100 text-right header_invoice text-base">{{ $record->tax_price }}</td>
                </tr>
                <tr >
                    <td colspan="3" class="py-2 text-right px-6 font-bold   dark:text-slat-100"></td>
                    <td colspan="2" class="py-2 text-left  font-bold   dark:text-slat-100">Delivery Fees:</td>
                    <td  class="py-2 dark:text-slat-100 text-right header_invoice text-base  ">{{ $record->delivery_fees ?? 0 }} </td>
                </tr>
                <tr >
                    <td colspan="3" class="py-2 text-right px-6 font-bold   dark:text-slat-100"></td>
                    <td colspan="2" class="py-2 text-left  font-bold   dark:text-slat-100">Grand Total:</td>
                    <td  class="py-2 dark:text-slat-100 text-right text-xl font-bold header_invoice"> <span class=" header_invoice">ကျပ်</span> {{ $record->total_price }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        @if($record->user)
        <div class="border-b-2  border-gray-300 py-8 ">
            <h2 class="text-2xl font-bold mb-4 header_invoice">Bill To:</h2>
            <div class="dark:text-slat-100 text-sm header_invoice mb-2"> Name : {{ $record->user?->name }}</div>
            <div class="dark:text-slat-100 text-sm header_invoice mb-2"> Address : {{ $record->address?->modAddress }}</div>
            <div class="dark:text-slat-100 text-sm header_invoice"> Phone : {{ $record->user?->phone }}</div>
        </div>
        @endif

        @if($record->seller->addresses)
        <div class=" border-gray-300 pb-8  mt-2">
            <h2 class=" font-bold mb-2 header_invoice">Seller Address:</h2>
            @forelse ($record->seller->addresses as $index=>$address)
            <div class="mb-2 text-xs">
                <p class=" text-xs">{{ $address->address }}</p>
            </div>
         @empty

         @endforelse
        </div>
        @endif

    </div>

    <div id="small" class="rounded-lg flex justify-center" x-show="isSmall" style="font-size: 12px !important" >
        <div id="invoice" class="m-5 shadow-sm mx-auto p-3 rounded lg:m-10 max-w-80 w-full overflow-hidden">
            <div class="mx-auto p-2 w-full overflow-hidden">

                <div class="flex justify-between items-start gap-2">
                    <div class="flex items-center min-w-0 flex-1">
                        @if($record->seller->getFirstMediaUrl('logos'))
                         <img class="h-12 w-12 mr-2 rounded-full mb-4 flex-shrink-0" src="{{ $record->seller->getFirstMediaUrl('logos') }}" alt="{{ $record->seller->name}}" />
                        @endif
                    </div>
                    <div class="mb-6 flex-shrink-0">
                        <div class="header_invoice flex justify-end">
                            <table class="mb-2">
                                <tr>
                                    <td class="pb-2 px-1 text-start text-xs">ရက်စွဲ</td>
                                    <td class="pb-2 px-2 text-start text-xs">:</td>
                                    <td class="pb-2 text-end text-xs whitespace-nowrap">
                                        {{ $record->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                                <tr class="mt-2">
                                    <td class="pb-2 px-1 text-start text-xs">အမှတ်စဉ်</td>
                                    <td class="pb-2 px-2 text-start text-xs">:</td>
                                    <td class="pb-2 text-end text-xs break-all">{{ $record->invoice_number }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <table class="w-full text-left mb-8 table-fixed">
                    <thead>
                        <tr>
                            <th class="header_invoice pb-3 w-2/5">Name</th>
                            <th class="header_invoice print:hidden pb-3 text-right w-1/5">Qty</th>
                            <th class="header_invoice pb-3 text-right w-1/5">Price</th>
                            <th class="header_invoice pb-3 text-right w-1/5">Total</th>
                        </tr>
                    </thead>
                    <tbody class="">
                    @foreach($record->products as $detail)
                        <tr class="">
                            <td class="pb-3 text-xs header_invoice break-words" style="line-height: 1.3rem">{{ $detail->product->name }}</td>
                            <td class="pb-3 text-right text-xs">{{ $detail->quantity }}</td>
                            <td class="pb-3 text-right text-xs">
                                @if($detail->discount > 0)
                                <sup style="font-size:8px">{{ number_format($detail->discount,0) }}%</sup> <span>{{ number_format($detail->discount_price,0) }}</span> 
                                @else
                                    {{ number_format($detail->price,0) }}
                                @endif
                            </td>
                            <td class="pb-3 text-right text-xs">{{ number_format($detail->sub_price,0) }}</td>
                        </tr>
                    @endforeach

                    <tr class="">
                        <td colspan="2" class="pb-3 text-right text-xs">Sub Total:</td>
                        <td colspan="2" class="pb-3 text-right header_invoice font-bold text-xs">{{ $record->sub_total }}</td>
                    </tr>

                    <tr>
                        <td colspan="2" class="pb-3 text-right text-xs whitespace-nowrap">Tax Fees ({{ number_format($record->tax ,0)}}%):</td>
                        <td colspan="2" class="pb-3 text-right header_invoice font-bold text-xs">{{ $record->tax_price ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pb-3 text-right text-xs">Delivery Fees:</td>
                        <td colspan="2" class="pb-3 text-right header_invoice font-bold text-xs">{{ $record->delivery_fees ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="pb-3 text-right text-xs">Grand Total:</td>
                        <td colspan="2" class="pb-3 text-right font-bold header_invoice text-xs">ကျပ် <span class="header_invoice">{{ number_format($record->total_price,0) }}</span></td>
                    </tr>
                    </tbody>
                </table>

                @if($record->user)
                <div class="border-gray-300 pb-8 mb-8 mt-2">
                    <table class="w-full table-fixed">
                        <tr>
                            <td class="px-1 pb-1 text-light w-1/4 text-xs">နာမည်</td>
                            <td class="px-1 pb-1 w-1/12 text-xs">:</td>
                            <td class="px-1 pb-1 break-words text-xs">{{ $record->user?->name }}</td>
                        </tr>
                        <tr>
                            <td class="px-1 pb-1 text-light w-1/4 text-xs">လိပ်စာ</td>
                            <td class="px-1 pb-1 w-1/12 text-xs">:</td>
                            <td class="px-1 pb-1 break-words text-xs" >{{ $record->address?->modAddress }}</td>
                        </tr>
                        <tr>
                            <td class="px-1 pb-1 text-light w-1/4 text-xs">ဖုန်း</td>
                            <td class="px-1 pb-1 w-1/12 text-xs">:</td>
                            <td class="px-1 pb-1 break-words text-xs">{{ $record->user?->phone }}</td>
                        </tr>
                    </table>
                </div>
                @endif

                @if($record->seller->addresses->count() > 0)
                <div class="border-gray-300 pb-8 mt-2">
                    <h2 class="font-bold mb-2 header_invoice text-xs">Seller Address:</h2>
                    @forelse ($record->seller->addresses as $index=>$address)
                    <div class="mb-2">
                        <p class="text-xs break-words">{{ $address->address }}</p>
                    </div>
                    @empty
                    @endforelse
                </div>
                @endif

            </div>

        </div>
    </div>

    <div class="mt-2 pt-4    " x-data="counter">
        <x-filament::button  @click="isSmall ? printDoc('small') : printDoc('invoice_id')">
            Print Invoice
        </x-filament::button>

        <x-filament::button @click="isSmall = !isSmall">
            <span x-text="isSmall ? 'Show Large Invoice' : 'Show Small Invoice'"></span>
        </x-filament::button>

        <x-filament::button color="warning" outlined @click="isSmall ? saveImage('invoice') : saveImage('invoice_id')">
            Save
        </x-filament::button>
    </div>

</div>
@assets
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@endassets
@script
<script>
    Alpine.data('counter', () => {
        return {
            printDoc(id) {
                var prtContent = document.getElementById(id);
                var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');

                WinPrint.document.write(
                   "<link href='https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css' rel='stylesheet'>"
                );
                WinPrint.document.write(
                    "<style> table,.header_invoice{font-size: 10px !important;} .above_margin{margin-top: 20px}  </style>"
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
            saveImage(id){
                const invoiceElement = document.getElementById(id);
                    if(id == 'invoice_id'){
                        invoiceElement.classList.add('p-4')
                    }
                    // Use html2canvas to capture the invoice element
                    html2canvas(invoiceElement,{scale : 3}).then(canvas => {
                        // Convert canvas to image
                        const imageData = canvas.toDataURL('image/png');

                        // Create a link element for download
                        const downloadLink = document.createElement('a');
                        downloadLink.href = imageData;
                        downloadLink.download = 'purchase.png'; // Specify the name of the downloaded file

                        // Trigger the download
                        downloadLink.click();
                    });
            }
        }
    })

    document.addEventListener('livewire:initialized', () => {
        document.getElementById('button').onclick = saveImage;
        function saveImage(){
            const invoiceElement = document.getElementById('invoice_id');

            // Use html2canvas to capture the invoice element
            html2canvas(invoiceElement).then(canvas => {
                // Convert canvas to image
                const imageData = canvas.toDataURL('image/png');

                // Create a link element for download
                const downloadLink = document.createElement('a');
                downloadLink.href = imageData;
                downloadLink.download = 'purchase.png'; // Specify the name of the downloaded file

                // Trigger the download
                downloadLink.click();
            });
        }
    })

</script>
@endscript
