{{--
    Horizontal slider for filter chips. When the chips overflow, it shows prev/next arrows (desktop)
    and a chevron toggle that expands the rail into a wrapped list of every chip.
    The chip marked with `data-active` is scrolled into view on load.
--}}
<div {{ $attributes->class('flex items-start gap-2') }}
    x-data="{
        expanded: false,
        overflowing: false,
        canScrollBack: false,
        canScrollForward: false,
        init() {
            new ResizeObserver(() => this.measure()).observe(this.$refs.track);
            this.$nextTick(() => { this.centerActiveChip(); this.measure(); });
        },
        measure() {
            const track = this.$refs.track;
            if (! this.expanded) {
                this.overflowing = track.scrollWidth > track.clientWidth + 1;
            }
            this.canScrollBack = ! this.expanded && track.scrollLeft > 4;
            this.canScrollForward = ! this.expanded && track.scrollLeft + track.clientWidth < track.scrollWidth - 4;
        },
        slide(direction) {
            this.$refs.track.scrollBy({ left: direction * this.$refs.track.clientWidth * 0.75, behavior: 'smooth' });
        },
        centerActiveChip() {
            const track = this.$refs.track;
            const chip = track.querySelector('[data-active]');
            if (chip) {
                track.scrollLeft = chip.offsetLeft - (track.clientWidth - chip.offsetWidth) / 2;
            }
        },
        toggle() {
            this.expanded = ! this.expanded;
            this.$nextTick(() => { if (! this.expanded) { this.centerActiveChip(); } this.measure(); });
        },
    }">

    <button type="button" x-show="canScrollBack" x-cloak @click="slide(-1)" aria-label="Previous"
        class="hidden size-10 shrink-0 items-center justify-center rounded-full border border-line bg-surface text-ink-soft transition-colors hover:border-ink/40 hover:text-ink sm:flex">
        <x-heroicon-m-chevron-left class="size-4" />
    </button>

    <div class="relative min-w-0 flex-1">
        <div x-ref="track" @scroll.passive="measure()"
            class="relative flex gap-2 scroll-smooth"
            :class="expanded ? 'flex-wrap' : 'hide-scrollbar overflow-x-auto'">
            {{ $slot }}
        </div>

        <div x-show="canScrollBack" x-transition.opacity x-cloak
            class="pointer-events-none absolute inset-y-0 start-0 w-12 bg-linear-to-r from-canvas to-transparent"></div>
        <div x-show="canScrollForward" x-transition.opacity x-cloak
            class="pointer-events-none absolute inset-y-0 end-0 w-12 bg-linear-to-l from-canvas to-transparent"></div>
    </div>

    <button type="button" x-show="canScrollForward" x-cloak @click="slide(1)" aria-label="Next"
        class="hidden size-10 shrink-0 items-center justify-center rounded-full border border-line bg-surface text-ink-soft transition-colors hover:border-ink/40 hover:text-ink sm:flex">
        <x-heroicon-m-chevron-right class="size-4" />
    </button>

    <button type="button" x-show="overflowing" x-cloak @click="toggle()" :aria-expanded="expanded"
        aria-label="{{ __('messages.view_all') }}" title="{{ __('messages.view_all') }}"
        class="flex size-10 shrink-0 items-center justify-center rounded-full border transition-colors"
        :class="expanded ? 'border-ink bg-ink text-canvas' : 'border-line bg-surface text-ink-soft hover:border-ink/40 hover:text-ink'">
        <x-heroicon-m-chevron-down class="size-4 transition-transform duration-300" ::class="expanded && 'rotate-180'" />
    </button>
</div>
