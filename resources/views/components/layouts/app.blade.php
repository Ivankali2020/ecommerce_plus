<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link icon rel="icon" type="image/png" href="{{ $tenantSellerLogo ?? asset('assets/favicon.ico') }}">

    <title>{{ isset($seo['title']) ? $seo['title'] : $tenantSellerName }}</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $seo['description'] ?? $tenantSellerName }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
    <meta property="og:url" content="{{ $seo['url'] ?? url()->current() }}">
    <meta property="og:title" content="{{ $seo['title'] ?? $tenantSellerName }}">
    <meta property="og:description" content="{{ $seo['description'] ?? $tenantSellerName }}">
    <meta property="og:image" content="{{ $seo['image'] ?? $tenantSellerLogo }}">
    
    @if(isset($seo['type']) && $seo['type'] === 'product')
    <!-- Product specific tags -->
    <meta property="product:price:amount" content="{{ $seo['price'] ?? '' }}">
    <meta property="product:price:currency" content="{{ $seo['currency'] ?? 'MMK' }}">
    @endif
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $seo['url'] ?? url()->current() }}">
    <meta property="twitter:title" content="{{ $seo['title'] ?? $tenantSellerName }}">
    <meta property="twitter:description" content="{{ $seo['description'] ?? $tenantSellerName }}">
    <meta property="twitter:image" content="{{ $seo['image'] ?? $tenantSellerLogo }}">
    

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @filamentStyles
    @vite('resources/css/app.css')

</head>
<body class=' hide-scrollbar'>

    <div class="fixed inset-x-0 -top-[30%] -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
        aria-hidden="true">
        <div class="relative left-[calc(50%-11rem)] aspect-1155/678 w-144.5 -translate-x-1/2 rotate-30 bg-linear-to-tr from-[#80ceff] to-[#bdbcc9] opacity-30 sm:left-[calc(50%-30rem)] sm:w-288.75"
            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
        </div>
    </div>

    <livewire:layout.navigation />

    {{ $slot }}

    @persist('footer')
    <x-footer />
    @endpersist

    <div class="fixed inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
        aria-hidden="true">
        <div class="relative left-[calc(50%+3rem)] aspect-1155/678 w-144.5 -translate-x-1/2 bg-linear-to-tr  from-[#80ceff] to-[#bdbcc9] opacity-30 sm:left-[calc(50%+36rem)] sm:w-288.75"
            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
        </div>
    </div>

    @livewire('notifications')
    {{-- @filamentScripts --}}
    @vite('resources/js/app.js')
</body>
</html>
