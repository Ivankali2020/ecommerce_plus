<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @livewireStyles
    @filamentStyles
    @vite('resources/css/app.css')

    <title>{{ $title ?? 'Page Title' }}</title>
</head>
<body>
    {{ $slot }}

    @livewire('notifications')
    @livewireScripts
    @filamentScripts
    @vite('resources/js/app.js')
</body>
</html>
