@extends('users.layouts.app')

@section('content')
    <!-- Main Content -->
    <section class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">

            @if (empty($users->phone))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md mb-4" role="alert">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm mb-2 sm:mb-0">
                            <strong class="font-semibold">Perhatian!</strong> Anda belum mengisi Nomor WhatsApp. Harap isi
                            sebelum melanjutkan agar mendapat update event-event <strong>#RinduTenang</strong>.
                            Terimakasih...
                        </p>
                        <a href="{{ route('users.update.phone.form') }}"
                            class="inline-block mt-2 sm:mt-0 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-4 py-2 rounded-md transition">
                            Isi Sekarang
                        </a>
                    </div>
                </div>
            @endif



            <!-- Header Section -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-slate-200">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-history text-teal-600 text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-slate-800">Riwayat Pesanan: {{ auth()->user()->name }}</h1>
                            <p class="text-slate-600">Kelola dan pantau semua pesanan event Anda</p>
                        </div>
                    </div>

                    <!-- Search -->
                    <div class="relative">
                        <input type="text" id="search-input" placeholder="Cari pesanan..."
                            class="search-input w-full md:w-80 pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:outline-none">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="flex flex-wrap gap-2">
                    <button class="filter-tab active px-6 py-3 rounded-lg font-semibold text-sm" data-filter="all">
                        Semua <span class="ml-2 bg-white/20 px-2 py-1 rounded-full text-xs">{{ $countAll }}</span>
                    </button>
                    <button class="filter-tab px-6 py-3 rounded-lg font-semibold text-sm border border-slate-200"
                        data-filter="success">
                        Berhasil <span
                            class="ml-2 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">{{ $countSuccess }}</span>
                    </button>
                    <button class="filter-tab px-6 py-3 rounded-lg font-semibold text-sm border border-slate-200"
                        data-filter="pending">
                        Pending <span
                            class="ml-2 bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">{{ $countPending }}</span>
                    </button>
                    <button class="filter-tab px-6 py-3 rounded-lg font-semibold text-sm border border-slate-200"
                        data-filter="cancelled">
                        Dibatalkan <span
                            class="ml-2 bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">{{ $countCancelled }}</span>
                    </button>
                </div>

            </div>

            <!-- Orders List -->
            <div id="orders-container" class="space-y-6">
                @if ($orders->isEmpty())
                    <div class="empty-state rounded-2xl p-12 text-center">
                        <h3 class="text-xl font-bold text-slate-800 mb-5">Pesanan Kosong</h3>
                        <a href="{{ route('events.list') }}"
                            class="mt-5 bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition-colors">
                            Tunggu Apa Lagi? Buruan Beli Tiket
                        </a>
                    </div>
                @else
                    @foreach ($orders as $order)
                        @php
                            $statusClass = match ($order->status) {
                                'PAID' => 'success',
                                'UNPAID' => 'pending',
                                'EXPIRED', 'FAILED', 'REFUND' => 'cancelled',
                                default => 'pending',
                            };
                            $event = $order->event;
                            $location = $event->EventsLocation ?? null;
                            $totalQty = $order->items->sum('quantity');
                            $transaction = $order->transaction;
                        @endphp
                        <div class="order-card card-glass rounded-2xl p-6 shadow-lg fade-in"
                            data-status="{{ $statusClass }}" data-search="{{ Str::lower($event->title) }}">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                                <div class="lg:w-1/4">
                                    <img src="{{ asset($event->image_path ?? '/storage/events/default.svg') }}"
                                        alt="Event" class="w-full object-cover rounded-lg">
                                </div>
                                <div class="lg:w-2/4">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h5 class="text-sm font-bold text-slate-800 mb-2">{{ $event->title }}</h5>
                                            <p class="text-sm text-slate-600 mb-3">ID: #{{ $order->order_number }}</p>
                                        </div>
                                        <span
                                            class="status-{{ $statusClass }} px-3 py-1 rounded-full text-xs font-semibold">
                                            <i
                                                class="fas fa-{{ $statusClass == 'success' ? 'check-circle' : ($statusClass == 'pending' ? 'clock' : 'times-circle') }} mr-1"></i>
                                            {{ ucfirst(strtolower($order->status)) }}
                                        </span>
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4 text-sm text-slate-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-calendar text-teal-600 w-4"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-clock text-teal-600 w-4"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($event->end_datetime)->format('H:i') }} WIB</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt text-teal-600 w-4"></i>
                                            <span>{{ $location->city ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-users text-teal-600 w-4"></i>
                                            <span>{{ $totalQty }} Peserta</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="lg:w-1/4 text-right">
                                    <p class="text-2xl font-bold text-teal-600 mb-2">Rp
                                        {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                    <p class="text-sm text-slate-600 mb-4">
                                        @if ($order->status == 'PAID')
                                            Dibayar:
                                            {{ \Carbon\Carbon::parse($order->paid_at)->translatedFormat('d M Y') }}
                                        @elseif ($order->status == 'UNPAID' && $transaction)
                                            Batas:
                                            {{ \Carbon\Carbon::parse($transaction->expired_time)->translatedFormat('d M Y H:i') }}
                                        @else
                                            Status: {{ $order->status }}
                                        @endif
                                    </p>
                                    <div class="space-y-2 pr-2">
                                        @if ($order->status == 'PAID')
                                            <a href="{{ route('tickets.downloads', ['order' => $order->id]) }}"
                                                class="w-full bg-teal-600 text-white py-2 px-4 mr-1 rounded-lg hover:bg-teal-700 transition-colors text-sm">
                                                <i class="fas fa-download mr-2"></i>Download Tiket
                                            </a>
                                            <a href="{{ route('user.orders.details', ['orderID' => $order->id]) }}"
                                                class="w-full bg-slate-100 text-slate-700 py-2 px-4  mr-1 rounded-lg hover:bg-slate-200 transition-colors text-sm">
                                                <i class="fas fa-eye mr-2"></i>Detail
                                            </a>
                                        @elseif ($order->status == 'UNPAID')
                                            <div class="flex flex-row gap-2 text-center">
                                                <a href="{{ route('orders.pay', $order->id) }}"
                                                    class="bg-amber-600 text-white py-2 px-4 rounded-lg hover:bg-amber-700 transition-colors ">
                                                    Bayar Sekarang
                                                </a>
                                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="bg-red-100 text-red-800 py-2 px-4 rounded-lg hover:bg-red-200 transition-colors flex items-center">
                                                        Batal Pesanan
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <button
                                                class="w-full bg-slate-400 text-white py-2 px-4  mr-1 rounded-lg cursor-not-allowed text-sm"
                                                disabled>
                                                <i class="fas fa-ban mr-2"></i>Dibatalkan
                                            </button>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
            <!-- Empty State -->

            <!-- Empty State -->
            <div id="empty-state" class="empty-state rounded-2xl p-12 text-center hidden">
                <div class="w-24 h-24 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-slate-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Tidak ada pesanan ditemukan</h3>
                <p class="text-slate-600 mb-6">Coba ubah filter atau kata kunci pencarian Anda</p>
                <button onclick="clearFilters()"
                    class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('active');
                });

                document.addEventListener('click', function(event) {
                    if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                        mobileMenu.classList.remove('active');
                    }
                });
            }

            // Filter functionality
            const filterTabs = document.querySelectorAll('.filter-tab');
            const orderCards = document.querySelectorAll('.order-card');
            const searchInput = document.getElementById('search-input');
            const emptyState = document.getElementById('empty-state');
            const ordersContainer = document.getElementById('orders-container');

            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');

                    // Update active tab
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // Filter orders
                    filterOrders(filter, searchInput.value);
                });
            });

            // Search functionality
            searchInput.addEventListener('input', function() {
                const activeFilter = document.querySelector('.filter-tab.active').getAttribute(
                    'data-filter');
                filterOrders(activeFilter, this.value);
            });

            function filterOrders(statusFilter, searchTerm) {
                let visibleCount = 0;

                orderCards.forEach(card => {
                    const status = card.getAttribute('data-status');
                    const searchData = card.getAttribute('data-search').toLowerCase();
                    const searchMatch = searchData.includes(searchTerm.toLowerCase());
                    const statusMatch = statusFilter === 'all' || status === statusFilter;

                    if (statusMatch && searchMatch) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide empty state
                if (visibleCount === 0) {
                    ordersContainer.style.display = 'none';
                    emptyState.classList.remove('hidden');
                } else {
                    ordersContainer.style.display = 'block';
                    emptyState.classList.add('hidden');
                }
            }
        });

        function clearFilters() {
            document.getElementById('search-input').value = '';
            document.querySelector('.filter-tab[data-filter="all"]').click();
        }
    </script>
@endsection
