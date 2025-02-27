@php use Milon\Barcode\DNS1D; @endphp
<div>
    <form wire:submit="create">

        {{ $this->form }}

{{--        <div class="mt-8 py-4 ">--}}
{{--            <x-filament::button type="submit">--}}
{{--                Submit--}}
{{--            </x-filament::button>--}}
{{--        </div>--}}
    </form>
    <div class="print  lg:m-10  mt-2  {{ !($this->product && $this->data['type'] && $this->data['total']) ? 'hidden' : '' }}  cursor-pointer hover:bg-warning " id="button" >
        <div class="flex gap-4 items-center   rounded px-4 py-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 w-5 h-5 ">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
            </svg>
            <span class="text-xs">Save</span>
        </div>
    </div>
    <x-filament-actions::modals/>
    <div class="flex gap-5 flex-wrap justify-center mt-20" id="{{ $this->data['total'] > 1 ? 'barcode' : '' }}">
        @if($this->product && $this->data['type'] && $this->data['total'])
        @forelse(range(1,$this->data['total']) as $number)

        @php
            $sku = $this->variant?->sku ?? $this->product?->sku;
            $barcode = null;
            if ($sku) {
                try {
                    $barcode = (new DNS1D())->getBarcodeHTML($sku, $this->data['type'], 2, 43);
                } catch (\Exception $e) {
                    // Handle the error silently or log it
                }
            }
        @endphp
       
            <div class="text-center py-4 px-2 " style="min-width: 300px"  id="{{ $this->data['total'] == 1 ? 'barcode' : '' }}">
                <div class="text-xs my-2">{{ $this->variant?->sku ?? $this->product->sku }}</div>
                <div class=" flex  justify-center">
                    @if($barcode)
                        {!! $barcode !!}
                    @else
                        <span class="text-danger-600 text-xs ">{{ $this->data['type'] }} type not available on {{ $sku }}</span>
                    @endif
                </div>
                <div class="text-xs my-2">  {{ $this->variant?->name ?? $this->product->name }}</div>
                <div class="font-bold text-md ">{{ $this->variant?->mmk_price ?? $this->product->mmk_price }} ks</div>
            </div>
            @empty
        @endforelse
        @endif
    </div>


</div>
@assets
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@endassets

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        document.getElementById('button').onclick = saveImage;
        function saveImage(){
            const invoiceElement = document.getElementById('barcode');

            // Use html2canvas to capture the invoice element
            html2canvas(invoiceElement,{scale : 4}).then(canvas => {
                // Convert canvas to image
                const imageData = canvas.toDataURL('image/png');

                // Create a link element for download
                const downloadLink = document.createElement('a');
                downloadLink.href = imageData;
                downloadLink.download = 'invoice.png'; // Specify the name of the downloaded file

                // Trigger the download
                downloadLink.click();
            });
        }
    })

</script>
@endscript
