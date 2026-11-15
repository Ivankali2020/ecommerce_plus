@php
    $seller = $tenantSeller ?? null;
    $social = $seller?->setting?->data ?? [];

    $linkGroups = [
        __('messages.footer.quick_links') => [
            ['route' => 'welcome', 'label' => __('messages.navigation.main_store')],
            ['route' => 'products', 'label' => __('messages.navigation.products')],
            ['route' => 'wholesale', 'label' => __('messages.navigation.wholesale')],
            ['route' => 'delivery', 'label' => __('messages.navigation.delivery')],
            ['route' => 'recent.orders', 'label' => __('messages.navigation.recent_orders')],
        ],
        __('messages.footer.support') => [
            ['route' => 'about-us', 'label' => __('messages.footer.about_us')],
            ['route' => 'faq', 'label' => __('messages.footer.faq')],
            ['route' => 'returns-exchanges', 'label' => __('messages.footer.returns')],
            ['route' => 'privacy-policy', 'label' => __('messages.footer.privacy_policy')],
            ['route' => 'terms-conditions', 'label' => __('messages.footer.terms_conditions')],
        ],
    ];

    $socialNetworks = ['facebook', 'twitter', 'youtube', 'instagram'];
@endphp

<footer class="mt-24 border-t border-line bg-surface">
    <div class="shop-container py-14 md:py-20">
        <div class="grid grid-cols-2 gap-10 md:grid-cols-12 md:gap-8">

            {{-- Brand & contact --}}
            <div class="col-span-2 space-y-5 md:col-span-4">
                <p class="font-display text-3xl text-ink">{{ $seller?->name ?? config('app.name') }}</p>

                @if ($seller)
                    <ul class="space-y-2 text-sm text-ink-soft">
                        @if ($seller->phone)
                            <li class="flex items-center gap-3">
                                <x-heroicon-o-phone class="size-4 text-accent" />
                                <a href="tel:{{ $seller->phone }}" class="hover:text-ink">{{ $seller->phone }}</a>
                            </li>
                        @endif
                        @if ($seller->email)
                            <li class="flex items-center gap-3">
                                <x-heroicon-o-envelope class="size-4 text-accent" />
                                <a href="mailto:{{ $seller->email }}" class="hover:text-ink">{{ $seller->email }}</a>
                            </li>
                        @endif
                        @if ($seller->address)
                            <li class="flex items-start gap-3">
                                <x-heroicon-o-map-pin class="mt-0.5 size-4 shrink-0 text-accent" />
                                <span>{{ $seller->address }}</span>
                            </li>
                        @endif
                    </ul>
                @endif

                <x-language-switcher align="up" class="-ms-3" />
            </div>

            {{-- Link columns --}}
            @foreach ($linkGroups as $heading => $links)
                <div class="md:col-span-2">
                    <h3 class="mb-5 text-[11px] font-medium uppercase tracking-[0.22em] text-ink">{{ $heading }}</h3>
                    <ul class="space-y-3">
                        @foreach ($links as $link)
                            <li>
                                <a href="{{ route($link['route']) }}" wire:navigate
                                    class="text-sm text-ink-soft transition-colors hover:text-ink">{{ $link['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            {{-- Payment methods --}}
            <div class="col-span-2 md:col-span-4">
                <h3 class="mb-5 text-[11px] font-medium uppercase tracking-[0.22em] text-ink">{{ __('messages.footer.payment_methods') }}</h3>
                @if ($seller && $seller->payments->isNotEmpty())
                    <div class="flex flex-wrap gap-3">
                        @foreach ($seller->payments as $payment)
                            @if ($payment->media->isNotEmpty())
                                <div class="flex size-14 items-center justify-center rounded-xl border border-line bg-canvas p-2">
                                    <img src="{{ $payment->media->first()->getUrl() }}" alt="{{ $payment->name }}" class="size-full rounded-md object-contain">
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="mt-14 flex flex-col-reverse items-center justify-between gap-6 border-t border-line pt-8 sm:flex-row">
            <a href="https://t.me/aphroditejr" class="text-xs tracking-wide text-muted transition-colors hover:text-ink">
                {{ __('messages.footer.all_rights_reserved') }}
            </a>

            <div class="flex items-center gap-2">
                @foreach ($socialNetworks as $network)
                    @if (! empty($social[$network]))
                        <a href="{{ $social[$network] }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"
                            class="flex size-9 items-center justify-center rounded-full border border-line text-ink-soft transition-colors hover:border-ink hover:text-ink">
                            <x-shop.social-icon :network="$network" class="size-4" />
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</footer>
