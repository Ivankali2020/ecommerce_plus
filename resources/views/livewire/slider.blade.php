<div class="z-0 relative">

    @if($sliders->count() > 0)
    <div class="px-4 lg:p-8 container mx-auto relative z-0" >
        <div x-data="{
            slides: {{ json_encode($sliders->map(function($media) {
                return [
                    'imgSrc' => $media->getUrl(),
                    'imgAlt' => $media->name,
                ];
            })) }},
            currentSlideIndex: 1,
            previous() {
                if (this.currentSlideIndex > 1) {
                    this.currentSlideIndex = this.currentSlideIndex - 1
                } else {
                    this.currentSlideIndex = this.slides.length
                }
            },
            next() {
                if (this.currentSlideIndex < this.slides.length) {
                    this.currentSlideIndex = this.currentSlideIndex + 1
                } else {
                    this.currentSlideIndex = 1
                }
            },
            startAutoPlay() {
                setInterval(() => this.next(), 3000); // Auto-advance every 5 seconds
            }
        }"
        x-init="startAutoPlay"
        class="relative w-full overflow-hidden rounded-lg z-0">

            <!-- Previous/Next buttons (only show if more than one slide) -->
            <template x-if="slides.length > 1">
                <div>
                    <!-- previous button -->
                    <button type="button"
                        class="absolute left-2 md:left-5 text-white top-1/2 z-10 flex rounded-full -translate-y-1/2 items-center justify-center bg-black/20 p-1.5 md:p-2 hover:bg-black/40 transition-colors duration-200"
                        aria-label="previous slide"
                        x-on:click="previous()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="3" class="size-4 md:size-6 pr-0.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>

                    <!-- next button -->
                    <button type="button"
                        class="absolute right-2 md:right-5 text-white top-1/2 z-10 flex rounded-full -translate-y-1/2 items-center justify-center bg-black/20 p-1.5 md:p-2 hover:bg-black/40 transition-colors duration-200"
                        aria-label="next slide"
                        x-on:click="next()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="3" class="size-4 md:size-6 pl-0.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </template>

            <!-- slides min-h-[15svh] lg:min-h-[40svh] -->
            <div class="relative  w-full min-h-[15svh] sm:min-h-[25svh] md:min-h-[30svh] lg:min-h-[44svh] 2xl:min-h-[50svh] ">
                <template x-for="(slide, index) in slides" :key="index">
                    <div
                        x-show="currentSlideIndex == index + 1"
                        class="absolute inset-0 "
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        >
                        <img
                            class=" rounded-lg w-full h-full inset-0 object-contain rounded-lg "
                            :src="slide.imgSrc"
                            :alt="slide.imgAlt"
                            loading="lazy" />
                    </div>
                </template>
            </div>

            <!-- indicators (only show if more than one slide) -->
            <template x-if="slides.length > 1">
                <div class="absolute bottom-3 md:bottom-5 left-1/2 z-0 flex -translate-x-1/2 gap-2 px-2 py-1 bg-black/20 backdrop-blur-sm rounded-full">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button
                            class="size-1 md:size-2 rounded-full transition-colors duration-200"
                            :class="currentSlideIndex === index + 1 ? 'bg-white' : 'bg-white/50 hover:bg-white/75'"
                            x-on:click="currentSlideIndex = index + 1"
                            x-bind:aria-label="'slide ' + (index + 1)"></button>
                    </template>
                </div>
            </template>
        </div>
    </div>
    @endif
</div>
