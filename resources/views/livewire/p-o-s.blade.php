@php use App\Models\Product;use Illuminate\Support\Facades\Auth; @endphp

<div class="flex gap-4 p-10 ">
    <div class="flex-none border border-primary  w-[500px] p-3 ">
        <form wire:submit="create">
            <table class="w-full text-sm text-left rtl:text-right ">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <td class="px-3  py-2">Name</td>
                    <td class="px-3  py-2">Price</td>
                    <td class="px-3  py-2">QTY</td>
                    <td class="px-3  py-2 text-right ">Sub Price</td>
                </tr>
                </thead>
                @forelse($this->data as $key=>$da)
                    <tr class="text-sm ">
                        <td class=" py-2 text-[12px] flex gap-4">
                            {{ ($this->deleteProductAction)(['id' => $key]) }}
                            {{ $da['name'] }}
                        </td>
                        <td class=" py-2">{{ $da['price'] }}</td>
                        <td class=" py-2 text-center ">
                            {{ $da['quantity'] }}
                        </td>
                        <td class="px-2  py-2 text-right ">
                            {{ $da['sub_price'] }}
                        </td>
                    </tr>
                @empty
                @endforelse
            </table>

        </form>
        <x-filament-actions::modals />
    </div>
    <div class="flex-grow   ">
        {{ $this->categoryAction }}
        <x-filament-actions::modals/>

            <div class="grid grid-cols-5 mt-5 gap-4 ">
                @forelse(Product::where('seller_id',Auth::guard('seller')->user()->parent_id ?? Auth::guard('seller')->id())->get() as $product)
                <div class="p-2 rounded  border flex justify-center flex-col  items-center  ">
                    <img class="cursor-pointer w-max "
                         src="https://www.telstra.com.au/content/dam/tcom/devices/mobile/mhdwhst-ip15/pink/landscape-front.png"
                         alt="">
                    <small class="text-[10px] font-bold ">{{ $product->name }}</small>

                    @if($product->has_variant)
                        <div class="mt-3">
                            {{ ($this->variantsAction)(['data' => $product]) }}
                        </div>
                        @else
                        <p class="my-2 pb-3   font-bold "> {{ number_format($product->price ?? 0) }}  ကျပ်</p>
                        {{ ($this->addAction)(['data' => $product]) }}
                    @endif

                </div>
                @empty
                @endforelse

            </div>

    </div>
</div>
