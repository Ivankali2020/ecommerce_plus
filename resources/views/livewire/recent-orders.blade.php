<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-lg md:text-2xl 2xl:text-3xl font-bold mb-6 ">
            {{ __('messages.recent_orders_page.title') }}
        </h1>
        
        @if($recentProducts->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white ">
                    {{ __('messages.recent_orders_page.no_products') }}
                </h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400 ">
                    {{ __('messages.recent_orders_page.no_products_description') }}
                </p>
                <div class="mt-6">
                    <a href="{{ route('products') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-slate-900 hover:bg-slate-700 focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-slate-500 ">
                        {{ __('messages.recent_orders_page.browse_products') }}
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
                @foreach($recentProducts as $product)
                   <x-product-item :product="$product" />
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $recentProducts->links() }}
            </div>
        @endif
    </div>
</div>
