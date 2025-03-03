<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

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
</head>

<body class="antialiased font-jost">
    <div class="hero-pattern left-[80%] bottom-[-40%]  absolute inset-0 -z-20"></div>

    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div class="relative flex flex-col items-center justify-center selection:bg-[slate] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="relative py-16 ">
                    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                        aria-hidden="true">
                        <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#80ceff] to-[#bdbcc9] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
                        </div>
                    </div>

                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <div class="mx-auto max-w-2xl text-center">
                            <div class="mb-8 flex justify-center">
                                <div class="font-jost relative rounded-full px-3 py-1 text-sm leading-6 text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20 dark:text-gray-300 dark:ring-gray-700 dark:hover:ring-gray-600">
                                    {{ __('messages.tagline') }}
                                </div>
                            </div>

                            <div class="flex justify-center">
                                <img class="w-40" src="{{ asset('assets/logo.png')}}" alt="Logo">
                            </div>

                            <!-- Main Heading -->
                            <h1 style="letter-spacing: 1rem"
                                class="text-8xl font-bebas  font-bold tracking-tight text-gray-900 sm:text-6xl bg-gradient-to-r from-slate-500 via-[#c2a03d] to-slate-500 bg-clip-text text-transparent dark:from-slate-400 dark:via-sky-400 dark:to-slate-400">
                                {{ __('messages.title') }}
                            </h1>

                            <!-- Subheading -->
                            <p class="mt-6 font-jost text-md text-gray-600 dark:text-gray-300">
                                {{ __('messages.description') }}
                            </p>

                            <!-- Call to Action Buttons -->
                            <div class="mt-10 flex items-center justify-center gap-x-6">

                                @guest
                                <a href="{{ route('login') }}"
                                    class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300">
                                    {{ __('messages.sign_in') }} <span aria-hidden="true">→</span>
                                </a>
                                @endguest
                            </div>
                        </div>
                    </div>

                    <!-- Bottom decorative element -->
                    <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
                        aria-hidden="true">
                        <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr  from-[#80ceff] to-[#bdbcc9] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"
                            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
                        </div>
                    </div>
                </header>

                <main id="sellers" >
                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                        @foreach ($sellers as $seller)
                            <a href="{{ route('store', $seller->id) }}"
                                class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
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
            </div>
        </div>
    </div>
</body>

</html>
