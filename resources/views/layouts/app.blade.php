<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($seo['title']) ? $seo['title'] : config('app.name') }}</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $seo['description'] ?? config('app.description', 'Your default description') }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
    <meta property="og:url" content="{{ $seo['url'] ?? url()->current() }}">
    <meta property="og:title" content="{{ $seo['title'] ?? config('app.name') }}">
    <meta property="og:description" content="{{ $seo['description'] ?? config('app.description', 'Your default description') }}">
    <meta property="og:image" content="{{ $seo['image'] ?? asset('assets/logo.png') }}">
    
    @if(isset($seo['type']) && $seo['type'] === 'product')
    <!-- Product specific tags -->
    <meta property="product:price:amount" content="{{ $seo['price'] ?? '' }}">
    <meta property="product:price:currency" content="{{ $seo['currency'] ?? 'MMK' }}">
    @endif
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $seo['url'] ?? url()->current() }}">
    <meta property="twitter:title" content="{{ $seo['title'] ?? config('app.name') }}">
    <meta property="twitter:description" content="{{ $seo['description'] ?? config('app.description', 'Your default description') }}">
    <meta property="twitter:image" content="{{ $seo['image'] ?? asset('assets/logo.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <link icon rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- @livewireStyles --}}
    @filamentStyles
    
</head>

<body class="font-sans antialiased  hide-scrollbar">
    <div class="min-h-screen ">
        <div class="fixed inset-x-0 -top-[30%] -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
        aria-hidden="true">
        <div class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#80ceff] to-[#bdbcc9] opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"
            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
        </div>
    </div>

    <livewire:layout.navigation />


    {{ $slot }}
    
    <div class="fixed inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
        aria-hidden="true">
        <div class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr  from-[#80ceff] to-[#bdbcc9] opacity-30 sm:left-[calc(50%+36rem)] sm:w-288.75"
            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
        </div>
    </div>

    <x-footer />
    @livewire('notifications')
    {{-- @filamentScripts --}}
    {{-- @livewireScripts --}}
    <script>
        // Add error handling for Livewire
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('error', (message) => {
                console.error('Livewire Error:', message);
            });
        });
    </script>
</body>

</html>
