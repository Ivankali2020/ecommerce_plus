@php use Milon\Barcode\DNS1D; use Milon\Barcode\DNS2D; @endphp
<div>
    <form wire:submit="create">

        {{ $this->form }}

{{--        <div class="mt-8 py-4 ">--}}
{{--            <x-filament::button type="submit">--}}
{{--                Submit--}}
{{--            </x-filament::button>--}}
{{--        </div>--}}
    </form>
    
    <!-- Button Container with Flex Layout -->
    <div class="flex gap-4 lg:m-10 mt-2 {{ !($this->product && $this->data['type'] && $this->data['total']) ? 'hidden' : '' }}">
        <!-- Save Button -->
        {{-- <div class="print cursor-pointer hover:bg-warning rounded" id="save">
            <div class="flex gap-4 items-center rounded px-4 py-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                </svg>
                <span class="text-xs">Save</span>
            </div>
        </div> --}}
        
        <!-- Print Button -->
        <div class="print cursor-pointer hover:bg-success rounded" 
             wire:click="printBarcodes"
             wire:loading.attr="disabled"
             wire:target="printBarcodes">
            <div class="flex gap-4 items-center rounded px-4 py-1.5">
                <!-- Loading spinner, hidden when not loading -->
                <svg wire:loading.remove wire:target="printBarcodes" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.204-.95-2.195-2.131-2.331A48.12 48.12 0 0 0 12 6.75c-2.676 0-5.216.178-7.69.532C2.942 7.42 2 8.41 2 9.456v6.294A2.25 2.25 0 0 0 4.25 18h1.091"/>
                </svg>
                
                <!-- Loading spinner, shown when loading -->
                <svg wire:loading wire:target="printBarcodes" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                
                <!-- Button text -->
                <span class="text-xs" wire:loading.remove wire:target="printBarcodes">Print</span>
                <span class="text-xs" wire:loading wire:target="printBarcodes">Printing...</span>
            </div>
        </div>
    </div>
    <x-filament-actions::modals/>
    <div class="flex gap-5 flex-wrap justify-center mt-20" id="{{ $this->data['total'] > 1 ? 'barcode' : '' }}">
        @if($this->product && $this->data['type'] && $this->data['total'])
        @forelse(range(1,$this->data['total']) as $number)

        @php
            $sku = $this->variant?->sku ?? $this->product?->sku;
            $barcode = null;
            // Convert mm to pixels (approximately 3.78 pixels per mm at 96 DPI)
            // Handle empty/null values and ensure numeric types
            $widthMm = isset($this->data['width_mm']) && !empty($this->data['width_mm']) && is_numeric($this->data['width_mm']) ? (float)$this->data['width_mm'] : 50;
            $heightMm = isset($this->data['height_mm']) && !empty($this->data['height_mm']) && is_numeric($this->data['height_mm']) ? (float)$this->data['height_mm'] : 30;
            
            $widthPixels = round($widthMm * 3.78);
            $heightPixels = round($heightMm * 3.78);
            
            if ($sku) {
                try {
                    // Get barcode specific dimensions
                    $barcodeWidth = isset($this->data['barcode_width']) && !empty($this->data['barcode_width']) && is_numeric($this->data['barcode_width']) ? (int)$this->data['barcode_width'] : 2;
                    $barcodeHeight = isset($this->data['barcode_height']) && !empty($this->data['barcode_height']) && is_numeric($this->data['barcode_height']) ? (int)$this->data['barcode_height'] : 43;
                    
                    //check if QRCODE is selected
                    if ($this->data['type'] === 'QRCODE') {
                        // For QR codes, use barcode width as size multiplier
                        $barcode = (new DNS2D())->getBarcodeHTML($sku, 'QRCODE', $barcodeWidth, $barcodeWidth);
                    } else {
                        // Use custom barcode width and height for regular barcodes
                        $barcode = (new DNS1D())->getBarcodeHTML($sku, $this->data['type'], $barcodeWidth, $barcodeHeight);
                    }
                } catch (\Exception $e) {
                    // Handle the error silently or log it
                }
            }
        @endphp
       
            <div class="text-center border px-2 flex flex-col items-center justify-center" 
                 style="min-width: {{ $widthPixels }}px; width: {{ $widthPixels }}px; height: {{ $heightPixels }}px;" 
                 id="{{ $this->data['total'] == 1 ? 'barcode' : '' }}">
                <div class="text-xs" style="margin-bottom:4px;">{{ $this->variant?->sku ?? $this->product->sku }}</div>
                <div class="flex justify-center barcode-container" >
                    @if($barcode)
                        <div>
                            {!! $barcode !!}
                        </div>
                    @else
                        <span class="text-danger-600 text-xs">{{ $this->data['type'] }} type not available on {{ $sku }}</span>
                    @endif
                </div>
                <div class="text-xs " style="margin:4px 0;">{{ $this->variant?->name ?? $this->product->name }}</div>
                <div class="font-bold text-xs">{{ $this->variant?->mmk_price ?? $this->product->mmk_price }} ks</div>
            </div>
            @empty
        @endforelse
        @endif
    </div>


</div>
@assets
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
    /* Custom barcode styling */
    .barcode-container svg {
        max-width: 100%;
        height: auto;
    }
    .barcode-container {
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }
</style>
@endassets

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        document.getElementById('save').onclick = saveImage;
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
