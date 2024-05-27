<div class="grid grid-cols-2 gap-4 ">
    @forelse($record as $variant)
        <div class="p-2 rounded  border flex justify-center flex-col  items-center  ">
            <img class="cursor-pointer w-max "
                 src="https://www.telstra.com.au/content/dam/tcom/devices/mobile/mhdwhst-ip15/pink/landscape-front.png"
                 alt="">
            <small class="text-[10px] font-bold ">{{ $variant->product->name }}</small>

            <small class="text-[10px] text-black  "># {{ $variant->sku }}</small>

            @dd($variant->attributes)
            @foreach($variant->attributes as $attribute)

            @endforeach
            <p class="my-2 pb-3   font-bold "> {{ number_format($variant->mmk_price ?? 0) }}  ကျပ် </p>
            {{ ($action->getModalAction('add'))(['data' => [ 'product_id' => $variant->product_id , 'id' => $variant->id, 'name' => $variant->product->name , 'price' => $variant->mmk_price ]]) }}

        </div>
    @empty
    @endforelse
</div>
