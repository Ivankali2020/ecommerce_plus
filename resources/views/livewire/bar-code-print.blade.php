@php use Picqer\Barcode\BarcodeGeneratorHTML; @endphp
<div>
    @if($product->has_variant)
        @foreach($product->variants as $variant)
           <div class="flex justify-between items-center">
               <div class="flex flex-col justify-center items-center mt-3 ">
                   <p class="text-black font-bold  text-sm  ">{{ $variant->price }} Ks</p>
                   {!! (new BarcodeGeneratorHTML())->getBarcode($variant->sku,(new BarcodeGeneratorHTML())::TYPE_CODE_128,1) !!}
                   <p class="text-gray-500 text-sm mt-1 ">{{ $variant->name }}</p>
               </div>
               <div class="">
                   <x-filament::icon-button
                       icon="heroicon-m-printer"
                       wire:click="print('{{$variant->sku}}','{{$variant->name}}','{{$variant->mmk_price == 0 ? $variant->price : $variant->mmk_price}}')"
                       tag="a"
                       label="Filament"
                   />
               </div>
           </div>
        @endforeach
    @else
        <div class="flex flex-col justify-center items-center  max-30 ">
            <p class="text-black font-bold  text-sm  ">{{ $product->price }} Ks</p>
            {!! (new BarcodeGeneratorHTML())->getBarcode($product->sku,(new BarcodeGeneratorHTML())::TYPE_CODE_128,1) !!}
            <p class="text-gray-500 text-sm mt-1 ">{{ $product->name }}</p>
        </div>

        <div class="flex flex-col justify-center items-center mt-3 ">
            <x-filament::input.wrapper >
                <x-filament::input placeholder="အစောင်အရေအတွက်" align="center" wire:model="pages" integer autofocus="true" />
            </x-filament::input.wrapper>
            <span class="my-2"></span>
            <x-filament::icon-button
                icon="heroicon-m-printer"
                wire:click="print('{{$product->sku}}','{{$product->name}}','{{$product->mmk_price == 0 ? $product->price : $product->mmk_price}}')"
                tag="a"
                label="Print"
            />
        </div>
    @endif
</div>
