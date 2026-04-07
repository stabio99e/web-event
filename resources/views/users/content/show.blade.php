@extends('users.layouts.app')
@section('meta_title', $event->title ?? 'Judul Default')
@section('meta_description', $event->description ?? 'Deskripsi default')
@section('content')
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Event Details Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-slate-200">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Event Poster -->
                <div class="lg:col-span-1">
                    <div class="relative rounded-xl overflow-hidden">
                        <img src="{{ asset($event->image_path ?? '/storage/events/default.svg') }}" alt="{{ $event->title }}"
                            class="w-full h-auto object-cover">
                        @if ($event->EventsLocation && $event->EventsLocation->city)
                            <div
                                class="absolute top-2 left-2 bg-teal-600 text-white px-2 py-1 rounded text-xs font-semibold">
                                {{ $event->EventsLocation->city }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Event Info -->
                <div class="lg:col-span-1">
                    <h2 class="text-3xl font-bold text-slate-800 mb-6">{{ $event->title }}</h2>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-600 mb-2">WAKTU</h3>
                            <p class="text-slate-800 font-medium">
                                {{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y') }}</p>
                            <p class="text-slate-800 font-medium">
                                {{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($event->end_datetime)->format('H:i') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-600 mb-2">LOKASI</h3>
                            <p class="text-slate-800 font-medium">{{ $event->EventsLocation->city ?? 'Online' }}</p>
                        </div>
                    </div>

                </div>

                <!-- Booking Section -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 rounded-xl p-6 border border-slate-200">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">Pilih Tiket</h3>

                        @foreach ($event->ticketTypes as $ticket)
                            <div
                                class="border {{ $ticket->is_premium ? 'border-amber-200 bg-amber-50' : 'border-slate-200 bg-white' }} rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <div class="flex items-center mb-1">
                                            <h4 class="font-semibold text-slate-800">Tiket {{ $ticket->name }}</h4>
                                            @if ($ticket->is_premium)
                                                <span
                                                    class="ml-2 bg-amber-500 text-white text-xs px-2 py-1 rounded-full">PREMIUM</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-slate-600">{{ $ticket->description }}</p>
                                        <p
                                            class="text-lg font-bold {{ $ticket->is_premium ? 'text-amber-600' : 'text-teal-600' }}">
                                            Rp{{ number_format($ticket->price, 0, ',', '.') }}</p>
                                        @if ($ticket->quantity_available > 0)
                                            <p class="text-xs text-slate-500 mt-1">Tersedia:
                                                {{ $ticket->quantity_available }} / {{ $ticket->used_quantity }}
                                            </p>
                                        @else
                                            <p class="text-xs text-red-500 mt-1">Habis</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button
                                            class="ticket-minus w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition-colors"
                                            data-type="{{ Str::slug($ticket->name) }}"
                                            {{ $ticket->quantity_available <= 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-minus text-sm text-slate-700"></i>
                                        </button>
                                        <span id="{{ Str::slug($ticket->name) }}-quantity"
                                            class="text-lg font-semibold w-8 text-center">0</span>
                                        <button
                                            class="ticket-plus w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition-colors"
                                            data-type="{{ Str::slug($ticket->name) }}"
                                            {{ $ticket->quantity_available <= 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-plus text-sm text-slate-700"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Price Summary -->
                        <div class="border-t border-slate-200 pt-4">
                            <div id="price-breakdown" class="space-y-2 mb-4">
                            </div>

                            <div class="flex justify-between items-center mb-6 pt-2 border-t border-slate-200">
                                <span class="text-lg font-bold text-slate-800">Total</span>
                                <span id="total-price" class="text-lg font-bold text-teal-600">Rp0</span>
                            </div>


                            @guest
                                <a href="{{ route('login', ['redirect' => url()->current()]) }}">
                                    <button id="book-ticket-btn"
                                        class="w-full bg-teal-600 text-white py-3 rounded-lg font-semibold hover:bg-teal-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed shadow-lg"
                                        disabled>
                                        Pesan Tiket
                                    </button>
                                </a>
                            @else
                                <form id="booking-form" action="{{ route('orders.create', $event) }}" method="get">
                                    @csrf
                                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                                    @foreach ($event->ticketTypes as $ticket)
                                        <input type="hidden" name="tickets[{{ $ticket->id }}]"
                                            id="ticket-{{ $ticket->id }}-input" value="0">
                                    @endforeach
                                    <button type="button" id="book-ticket-btn"
                                        class="w-full bg-teal-600 text-white py-3 rounded-lg font-semibold hover:bg-teal-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed shadow-lg"
                                        disabled>
                                        Pesan Tiket
                                    </button>
                                </form>
                            @endguest

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Venue Section -->
        @if ($event->EventsLocation)
            <div class="bg-teal-100 rounded-2xl p-6 mb-8 border border-teal-200">
                <h3 class="text-lg font-bold text-slate-800 mb-4">TEMPAT</h3>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <span class="text-slate-800 font-medium block">{{ $event->EventsLocation->name }}</span>
                        <span class="text-slate-600 text-sm block">{{ $event->EventsLocation->address }}</span>
                        @if ($event->EventsLocation->city)
                            <span class="text-slate-600 text-sm">{{ $event->EventsLocation->city }},
                                {{ $event->EventsLocation->province }}</span>
                        @endif
                    </div>
                    @if ($event->EventsLocation->map_url)
                        <a href="{{ $event->EventsLocation->map_url }}" target="_blank"
                            class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors flex items-center shadow-lg">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Google Maps
                        </a>
                    @endif
                </div>
            </div>
        @endif


        @if ($event->safe_content)
            <!-- Event Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-slate-200 ck-content">
                {!! $event->safe_content !!}
            </div>
        @endif


        <!-- FAQ Section  -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-slate-200">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">Pertanyaan yang Sering ditanyakan</h3>

            <div class="space-y-4">

                @if (!empty($getQna))
                    @foreach ($getQna as $rows)
                        <!-- FAQ Item -->
                        <div x-data="{ open: false }" class="border-b border-slate-200 pb-4">
                            <button @click="open = !open" class="flex justify-between items-center w-full text-left group">
                                <span class="font-medium text-slate-800 group-hover:text-teal-600 transition-colors">
                                    {{ $rows->question }}
                                </span>
                                <i :class="open ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                                    class="fas text-slate-400 group-hover:text-teal-600 transition-colors"></i>
                            </button>
                            <div x-show="open" x-transition class="mt-2 text-slate-600">
                                {!! $rows->safe_content !!}
                            </div>
                        </div>
                    @endforeach
                @else
                @endif

            </div>
        </div>

        <!-- Recommended Events -->
        @if ($recommendedEvents->count() > 0)
            <div class="mb-8">
                <div class="flex items-center mb-8">
                    <h3 class="text-4xl font-bold text-teal-600">
                        DAFTAR EVENT
                        {{ strtoupper($event->EventsLocation->city ?? 'LOKASI INI') }}</h3>
                    <div class="flex-1 h-px bg-gradient-to-r from-teal-500/20 via-slate-300/50 to-transparent ml-6"></div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($recommendedEvents as $recommended)
                        <div
                            class="card-glass rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-200">
                            <div class="relative">
                                <img src="{{ asset($recommended->image_path ?? '/storage/events/default.svg') }}"
                                    alt="{{ $recommended->title }}" class="w-full h-64 object-cover">
                                @if ($recommended->EventsLocation && $recommended->EventsLocation->city)
                                    <div
                                        class="absolute top-2 left-2 bg-teal-600 text-white px-2 py-1 rounded text-xs font-semibold">
                                        {{ $recommended->EventsLocation->city }}
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <a href="{{ route('events.show', $recommended->slug) }}"
                                    class="w-full bg-teal-600 text-white py-3 rounded-lg mb-4 hover:bg-teal-700 transition-colors font-semibold shadow-lg block text-center">
                                    Daftar Event Ini
                                </a>
                                <h4 class="font-bold text-slate-800 mb-4 text-lg leading-tight line-clamp-2">
                                    {{ $recommended->title }}</h4>
                                <div class="text-sm text-slate-600 space-y-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-teal-600"></i>
                                        <span
                                            class="font-medium">{{ \Carbon\Carbon::parse($recommended->start_datetime)->translatedFormat('d F Y') }}</span>

                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock text-teal-600"></i>
                                        <span
                                            class="font-medium">{{ \Carbon\Carbon::parse($recommended->start_datetime)->format('H:i') }}
                                            - {{ \Carbon\Carbon::parse($recommended->end_datetime)->format('H:i') }}</span>
                                    </div>
                                    @if ($recommended->EventsLocation)
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt text-teal-600"></i>
                                            <span class="font-medium">{{ $recommended->EventsLocation->city }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </main>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tickets = {};
            const ticketPrices = {};
            const ticketIds = {};

            // Inisialisasi tiket
            @foreach ($event->ticketTypes as $ticket)
                ticketPrices['{{ Str::slug($ticket->name) }}'] = {{ $ticket->price }};
                ticketIds['{{ Str::slug($ticket->name) }}'] = {{ $ticket->id }};
                tickets['{{ Str::slug($ticket->name) }}'] = 0;
            @endforeach

            // Event listener untuk tombol +/-
            document.querySelectorAll('.ticket-plus').forEach(button => {
                button.addEventListener('click', function() {
                    const type = this.getAttribute('data-type');
                    const qtyElement = document.getElementById(`${type}-quantity`);
                    if (!qtyElement) return;

                    let qty = parseInt(qtyElement.textContent);
                    qty++;
                    qtyElement.textContent = qty;
                    tickets[type] = qty;
                    updateSummary();
                });
            });

            document.querySelectorAll('.ticket-minus').forEach(button => {
                button.addEventListener('click', function() {
                    const type = this.getAttribute('data-type');
                    const qtyElement = document.getElementById(`${type}-quantity`);
                    if (!qtyElement) return;

                    let qty = parseInt(qtyElement.textContent);
                    qty = Math.max(0, qty - 1);
                    qtyElement.textContent = qty;
                    tickets[type] = qty;
                    updateSummary();
                });
            });

            function updateSummary() {
                const priceBreakdown = document.getElementById('price-breakdown');
                const totalPrice = document.getElementById('total-price');
                const bookButton = document.getElementById('book-ticket-btn');

                let total = 0;
                let html = '';

                for (const [type, qty] of Object.entries(tickets)) {
                    if (qty > 0) {
                        const price = ticketPrices[type];
                        const subtotal = price * qty;
                        total += subtotal;

                        html += `
                    <div class="flex justify-between">
                        <span>${type.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase())} x${qty}</span>
                        <span>Rp${subtotal.toLocaleString('id-ID')}</span>
                    </div>
                `;
                    }
                }

                if (priceBreakdown) priceBreakdown.innerHTML = html;
                if (totalPrice) totalPrice.textContent = `Rp${total.toLocaleString('id-ID')}`;
                if (bookButton) bookButton.disabled = total <= 0;
            }

            // Submit form jika tombol tersedia
            const bookButton = document.getElementById('book-ticket-btn');
            if (bookButton) {
                bookButton.addEventListener('click', function() {
                    for (const [type, qty] of Object.entries(tickets)) {
                        const id = ticketIds[type];
                        const input = document.getElementById(`ticket-${id}-input`);
                        if (input) {
                            input.value = qty;
                        }
                    }
                    document.getElementById('booking-form').submit();
                });
            }
        });
    </script>
@endsection
