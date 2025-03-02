<div>


    @if (session()->has('error'))
        <div class="flex justify-center items-center min-h-svh flex-col gap-2 animate-fade-in-up">
            <p class="font-bold font-bebas text-4xl text-red-600 ">Error</p>
            <p class="text-sm font-jost text-slate-600 ">{{ session('error') }}</p>
        </div>
    @else


    @livewire('slider',[ 'seller' => $this->seller])
    @livewire('product', ['storeID' => $this->store_id])

    @endif


</div>
