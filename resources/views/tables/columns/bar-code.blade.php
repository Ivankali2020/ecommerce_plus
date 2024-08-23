@php use Picqer\Barcode\BarcodeGeneratorHTML; @endphp
<div class="text-center max-30 ">
    @if($getState())
        {!! (new BarcodeGeneratorHTML())->getBarcode($getState(),(new BarcodeGeneratorHTML())::TYPE_CODE_128,1) !!}
        <p class="text-gray-500 text-sm mt-1 ">{{ $getState() }}</p>
    @else
        <p class="text-gray-500 text-sm mt-1 "> Has Variants </p>
    @endif
</div>
