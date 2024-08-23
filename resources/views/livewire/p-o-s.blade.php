<div class="">
    @php use App\Models\Brand;use App\Models\Category;use App\Models\Product;use Illuminate\Support\Facades\Auth; @endphp

    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="https://flowbite.com/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img
                    src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT532dphHQ43_XgB7Q9B99BmkH5mE2H280PhxEsQQtQYuDIoJbkyJEm&usqp=CAE&s"
                    class="h-10"/>
            </a>
            <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-cta">
                <x-filament::input.wrapper class="mx-3 w-96 ">
                    <x-filament::input type="text" wire:model="search" placeholder="Scan or Type Search"/>
                </x-filament::input.wrapper>
                <x-filament::button>
                    Search
                </x-filament::button>

            </div>
        </div>
    </nav>

    <div class="flex  px-10 mt-5  ">
        <div class="flex-grow  bg-gray-50 p-5 ">
{{--            {{ $this->categoryAction }}--}}
            <x-filament-actions::modals/>

            <div class="flex flex-nowrap ">
                <div class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                    <div class="me-2">
                        <x-filament::button outlined
                            wire:click="filterCategory(0)"
                            color="{{ $this->categoryId == 0 ? 'primary' : 'gray'}}"
                            icon="heroicon-m-sparkles"
                            icon-position="before"
                        >
                            All Brands
                        </x-filament::button>
                    </div>
                    @foreach(Category::get() as $cat)
                        <div class="me-2 mb-3 ">
                            @if($this->categoryId == $cat->id)
                                <x-filament::button
                                                    wire:click="filterCategory({{$cat->id}})"
                                                    color="warning" size="sm" text="sm">
                                    {{ $cat->name }}
                                </x-filament::button>
                                @else
                                <x-filament::button outlined
                                                    wire:click="filterCategory({{$cat->id}})"
                                                    color="gray" size="sm" text="sm">
                                    {{ $cat->name }}
                                </x-filament::button>
                            @endif

                        </div>
                    @endforeach
                </div>

            </div>

            <div class="grid grid-cols-5 mt-5 gap-3  ">
                @forelse($this->products as $product)
                    <div class="p-2 rounded  border flex justify-center flex-col  items-center  bg-white ">
                        <img class="cursor-pointer w-max "
                             src="https://www.telstra.com.au/content/dam/tcom/devices/mobile/mhdwhst-ip15/pink/landscape-front.png"
                             alt="">
                        <small class="text-[10px] font-bold ">{{ $product->name }}</small>

                        @if($product->has_variant)
                            <div class="mt-3">
                                {{ ($this->variantsAction)(['data' => $product]) }}
                            </div>
                        @else
                            <p class="my-2 pb-3   font-bold "> {{ number_format($product->price ?? 0) }} ကျပ်</p>
                            {{ ($this->addAction)(['data' => $product]) }}
                        @endif

                    </div>
                @empty
                    <div class="grid grid-cols-subgrid col-span-5 ">
                        <h1 class="text-gray-500 text-sm ">မတွေ့ရှိပါခင်ဗျာ</h1>
                    </div>
                @endforelse

            </div>

        </div>
        <div class="flex-none border border-primary  w-[500px] p-5 ">
            <form wire:submit="create">
                <table class="w-full text-sm text-left rtl:text-right ">
                    @forelse($this->data as $key=>$da)
                        <tr class="text-sm ">
                            <td class=" py-2  flex gap-4">
                                {{ ($this->deleteProductAction)(['id' => $key]) }}
                                <div class="flex flex-col">
                                    <p class="text-md "> {{ $da['name'] }} </p>
                                    <div class="text-gray-500 text-[10px] ">
                                       {{ $da['category_name'] }}
                                    </div>
                                </div>

                            </td>
                            <td class=" py-2"></td>
                            <td class=" py-2 text-center ">
                                <input type="text" id="first_name" style="" min="1"
                                       wire:change="changePriceDiscount({{ $da["product_id"] }})"
                                       wire:model ="discount"
                                       class="bg-gray-50 text-center  placeholder:text-[11px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0  focus:border-amber-500 block w-16  px-2 py-1.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500"
                                       placeholder="0 %"/>
                            </td>
                            <td class=" py-2 text-center ">
                                <input type="number" id="first_name" style="" min="1" value="{{ $da['quantity'] }}"
                                       class="bg-gray-50 text-center  placeholder:text-[11px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0  focus:border-amber-500 block w-16  px-1.5 py-1.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500"/>
                            </td>
                            <td class="px-2  py-2 text-right ">

                                @if($da["discount"] == 0)
                                    <sup>ကျပ်</sup>
                                    <small class="font-bold  text-[13px]">
                                        {{ $da['sub_price'] }}
                                    </small>
                                @else
                                    <div class="">
                                        <sup>ကျပ်</sup>
                                        <small class="line-through text-gray-500 text-[13px]">
                                            {{ $da['price'] * $da['quantity'] }}
                                        </small>
                                    </div>
                                    <small class="font-bold text-lg ">
                                        {{ $da['sub_price'] }}
                                    </small>
                                @endif

                            </td>
                        </tr>
                    @empty
                    @endforelse


                </table>

            </form>
            <x-filament-actions::modals/>
        </div>

    </div>

</div>
