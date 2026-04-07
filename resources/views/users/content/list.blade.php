@extends('users.layouts.app')
@section('meta_description', 'Daftar event yang tersedia di berbagai wilayah, Temukan event menarik di sekitar Anda.')
@section('content')
    <main class="bg-gray-50 py-16">
        <div class="container mx-auto px-4">
            <!-- Title -->
            <div class="flex items-center mb-6">
                <h3 class="text-4xl font-bold text-teal-600">
                    DAFTAR EVENT
                </h3>
                <div class="flex-1 h-px bg-gradient-to-r from-teal-500/20 via-slate-300/50 to-transparent ml-6"></div>
            </div>

            <!-- Info + Filter -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
                <p class="text-slate-600">Menampilkan event berdasarkan
                    <span id="selected-region" class="font-semibold text-teal-600">Semua Wilayah</span>
                </p>

                <!-- Filter Dropdown -->
                <div class="relative inline-block text-left">
                    <button id="region-dropdown-button"
                        class="bg-teal-600 text-white px-6 py-2 rounded-full flex items-center gap-2 hover:bg-teal-700 transition-colors">
                        <i class="fas fa-map-marker-alt"></i>
                        <span id="dropdown-selected-text">PILIH WILAYAH</span>
                        <i class="fas fa-chevron-down text-xs ml-1"></i>
                    </button>

                    <div id="region-dropdown-menu"
                        class="hidden absolute left-0 mt-2 w-56 origin-top-left bg-white rounded-lg shadow-lg ring-1 ring-slate-200 z-50 border border-slate-200">
                        <div class="py-1" role="none">
                            <a href="#"
                                class="region-option block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100"
                                data-region="all">Semua Wilayah</a>
                            @foreach ($locations as $locations)
                                <a href="#"
                                    class="region-option block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100"
                                    data-region="{{ strtoupper($locations->city) }}">{{ strtoupper($locations->city) }}</a>
                            @endforeach
                             
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events Grid -->
            <div id="events-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($events as $event)
                    <div class="event-card card-glass rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-200"
                        data-region="{{ strtoupper($event->EventsLocation->city ?? 'Tidak diketahui') }}">
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

            <!-- Empty State -->
            <div id="no-events-message" class="hidden text-center py-12">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-calendar-times text-5xl text-slate-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-slate-600 mb-2">Tidak Ada Event</h3>
                    <p class="text-slate-500">Maaf, saat ini tidak ada event yang tersedia di wilayah yang dipilih.</p>
                </div>
            </div>
    </main>
@endsection
@section('scripts')
    <script>
        const dropdownButton = document.getElementById('region-dropdown-button');
        const dropdownMenu = document.getElementById('region-dropdown-menu');
        const regionOptions = document.querySelectorAll('.region-option');
        const selectedRegionText = document.getElementById('selected-region');
        const dropdownSelectedText = document.getElementById('dropdown-selected-text');
        const eventCards = document.querySelectorAll('.event-card');
        const eventsContainer = document.getElementById('events-container');
        const noEventsMessage = document.getElementById('no-events-message');

        dropdownButton.addEventListener('click', function() {
            dropdownMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', function(event) {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
        regionOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const region = this.getAttribute('data-region');
                const regionName = this.textContent;
                if (region === 'all') {
                    selectedRegionText.textContent = 'Semua Wilayah';
                    dropdownSelectedText.textContent = 'PILIH WILAYAH';
                } else {
                    selectedRegionText.textContent = regionName;
                    dropdownSelectedText.textContent = regionName;
                }

                let hasEvents = false;

                eventCards.forEach(card => {
                    if (region === 'all' || card.getAttribute('data-region') === region) {
                        card.style.display = 'block';
                        hasEvents = true;
                    } else {
                        card.style.display = 'none';
                    }
                });
                if (hasEvents) {
                    eventsContainer.style.display = 'grid';
                    noEventsMessage.classList.add('hidden');
                } else {
                    eventsContainer.style.display = 'none';
                    noEventsMessage.classList.remove('hidden');
                }
                dropdownMenu.classList.add('hidden');
            });
        });
    </script>
@endsection
