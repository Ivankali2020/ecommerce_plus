<div>
    @if ($sliders->isNotEmpty())
        <section class="shop-container pt-4 md:pt-8">
            <div x-data="{
                    slides: @js($sliders->map(fn ($media) => ['src' => $media->getUrl(), 'alt' => $media->name])->values()),
                    current: 0,
                    timer: null,
                    go(index) { this.current = (index + this.slides.length) % this.slides.length },
                    next() { this.go(this.current + 1) },
                    previous() { this.go(this.current - 1) },
                    play() { if (this.slides.length > 1) { this.timer = setInterval(() => this.next(), 5000) } },
                    pause() { clearInterval(this.timer) },
                }"
                x-init="play()"
                @mouseenter="pause()"
                @mouseleave="play()"
                class="group relative overflow-hidden rounded-3xl bg-accent-soft/60">

                <div class="relative aspect-[16/9] w-full md:aspect-[21/8]">
                    <template x-for="(slide, index) in slides" :key="index">
                        <img x-show="current === index"
                            x-transition:enter="transition-opacity duration-700 ease-out"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity duration-700 ease-in"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            :src="slide.src" :alt="slide.alt"
                            class="absolute inset-0 size-full object-cover" />
                    </template>
                </div>

                <template x-if="slides.length > 1">
                    <div>
                        <button type="button" @click="previous()" aria-label="Previous slide"
                            class="absolute left-4 top-1/2 flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-surface/80 text-ink opacity-0 backdrop-blur transition-opacity hover:bg-surface group-hover:opacity-100 max-md:hidden">
                            <x-heroicon-m-chevron-left class="size-5" />
                        </button>
                        <button type="button" @click="next()" aria-label="Next slide"
                            class="absolute right-4 top-1/2 flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-surface/80 text-ink opacity-0 backdrop-blur transition-opacity hover:bg-surface group-hover:opacity-100 max-md:hidden">
                            <x-heroicon-m-chevron-right class="size-5" />
                        </button>

                        <div class="absolute inset-x-0 bottom-4 flex justify-center gap-2 md:bottom-6">
                            <template x-for="(slide, index) in slides" :key="index">
                                <button type="button" @click="go(index)" :aria-label="'Slide ' + (index + 1)"
                                    class="h-0.5 rounded-full bg-white transition-all duration-500"
                                    :class="current === index ? 'w-10 opacity-100' : 'w-5 opacity-50 hover:opacity-80'"></button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </section>
    @endif
</div>
