<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">

    <x-shop.seo-meta
        :seo="$seo ?? []"
        :default-title="config('app.name')"
        :default-description="config('app.description', config('app.name'))"
        :default-image="asset('assets/logo.png')" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <style>[x-cloak] { display: none !important; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
</head>
<body class="hide-scrollbar min-h-screen bg-canvas text-ink antialiased">
    <livewire:layout.navigation />

    <main class="pb-20 sm:pb-0">
        {{ $slot }}
    </main>

    <x-footer />

    @livewire('notifications')
    @filamentScripts
</body>
</html>
