<div>

    <nav class="relative z-10 px-8 container mx-auto mt-2">
        <div
            class="flex items-center justify-between p-1 space-x-1 list-none border rounded-md text-neutral-700 group border-neutral-200/80">
            <a href="#_"
                class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-900 focus:outline-none disabled:opacity-50 disabled:pointer-events-none bg-background hover:bg-neutral-100 group w-max">
                <span class="block font-bold">Pines UI</span>
            </a>
            <ul class="flex items-center justify-center flex-1">
                <li>
                    <a href="{{ route('store',$store_id) }}"
                        class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-900 focus:outline-none disabled:opacity-50 disabled:pointer-events-none bg-background hover:bg-neutral-100 group w-max">
                        Home
                    </a>
                </li>
                @auth
                    <li>
                        <a href="{{ route('cart') }}"
                            class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-900 focus:outline-none disabled:opacity-50 disabled:pointer-events-none bg-background hover:bg-neutral-100 group w-max">
                            Cart
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('order') }}"
                            class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-900 focus:outline-none disabled:opacity-50 disabled:pointer-events-none bg-background hover:bg-neutral-100 group w-max">
                            Orders
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-900 focus:outline-none disabled:opacity-50 disabled:pointer-events-none bg-background hover:bg-neutral-100 group w-max">
                            <span class="block font-bold">Login to View Cart</span>
                        </a>
                    </li>
                @endauth
                <li>
                    <a href="#"
                        class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-900 focus:outline-none disabled:opacity-50 disabled:pointer-events-none bg-background hover:bg-neutral-100 group w-max">
                        About
                    </a>
                </li>
            </ul>
            <a href="{{ route('login') }}"
                class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium transition-colors rounded-md hover:text-neutral-900 focus:outline-none disabled:opacity-50 disabled:pointer-events-none bg-background hover:bg-neutral-100 group w-max">
                <span class="block font-bold">Login</span>
            </a>
        </div>
    </nav>


    {{-- @livewire('slider') --}}

    @livewire('product', ['storeID' => $this->store_id])

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const profileButton = document.querySelector('.relative button');
        const profileDropdown = document.querySelector('.relative .absolute');

        profileButton.addEventListener('click', function () {
            profileDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function (event) {
            if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.add('hidden');
            }
        });
    });
</script>