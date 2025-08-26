<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'PADUANG') }}</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ config('app.name', 'PADUANG') }}">
    <meta name="description" content="{{ __('meta.description') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ config('app.name', 'PADUANG') }}">
    <meta property="og:description" content="{{ __('meta.description') }}">
    <meta property="og:image" content="{{ asset('assets/logo.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="{{ config('app.name', 'PADUANG') }}">
    <meta property="twitter:description" content="{{ __('meta.description') }}">
    <meta property="twitter:image" content="{{ asset('assets/logo.png') }}">

    <link icon rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <style>
        .hero-pattern {
            background-image: url('{{ asset('assets/logo.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.03;
        }
    </style>
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>

<body class="antialiased text-black/50 dark:bg-black dark:text-white/50 min-h-svh {{ app()->currentLocale() === 'my' ? 'font-mm' : 'font-jost' }}">
    <x-theme-switcher />
    <div class="hero-pattern left-[80%] bottom-[-40%]  absolute inset-0 -z-20"></div>

    <div class=" ">
        <div class="relative flex flex-col items-center justify-center selection:bg-[slate] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="relative py-16 ">
                    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                        aria-hidden="true">
                        <div class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#80ceff] to-[#bdbcc9] dark:from-slate-400 dark:via-[#c29f37] dark:to-slate-400 opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"
                            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
                        </div>
                    </div>

                    <div class="mx-auto container px-6 lg:px-8 animate-fade-in  ">
                        <div class="mx-auto text-center">
                            <div class="mb-8 flex justify-center">
                                <div class="font-jost relative rounded-full px-3 py-1 text-sm leading-6 text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20 dark:text-gray-300 dark:ring-gray-700 dark:hover:ring-gray-600">
                                    {{ __('messages.tagline') }}
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <img  class="w-80" src="{{ asset('assets/logo.png')}}" alt="{{ __('meta.description') }}">
                            </div>

                            <!-- Main Heading -->
                            <h1  class="text-5xl lg:text-8xl font-bebas mt-2 tracking-wider font-bold  sm:text-6xl bg-linear-to-r from-slate-900 via-[#c29f37] to-slate-900 bg-clip-text text-transparent dark:from-slate-900 dark:via-[#c29f37] dark:to-slate-400">
                                {{ __('messages.title') }}
                            </h1>

                            <!-- Subheading -->
                            <div class="flex items-center justify-center">
                                <p class="mt-6  max-w-2xl  font-jost text-md  dark:text-gray-300" style="line-height: 2rem">
                                    {{ __('messages.main_description') }}
                                </p>
                            </div>


                            <!-- Call to Action Buttons -->
                            <div class="mt-10 flex items-center justify-center gap-x-6 ">
                                <!-- Seller Registration CTA -->
                                <x-primary-link-button :href="route('partner.register')" class="py-2.5">
                                     {{ __('messages.welcome.create_your_store') }}
                                </x-primary-link-button>
                              
                            </div>
                        </div>
                    </div>

                    <!-- Bottom decorative element -->
                    <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
                        aria-hidden="true">
                        <div class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr  from-[#80ceff] to-[#bdbcc9] dark:from-slate-400 dark:via-[#c29f37] dark:to-slate-400 opacity-30 sm:left-[calc(50%+36rem)] sm:w-288.75"
                            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
                        </div>
                    </div>
                </header>

                <!-- Seller Benefits Section -->
                <section class="py-16">
                    <div class="mx-auto max-w-4xl text-center">
                        <h2 class="text-3xl font-bebas tracking-wider font-bold text-black dark:text-white mb-8">
                            {{ __('messages.welcome.why_choose_platform') }}
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="bg-white dark:bg-zinc-900 rounded-lg  p-6 shadow-lg ring-1 ring-black/5 dark:ring-zinc-800">
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-black dark:text-white mb-2">{{ __('messages.welcome.quick_setup') }}</h3>
                                <p class="text-gray-600 dark:text-gray-300   " style="line-height:2rem">{{ __('messages.welcome.quick_setup_description') }}</p>
                            </div>
                            
                            <div class="bg-white dark:bg-zinc-900 rounded-lg  p-6 shadow-lg ring-1 ring-black/5 dark:ring-zinc-800">
                                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                    {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                                    </svg> --}}
                                    <svg fill="#000000" class="h-8 w-8 text-green-600" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 472.615 472.615" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <polygon points="147.25,194.145 133.221,241.875 125.577,217.596 106.788,217.606 99.154,241.875 85.115,194.145 66.231,199.702 89.384,278.471 108.221,278.644 116.183,253.337 124.154,278.654 142.991,278.471 166.134,199.702 "></polygon> </g> </g> <g> <g> <polygon points="267.375,194.145 253.346,241.875 245.702,217.596 226.913,217.596 219.269,241.875 205.24,194.145 186.356,199.702 209.499,278.471 228.336,278.654 236.308,253.337 244.279,278.654 263.116,278.471 286.259,199.702 "></polygon> </g> </g> <g> <g> <polygon points="387.5,194.145 373.461,241.875 365.827,217.606 347.038,217.596 339.394,241.875 325.365,194.145 306.481,199.702 329.624,278.471 348.461,278.654 356.433,253.337 364.394,278.644 383.231,278.471 406.384,199.702 "></polygon> </g> </g> <g> <g> <path d="M441.692,167.385c-9.361-27.847-24.473-53.592-44.195-75.521c-14.009,13.152-29.357,24.377-45.723,33.598 c3.864,13.214,7.028,27.238,9.471,41.923h-19.951c-1.998-11.459-4.451-22.437-7.33-32.884 c-27.338,12.442-57.012,19.574-87.81,20.825v12.06h-19.692v-12.06c-30.798-1.25-60.472-8.383-87.81-20.825 c-2.879,10.447-5.333,21.425-7.33,32.884H111.37c2.443-14.685,5.607-28.709,9.471-41.923c-16.365-9.221-31.713-20.448-45.723-33.6 c-19.735,21.941-34.839,47.687-44.195,75.523H0v137.846h472.615V167.385H441.692z M452.923,285.538H19.692v-98.462h433.231 V285.538z"></path> </g> </g> <g> <g> <path d="M345.502,366.491c-11.776,32.32-27.769,58.784-46.437,77.199c31.25-9.421,59.896-26.088,84.31-48.853 C371.67,383.921,359.005,374.433,345.502,366.491z"></path> </g> </g> <g> <g> <path d="M246.154,305.241v12.051c30.795,1.25,60.47,8.387,87.809,20.832c2.878-10.446,5.333-21.424,7.33-32.883H246.154z"></path> </g> </g> <g> <g> <path d="M361.243,305.241c-2.443,14.686-5.607,28.709-9.472,41.923c16.365,9.221,31.714,20.448,45.725,33.599 c19.733-21.95,34.83-47.693,44.196-75.522H361.243z"></path> </g> </g> <g> <g> <path d="M111.371,305.241H30.923c9.369,27.836,24.473,53.587,44.199,75.519c14.01-13.151,29.356-24.375,45.72-33.595 C116.978,333.95,113.814,319.927,111.371,305.241z"></path> </g> </g> <g> <g> <path d="M127.113,366.491c-13.502,7.942-26.166,17.428-37.867,28.341c24.416,22.768,53.059,39.434,84.3,48.854 C154.879,425.273,138.885,398.809,127.113,366.491z"></path> </g> </g> <g> <g> <path d="M131.321,305.241c1.999,11.459,4.451,22.437,7.33,32.883c27.34-12.445,57.015-19.582,87.81-20.832v-12.051H131.321z"></path> </g> </g> <g> <g> <path d="M144.542,357.315c18.513,53.226,48.514,88.794,81.92,94.692V337.226C197.665,338.509,169.987,345.357,144.542,357.315z"></path> </g> </g> <g> <g> <path d="M246.154,337.226v114.782c33.41-5.897,63.409-41.463,81.92-94.692C302.628,345.357,274.95,338.509,246.154,337.226z"></path> </g> </g> <g> <g> <path d="M89.24,77.789c11.704,10.913,24.369,20.396,37.872,28.34c11.774-32.321,27.769-58.784,46.436-77.2 C142.299,38.351,113.653,55.019,89.24,77.789z"></path> </g> </g> <g> <g> <path d="M299.067,28.929c18.666,18.413,34.661,44.879,46.435,77.2c13.503-7.944,26.168-17.427,37.872-28.34 C358.962,55.019,330.316,38.351,299.067,28.929z"></path> </g> </g> <g> <g> <path d="M246.154,20.608V135.39c28.798-1.282,56.475-8.131,81.919-20.086C309.561,62.073,279.56,26.508,246.154,20.608z"></path> </g> </g> <g> <g> <path d="M144.542,115.303c25.443,11.955,53.121,18.804,81.919,20.086V20.608C193.055,26.508,163.054,62.073,144.542,115.303z"></path> </g> </g> </g></svg>
                                </div>

                                <h3 class="text-xl font-semibold text-black dark:text-white mb-2">{{ __('messages.welcome.custom_domain') }}</h3>
                                <p class="text-gray-600 dark:text-gray-300   " style="line-height:2rem">{{ __('messages.welcome.custom_domain_description') }}</p>
                            </div>
                            
                            <div class="bg-white dark:bg-zinc-900 rounded-lg p-6 shadow-lg ring-1 ring-black/5 dark:ring-zinc-800">
                                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-black dark:text-white mb-2">{{ __('messages.welcome.complete_tools') }}</h3>
                                <p class="text-gray-600 dark:text-gray-300  "  style="line-height:2rem"">{{ __('messages.welcome.complete_tools_description') }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-12">
                            <p class="text-lg text-gray-600 dark:text-gray-300 font-jost mb-6">
                                {{ __('messages.welcome.join_sellers') }}
                            </p>
                            <x-primary-link-button :href="route('partner.register')" class="py-3 inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                {{ __('messages.welcome.start_free_store') }}
                            </x-primary-link-button>
                        </div>
                    </div>
                </section>

                @if($sellers->count() > 0)
                <main id="sellers" >
                    <!-- Existing Stores Section -->
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bebas tracking-wider font-bold text-black dark:text-white mb-4">
                            {{ __('messages.welcome.explore_stores') }}
                        </h2>
                        <p class="text-lg text-gray-600 dark:text-gray-300 font-jost">
                            {{ __('messages.welcome.browse_community') }}
                        </p>
                    </div>
                    
                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8 mb-4 ">
                        @foreach ($sellers as $seller)  
                            {{-- their domain --}}
                            <a href="http://{{ $seller->tenants->first()->domain->domain ?? '#' }}" target="_blank"
                                class="flex items-start gap-4 rounded-lg bg-white p-6 animate-fade-in  shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/5 transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
                                <img src="{{ $seller->getFirstMediaUrl('logos') }}" alt="{{ $seller->name }}"
                                    class="w-16 h-16 rounded-full object-cover">
                                <div>
                                    <h2 class="text-xl font-bebas  font-semibold text-black dark:text-white">
                                        {{ $seller->name }}</h2>
                                    @if ($seller->addresses)
                                        <div class="font-jost  border-gray-300   mt-4 ">
                                            <h2 class=" font-bold mb-2 header_invoice">{{ __('messages.addresses') }}</h2>
                                            @forelse ($seller->addresses as $index=>$address)
                                                <div class="mt-2 text-xs">
                                                    <p class=" text-xs">{{ $address->address }}</p>
                                                </div>
                                            @empty
                                            @endforelse
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </main>
                @endif
            </div>
        </div>
    </div>
    <script>
        // Check initial theme
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>

</html>
