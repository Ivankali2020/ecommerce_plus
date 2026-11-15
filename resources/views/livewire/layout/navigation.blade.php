<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    /**
     * Primary storefront links shown in the header.
     *
     * @return array<int, array{route: string, label: string}>
     */
    public function primaryLinks(): array
    {
        $links = [
            ['route' => 'welcome', 'label' => __('messages.navigation.main_store')],
            ['route' => 'products', 'label' => __('messages.navigation.products')],
            ['route' => 'wholesale', 'label' => __('messages.navigation.wholesale')],
        ];

        if (auth()->check()) {
            $links[] = ['route' => 'gifts', 'label' => __('gifts.gifts')];
        }

        return $links;
    }

    /**
     * Links in the account dropdown (desktop) and slide-down menu (mobile).
     *
     * @return array<int, array{route: string, label: string, icon: string, navigate: bool}>
     */
    public function accountLinks(): array
    {
        return [
            ['route' => 'profile', 'label' => __('messages.auth.profile.title'), 'icon' => 'heroicon-o-user-circle', 'navigate' => true],
            ['route' => 'order', 'label' => __('messages.navigation.order'), 'icon' => 'heroicon-o-document-text', 'navigate' => true],
            ['route' => 'recent.orders', 'label' => __('messages.navigation.recent_orders'), 'icon' => 'heroicon-o-clock', 'navigate' => true],
            ['route' => 'dashboard', 'label' => __('messages.navigation.dashboard'), 'icon' => 'heroicon-o-squares-2x2', 'navigate' => true],
            ['route' => 'delivery', 'label' => __('messages.navigation.delivery'), 'icon' => 'heroicon-o-truck', 'navigate' => true],
            ['route' => 'referral', 'label' => __('referral.title'), 'icon' => 'heroicon-o-share', 'navigate' => false],
            ['route' => 'addresses', 'label' => __('messages.address.addresses_count'), 'icon' => 'heroicon-o-map-pin', 'navigate' => false],
        ];
    }
}; ?>

<div class="sticky top-0 z-30">
    <nav x-data="{ open: false }" @keydown.escape="open = false"
        class="border-b border-line bg-canvas/85 backdrop-blur-md supports-[backdrop-filter]:bg-canvas/70">
        <div class="shop-container flex h-16 items-center justify-between gap-6 md:h-20">

            {{-- Brand --}}
            <a href="{{ route('welcome') }}" wire:navigate class="flex shrink-0 items-center gap-3">
                @if ($tenantSellerLogo ?? null)
                    <img src="{{ $tenantSellerLogo }}" alt="{{ $tenantSellerName ?? '' }}" class="h-10 w-auto rounded-md md:h-12">
                @else
                    <span class="font-display text-2xl font-medium tracking-wide text-ink md:text-3xl">{{ $tenantSellerName ?? config('app.name') }}</span>
                @endif
            </a>

            {{-- Primary links --}}
            <div class="hidden h-full items-center gap-8 sm:flex lg:gap-10">
                @foreach ($this->primaryLinks() as $link)
                    <x-shop.nav-link :href="route($link['route'])" :active="request()->routeIs($link['route'])" wire:navigate>
                        {{ $link['label'] }}
                    </x-shop.nav-link>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1">
                <x-language-switcher />

                @auth
                    <div class="hidden sm:block">
                        <x-dropdown align="right" width="w-64" content-classes="rounded-xl border border-line bg-surface p-1.5 shadow-xl shadow-ink/5">
                            <x-slot name="trigger">
                                <button type="button"
                                    class="flex h-10 items-center gap-2 rounded-full pe-3 ps-1 text-[13px] text-ink-soft transition-colors hover:bg-ink/5 hover:text-ink">
                                    <span class="flex size-8 items-center justify-center rounded-full bg-accent-soft font-display text-sm text-accent-dark">
                                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                    <span class="max-w-28 truncate"
                                        x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                                        x-text="name"
                                        x-on:profile-updated.window="name = $event.detail.name"></span>
                                    <x-heroicon-m-chevron-down class="size-3.5" />
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="border-b border-line px-3 pb-3 pt-2">
                                    <p class="truncate text-sm text-ink">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-xs text-muted">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="py-1.5">
                                    @foreach ($this->accountLinks() as $link)
                                        <x-shop.menu-item :href="route($link['route'])" :icon="$link['icon']" :wire:navigate="$link['navigate']">
                                            {{ $link['label'] }}
                                        </x-shop.menu-item>
                                    @endforeach
                                </div>
                                <div class="border-t border-line pt-1.5">
                                    <x-shop.menu-item wire:click="logout" icon="heroicon-o-arrow-right-start-on-rectangle" danger>
                                        {{ __('messages.auth.logout') }}
                                    </x-shop.menu-item>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <div class="hidden sm:block">
                        <livewire:cart-nav-item />
                    </div>
                @else
                    <div class="hidden items-center gap-2 sm:flex">
                        <x-shop.button variant="ghost" size="sm" :href="route('login')" wire:navigate>
                            {{ __('messages.auth.login') }}
                        </x-shop.button>
                        <x-shop.button size="sm" :href="route('register')" wire:navigate>
                            {{ __('messages.auth.register') }}
                        </x-shop.button>
                    </div>
                @endauth

                {{-- Mobile menu toggle --}}
                <button type="button" @click="open = ! open" :aria-expanded="open"
                    aria-label="{{ __('Menu') }}"
                    class="flex size-10 items-center justify-center rounded-full text-ink-soft transition-colors hover:bg-ink/5 hover:text-ink sm:hidden">
                    <x-heroicon-o-bars-2 x-show="! open" class="size-6" />
                    <x-heroicon-o-x-mark x-show="open" x-cloak class="size-6" />
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-cloak x-collapse class="border-t border-line bg-canvas sm:hidden">
            <div class="shop-container py-4">
                @auth
                    <div class="mb-3 flex items-center gap-3 px-3">
                        <span class="flex size-10 items-center justify-center rounded-full bg-accent-soft font-display text-base text-accent-dark">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm text-ink">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-muted">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <x-shop.menu-item :href="route('gifts')" icon="heroicon-o-gift" wire:navigate>{{ __('gifts.gifts') }}</x-shop.menu-item>
                    @foreach ($this->accountLinks() as $link)
                        <x-shop.menu-item :href="route($link['route'])" :icon="$link['icon']" :wire:navigate="$link['navigate']">
                            {{ $link['label'] }}
                        </x-shop.menu-item>
                    @endforeach

                    <div class="mt-2 border-t border-line pt-2">
                        <x-shop.menu-item wire:click="logout" icon="heroicon-o-arrow-right-start-on-rectangle" danger>
                            {{ __('messages.auth.logout') }}
                        </x-shop.menu-item>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <x-shop.button variant="secondary" :href="route('login')" wire:navigate>{{ __('messages.auth.login') }}</x-shop.button>
                        <x-shop.button :href="route('register')" wire:navigate>{{ __('messages.auth.register') }}</x-shop.button>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Mobile bottom tab bar --}}
    <div class="fixed inset-x-0 bottom-0 z-40 flex h-16 border-t border-line bg-surface/95 pb-[env(safe-area-inset-bottom)] backdrop-blur-md sm:hidden">
        <x-shop.bottom-nav-item :href="route('welcome')" icon="heroicon-o-home"
            :label="__('messages.store')" :active="request()->routeIs('welcome')" />
        <x-shop.bottom-nav-item :href="route('products')" icon="heroicon-o-magnifying-glass"
            :label="__('messages.navigation.products')" :active="request()->routeIs('products')" />
        <x-shop.bottom-nav-item :href="route('wholesale')" icon="heroicon-o-building-storefront"
            :label="__('messages.navigation.wholesale')" :active="request()->routeIs('wholesale')" />
        <livewire:cart-nav-item variant="mobile" />
        <x-shop.bottom-nav-item :href="route('order')" icon="heroicon-o-document-text"
            :label="__('messages.navigation.order')" :active="request()->routeIs('order')" />
        <x-shop.bottom-nav-item :href="route('profile')" icon="heroicon-o-user"
            :label="__('messages.auth.profile.title')" :active="request()->routeIs('profile')" />
    </div>
</div>
