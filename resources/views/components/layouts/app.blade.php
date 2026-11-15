<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ ($tenantSellerLogo ?? null) ?: asset('assets/favicon.ico') }}">

    <x-shop.seo-meta
        :seo="$seo ?? []"
        :default-title="$tenantSellerName ?? config('app.name')"
        :default-image="$tenantSellerLogo ?? null" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <style>[x-cloak] { display: none !important; }</style>
    @filamentStyles
    @vite('resources/css/app.css')
</head>
<body class="hide-scrollbar min-h-screen bg-canvas text-ink antialiased">
    <livewire:layout.navigation />

    <main class="pb-20 sm:pb-0">
        {{ $slot }}
    </main>

    @persist('footer')
        <x-footer />
    @endpersist

    @livewire('notifications')
    @filamentScripts
    @vite('resources/js/app.js')
</body>
</html>
