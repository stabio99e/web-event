@extends('users.layouts.app')

@section('content')
    @if ($sliders->isNotEmpty())
        <!-- Hero Slider Section -->
        <section class="container mx-auto px-4 py-8">
            <div class="relative hero-slider bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
                <!-- Slider Items -->
                <div class="relative h-full">
                    
                    @foreach ($sliders as $index => $slide)
                        <div
                            class="slide {{ $index === 0 ? 'slide-active' : 'slide-hidden' }} absolute inset-0 flex items-center p-8 md:p-12">
                            <div class="absolute inset-0 bg-gradient-to-r from-teal-900/70 to-teal-700/70"></div>
                            <img src="{{ asset($slide->image_url) }}" alt="Slide {{ $index + 1 }}"
                                class="absolute inset-0 w-full h-full object-cover">

                            <div class="relative z-10 w-full text-white px-4 md:px-0">
                                <div class="max-w-2xl md:ml-8 mx-auto text-center md:text-left">
                                    @if ($slide->title)
                                        <h2 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                                            {{ $slide->title }}
                                            @if ($slide->subtitle)
                                                <span class="block text-lg md:text-xl mb-5 font-medium text-white mt-1">
                                                    {{ $slide->subtitle }}
                                                </span>
                                            @endif
                                        </h2>
                                    @endif

                                    @if ($slide->buttons)
                                        <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                                            @foreach ($slide->buttons as $btn)
                                                <a href="{{ $btn['link'] }}" target="_blank"
                                                    title="{{ $btn['label'] ?? $btn['icon'] }}"
                                                    class="w-10 h-10 flex items-center justify-center bg-white text-teal-700 rounded-full shadow hover:bg-gray-100 transition-all">
                                                    @switch($btn['icon'])
                                                        @case('instagram')
                                                            <i class="fab fa-instagram text-lg"></i>
                                                        @break

                                                        @case('facebook')
                                                            <i class="fab fa-facebook-f text-lg"></i>
                                                        @break

                                                        @case('youtube')
                                                            <i class="fab fa-youtube text-lg"></i>
                                                        @break

                                                        @case('tiktok')
                                                            <i class="fab fa-tiktok text-lg"></i>
                                                        @break
                                                    @endswitch
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif



                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Navigation Arrows -->
                <button id="prev-slide"
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-teal-600/40 backdrop-blur-sm rounded-full p-3 text-white hover:bg-teal-700 transition-all z-20">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button id="next-slide"
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-teal-600/40 backdrop-blur-sm rounded-full p-3 text-white hover:bg-teal-700 transition-all z-20">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <!-- Indicators -->
                <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2 z-20">
                    @foreach ($sliders as $index => $slide)
                        <button
                            class="indicator w-3 h-3 rounded-full {{ $index === 0 ? 'bg-white/80' : 'bg-white/40' }} hover:bg-white transition-all"
                            data-index="{{ $index }}"></button>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Events Section -->
    <section class="bg-gray-50 py-16">
        <div class="container mx-auto px-4">
            <div class="flex items-center mb-12">
                <h3 class="text-4xl font-bold text-teal-600">
                    DAFTAR EVENT
                </h3>
                <div class="flex-1 h-px bg-gradient-to-r from-teal-500/20 via-slate-300/50 to-transparent ml-6"></div>
            </div>

            <!-- Desktop Grid -->
            <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
                @foreach ($events as $event)
                    <div
                        class="card-glass rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-200">
                        <div class="relative">
                            <img src="{{ asset($event->image_path ?? '/storage/events/default.svg') }}" alt="Event"
                                class="w-full h-auto object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            <div
                                class="absolute top-2 left-2 bg-teal-600 text-white px-2 py-1 rounded text-xs font-semibold">
                                {{ strtoupper($event->EventsLocation->city ?? 'Tidak diketahui') }}
                            </div>
                        </div>

                        <div class="p-6">
                            <a href="{{ route('events.show', $event->slug) }}">
                                <button
                                    class="w-full bg-teal-600 text-white py-3 rounded-lg mb-4 hover:bg-teal-700 transition-colors font-semibold">
                                    Daftar Event Ini
                                </button>
                            </a>

                            <h4 class="font-bold text-slate-800 mb-4 text-lg leading-tight line-clamp-2">
                                {{ $event->title }}
                            </h4>

                            <div class="space-y-3 text-sm text-slate-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar text-teal-600"></i>
                                    <span
                                        class="font-medium">{{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-teal-600"></i>
                                    <span class="font-medium">
                                        {{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($event->end_datetime)->format('H:i') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-teal-600"></i>
                                    <span
                                        class="font-medium">{{ strtoupper($event->EventsLocation->city ?? 'Tidak diketahui') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Mobile Slider -->
            <div class="md:hidden relative mb-12">
                <!-- Navigation Arrows -->
                <button id="scroll-left"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-teal-600 text-white rounded-lg hover:bg-teal-700 backdrop-blur-sm rounded-full p-2 shadow-lg hover:bg-slate-100 transition-all duration-300 hidden">
                    <i class="fas fa-chevron-left text-white-600"></i>
                </button>

                <button id="scroll-right"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-teal-600 text-white rounded-lg hover:bg-teal-700 backdrop-blur-sm rounded-full p-2 shadow-lg hover:bg-slate-100 transition-all duration-300">
                    <i class="fas fa-chevron-right text-white-600"></i>
                </button>

                <!-- Scrollable Container -->
                <div id="events-slider"
                    class="flex gap-4 overflow-x-auto pb-4 scroll-smooth snap-x snap-mandatory scrollbar-hide">
                    <!-- Mobile Event Cards -->
                    @foreach ($events as $event)
                        <div class="snap-start min-w-[280px]">
                            <div
                                class="card-glass rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-200">
                                <div class="relative">
                                    <img src="{{ asset($event->image_path ?? '/storage/events/default.svg') }}"
                                        alt="Event" class="w-full h-48 object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                    <div
                                        class="absolute top-2 left-2 bg-teal-600 text-white px-2 py-1 rounded text-xs font-semibold">
                                        {{ strtoupper($event->EventsLocation->city ?? 'Tidak diketahui') }}
                                    </div>
                                </div>

                                <div class="p-6">
                                    <a href="{{ route('events.show', $event->slug) }}">
                                        <button
                                            class="w-full bg-teal-600 text-white py-3 rounded-lg mb-4 hover:bg-teal-700 transition-colors font-semibold">
                                            Daftar Event Ini
                                        </button>
                                    </a>
                                    <h4 class="font-bold text-slate-800 mb-4 text-lg leading-tight line-clamp-2">
                                        {{ $event->title }}
                                    </h4>

                                    <div class="space-y-3 text-sm text-slate-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-calendar text-teal-600"></i>
                                            <span
                                                class="font-medium">{{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-clock text-teal-600"></i>
                                            <span class="font-medium">
                                                {{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($event->end_datetime)->format('H:i') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt text-teal-600"></i>
                                            <span
                                                class="font-medium">{{ strtoupper($event->EventsLocation->city ?? 'Tidak diketahui') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('events.list') }}">
                    <button
                        class="bg-teal-600 text-white px-10 py-4 rounded-lg hover:bg-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl font-semibold text-lg border border-teal-700">
                        Lihat Semua Event
                    </button>
                </a>
            </div>
        </div>
    </section>

    <!-- Regions Section -->
    <section class="bg-white py-16">
        <div class="container mx-auto px-4">
            <div class="flex items-center mb-12">
                <h3 class="text-4xl font-bold text-teal-600">
                    BERDASARKAN WILAYAH
                </h3>
                <div class="flex-1 h-px bg-gradient-to-r from-teal-500/20 via-slate-300/50 to-transparent ml-6">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                @foreach ($locations as $location)
                    <a href="{{ route('events.list') }}"
                        class="bg-white rounded-2xl p-8 text-center hover:scale-105 transition-all duration-300 cursor-pointer shadow-lg hover:shadow-xl group border border-slate-200 block">
                        <div class="mb-4">
                            <div
                                class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto group-hover:bg-teal-200 transition-all duration-300">
                                <i
                                    class="fas fa-map-marker-alt text-2xl text-teal-600 group-hover:text-teal-700 group-hover:scale-110 transition-all duration-300"></i>
                            </div>
                        </div>
                        <h4 class="text-2xl font-bold mb-3 text-slate-800">{{ strtoupper($location->city) }}</h4>
                        <p class="text-slate-600 leading-relaxed">
                            {{ $location->description ?? 'Lihat seluruh event di ' . strtoupper($location->city) }}
                        </p>
                    </a>
                @endforeach
            </div>

            <div class="text-center">
                <a href="{{ route('events.list') }}">
                    <button
                        class="bg-teal-600 text-white px-10 py-4 rounded-lg hover:bg-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl font-semibold text-lg border border-teal-700">
                        Lihat Semua Wilayah
                    </button>
                </a>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.slide');
            const indicators = document.querySelectorAll('.indicator');
            let currentSlide = 0;

            function showSlide(index) {
                slides.forEach(slide => {
                    slide.classList.remove('slide-active');
                    slide.classList.add('slide-hidden');
                });
                slides[index].classList.remove('slide-hidden');
                slides[index].classList.add('slide-active');
                indicators.forEach(ind => ind.classList.remove('bg-white/50'));
                indicators.forEach(ind => ind.classList.add('bg-white/30'));
                indicators[index].classList.remove('bg-white/30');
                indicators[index].classList.add('bg-white/50');
                currentSlide = index;
            }
            document.getElementById('next-slide').addEventListener('click', function() {
                const nextSlide = (currentSlide + 1) % slides.length;
                showSlide(nextSlide);
            });
            document.getElementById('prev-slide').addEventListener('click', function() {
                const prevSlide = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(prevSlide);
            });
            indicators.forEach(indicator => {
                indicator.addEventListener('click', function() {
                    const slideIndex = parseInt(this.getAttribute('data-index'));
                    showSlide(slideIndex);
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            const eventsSlider = document.getElementById('events-slider');
            const scrollLeftBtn = document.getElementById('scroll-left');
            const scrollRightBtn = document.getElementById('scroll-right');

            function checkScroll() {
                if (eventsSlider) {
                    const {
                        scrollLeft,
                        scrollWidth,
                        clientWidth
                    } = eventsSlider;

                    if (scrollLeft > 0) {
                        scrollLeftBtn.classList.remove('hidden');
                    } else {
                        scrollLeftBtn.classList.add('hidden');
                    }

                    if (scrollLeft < scrollWidth - clientWidth - 10) {
                        scrollRightBtn.classList.remove('hidden');
                    } else {
                        scrollRightBtn.classList.add('hidden');
                    }
                }
            }

            function scroll(direction) {
                if (eventsSlider) {
                    const scrollAmount = 300;
                    const currentScroll = eventsSlider.scrollLeft;
                    const targetScroll = direction === 'left' ?
                        currentScroll - scrollAmount :
                        currentScroll + scrollAmount;

                    eventsSlider.scrollTo({
                        left: targetScroll,
                        behavior: 'smooth'
                    });
                }
            }
            checkScroll();
            if (eventsSlider) {
                eventsSlider.addEventListener('scroll', checkScroll);
            }
            if (scrollLeftBtn) {
                scrollLeftBtn.addEventListener('click', () => scroll('left'));
            }

            if (scrollRightBtn) {
                scrollRightBtn.addEventListener('click', () => scroll('right'));
            }
        });
    </script>
@endsection
