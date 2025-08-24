<footer class="mt-16 border-t border-gray-300">    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">        
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:gap-8">           
         <!-- Company Info -->           
          <div class="space-y-2 md:space-y-4">              
              <h3 class="text-md md:text-lg font-semibold">{{ __('messages.footer.company_info') }}</h3>                
                        @if($seller)                  
                          <div>                        
                            <h4 class="font-medium mb-4">{{ $seller->name }}</h4>                        
                        @if($seller->phone)
                            <p class="text-sm lg:text-lg text-gray-500 mb-2">{{ __('messages.footer.phone') }}: {{ $seller->phone}}</p>
                        @endif
                        @if($seller->email)
                            <p class="text-sm lg:text-lg text-gray-500 mb-2">{{ __('messages.footer.email') }}: {{ $seller->email }}</p>
                        @endif
                        @if($seller->address)
                            <p class="text-sm lg:text-lg text-gray-500">{{ $seller->address }}</p>
                        @endif
                    </div>
                @endif

                <x-language-switcher />
            </div>

           
            <!-- Customer Support -->
            <div class="space-y-2 md:space-y-4">
                <h3 class="text-md md:text-lg font-semibold">{{ __('messages.footer.support') }}</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('about-us') }}" class="text-sm lg:text-lg text-gray-500 hover:text-black transition-colors">{{ __('messages.footer.about_us') }}</a></li>
                    <li><a href="{{ route('faq') }}" class="text-sm lg:text-lg text-gray-500 hover:text-black transition-colors">{{ __('messages.footer.faq') }}</a></li>
                    <li><a href="{{ route('returns-exchanges') }}" class="text-sm lg:text-lg text-gray-500 hover:text-black transition-colors">{{ __('messages.footer.returns') }}</a></li>
                    <li><a href="{{ route('privacy-policy') }}" class="text-sm lg:text-lg text-gray-500 hover:text-black transition-colors">{{ __('messages.footer.privacy_policy') }}</a></li>
                    <li><a href="{{ route('terms-conditions') }}" class="text-sm lg:text-lg text-gray-500 hover:text-black transition-colors">{{ __('messages.footer.terms_conditions') }}</a></li>
                </ul>
            </div>


            <!-- Quick Links -->
            <div class="space-y-2 md:space-y-4  ">
                <h3 class="text-md md:text-lg font-semibold">{{ __('messages.footer.quick_links') }}</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('welcome') }}" class="text-sm lg:text-lg text-gray-500 hover:text-black transition-colors">{{ __('messages.navigation.main_store') }}</a></li>
                    <li><a href="{{ route('products') }}" class="text-sm lg:text-lg text-gray-500 hover:text-black transition-colors">{{ __('messages.navigation.products') }}</a></li>
                    <li><a href="{{ route('wholesale') }}" class="text-sm lg:text-lg text-gray-500 hover:text-black transition-colors">{{ __('messages.navigation.wholesale') }}</a></li>
                    <li><a href="{{ route('delivery') }}" class="text-sm lg:text-lg text-gray-500 hover:text-black transition-colors">{{ __('messages.navigation.delivery') }}</a></li>
                </ul>
            </div>

            <div class="space-y-2 md:space-y-4">
                <h3 class="text-md md:text-lg font-semibold">{{ __('messages.footer.payment_methods') }}</h3>
                @if($seller && $seller->payments->isNotEmpty())
                    <div class="grid grid-cols-5 md:grid-cols-3 gap-3">
                        @foreach($seller->payments as $payment)
                            <div class="rounded-md bg-slate-100 h-14 w-14 text-center p-2 flex items-center justify-center ">
                                @if($payment->media->isNotEmpty())
                                    <img src="{{ $payment->media->first()->getUrl() }}" 
                                         alt="{{ $payment->name }}" 
                                         class="w-full h-full object-cover rounded-lg">
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-300 mt-8 pt-8 pb-16 lg:pb-0 flex flex-col sm:flex-row justify-between items-center">
            <a href="https://ivankali2020.github.io/porfolio/" class="text-gray-400 text-sm underline decoration-gray-200 underline-offset-7 hover:text-red-500">
                {{-- © {{ date('Y') }} {{ $seller ? $seller->name : __('messages.footer.company_info') }}.  --}}
                {{ __('messages.footer.all_rights_reserved') }}
            </a>
            <div class="flex space-x-4 mt-4 sm:mt-0">
                @php
                    $social = $seller && $seller->setting ? $seller->setting->data : [];
                @endphp
                @if(!empty($social['facebook']))
                    <a href="{{ $social['facebook'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-blue-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.408.595 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.408 24 22.674V1.326C24 .592 23.406 0 22.675 0"/>
                        </svg>
                    </a>
                @endif
                @if(!empty($social['twitter']))
                    <a href="{{ $social['twitter'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-blue-400 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                @endif
                @if(!empty($social['youtube']))
                    <a href="{{ $social['youtube'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a2.994 2.994 0 0 0-2.107-2.117C19.24 3.5 12 3.5 12 3.5s-7.24 0-9.391.569A2.994 2.994 0 0 0 .502 6.186C0 8.338 0 12 0 12s0 3.662.502 5.814a2.994 2.994 0 0 0 2.107 2.117C4.76 20.5 12 20.5 12 20.5s7.24 0 9.391-.569a2.994 2.994 0 0 0 2.107-2.117C24 15.662 24 12 24 12s0-3.662-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                @endif
                @if(!empty($social['instagram']))
                    <a href="{{ $social['instagram'] }}" target="_blank" rel="noopener" class="text-gray-400 hover:text-pink-500 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.974.974 1.246 2.241 1.308 3.608.058 1.266.069 1.646.069 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.974.974-2.241 1.246-3.608 1.308-1.266.058-1.646.069-4.85.069s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.974-.974-1.246-2.241-1.308-3.608C2.175 15.647 2.163 15.267 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608.974-.974 2.241-1.246 3.608-1.308C8.416 2.175 8.796 2.163 12 2.163zm0-2.163C8.741 0 8.332.013 7.052.072 5.775.131 4.602.425 3.635 1.392 2.668 2.359 2.374 3.532 2.315 4.809 2.256 6.089 2.243 6.498 2.243 12c0 5.502.013 5.911.072 7.191.059 1.277.353 2.45 1.32 3.417.967.967 2.14 1.261 3.417 1.32 1.28.059 1.689.072 7.191.072s5.911-.013 7.191-.072c1.277-.059 2.45-.353 3.417-1.32.967-.967 1.261-2.14 1.32-3.417.059-1.28.072-1.689.072-7.191s-.013-5.911-.072-7.191c-.059-1.277-.353-2.45-1.32-3.417C19.45.425 18.277.131 17 .072 15.72.013 15.311 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a3.999 3.999 0 1 1 0-7.998 3.999 3.999 0 0 1 0 7.998zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</footer>
