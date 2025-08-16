<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>POS</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @filamentStyles
        @vite('resources/css/app.css')

        <!-- Add custom styles for better mobile experience -->
        <style>
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
            
            .touch-manipulation {
                touch-action: manipulation;
            }
            
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            
            /* Ensure mobile responsiveness */
            @media (max-width: 1024px) {
                .cart-mobile {
                    width: 100%;
                    max-width: 400px;
                }
            }
        </style>
       
    </head>
    <body class="antialiased font-jost">
        {{ $slot }}

        @livewire('notifications')
        {{-- @livewireScripts --}}
        @filamentScripts
        @vite('resources/js/app.js')
    </body>
</html>
