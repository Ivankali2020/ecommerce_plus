
<x-filament-panels::page>

<div class="flex gap-4 p-10 ">
    <div class="flex-none border border-primary  text-8xl text-red-600 w-[400px] p-3 ">
        01
    </div>
    <div class="flex-grow   ">
        {{ $this->categoryAction }}
        <x-filament-actions::modals />
        <div class="flex gap-2 justify-center  flex-wrap  mb-3 ">
            @foreach(range(0,10) as $int)
                <button class="px-3 py-1 hover:bg-green-50 hover:text-green-400 border rounded text-sm ">Categroy {{ $int }}</button>
            @endforeach
        </div>
        <div class="grid grid-cols-5   gap-4 ">
            @foreach(range(0,10) as $int)
                <div class="p-2  border flex justify-center flex-col  items-center  ">
                    <img  class="cursor-pointer " width="100" src="https://www.telstra.com.au/content/dam/tcom/devices/mobile/mhdwhst-ip15/pink/landscape-front.png" alt="">
                    <p class="text-sm font-bold ">iPhone 14 Pro max </p>
                    <p class="my-2  font-bold "> 999 Ks </p>
                    <button class="px-3 py-1 hover:bg-green-50 hover:text-green-400 border rounded text-sm ">Add</button>
                </div>
            @endforeach

        </div>
    </div>
</div>
</x-filament-panels::page>
