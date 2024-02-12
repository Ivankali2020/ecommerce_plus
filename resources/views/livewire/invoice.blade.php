<div class=" rounded-lg" >
    <div id="invoice_id">
        <div class="mx-auto  max-w-2xl above_margin" >
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <img class="h-16 w-16 mr-2" src="https://camo.githubusercontent.com/044c0d1693b12131f1de3d29d8e93ba5df8b4cf94bc1da39c3454ac711d1602c/68747470733a2f2f6d656469612e67697068792e636f6d2f6d656469612f456b73443167776545456447772f67697068792e676966"
                         alt="Logo" />
                    {{--            <div class="dark:text-slat-100 font-semibold text-lg">LOGO </div>--}}
                </div>
                <div class="dark:text-slat-100 header_invoice" id="">
                    <div class="font-bold text-xl mb-2 header_invoice">INVOICE</div>
                    <div class="text-sm header_invoice">Date: {{ $record->created_at->format('d M, Y H:i A') }}</div>
                    <div class="text-sm header_invoice">Invoice #: {{ $record->invoice_number }}</div>
                </div>
            </div>
            <div class="border-b-2  border-gray-300 pb-8 mb-8 ">
                <h2 class="text-2xl font-bold mb-4 header_invoice">Bill To:</h2>
                <div class="dark:text-slat-100 text-sm header_invoice mb-2"> Name : {{ $record->user->name }}</div>
                <div class="dark:text-slat-100 text-sm header_invoice mb-2"> Address : {{ $record->address->modAddress }}</div>
                <div class="dark:text-slat-100 text-sm header_invoice"> Phone : {{ $record->user->phone }}</div>
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
                        <td class="py-2 dark:text-slat-100 text-[8px] header_invoice">{{ $detail->product->name }}</td>
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
                    <td  class="py-2 dark:text-slat-100 text-right text-xl font-bold header_invoice"> <span class=" header_invoice">{{ $record->payment->currency->symbol_native }}</span> {{ $record->total_price }}</td>
                </tr>
                </tbody>
            </table>
        </div>



    </div>

    <div class="mt-4 " x-data="counter">
        <x-filament::button @click="printDoc">
            Print Invoice
        </x-filament::button>
    </div>

</div>

@script
<script>
    Alpine.data('counter', () => {
        return {
            printDoc() {
                var prtContent = document.getElementById("invoice_id");

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
        }
    })
</script>
@endscript
