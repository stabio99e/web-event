@extends('users.layouts.app')

@section('content')
    <!-- Progress Steps -->
    <section class="container mx-auto px-4 py-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-center space-x-4 md:space-x-8">
                <div class="flex items-center">
                    <div
                        class="step-indicator completed w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm border-2 border-teal-600 bg-teal-600 text-white">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium text-slate-700 hidden md:block">Rincian Pesanan</span>
                </div>
                <div class="w-8 md:w-16 h-0.5 bg-teal-500"></div>
                <div class="flex items-center">
                    <div
                        class="step-indicator completed w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm border-2 border-teal-600 bg-teal-600 text-white">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="ml-2 text-sm font-medium text-slate-700 hidden md:block">Pembayaran</span>
                </div>
                <div class="w-8 md:w-16 h-0.5 bg-teal-500"></div>
                <div class="flex items-center">
                    <div
                        class="step-indicator active w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm border-2 border-teal-600">
                        3
                    </div>
                    <span class="ml-2 text-sm font-medium text-slate-700 hidden md:block">Selesai</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Page 3: Sukses Pembayaran -->
    <div id="page-3" class="page active">
        <section class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Success Message -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-slate-200 text-center">
                    <div class="success-animation mb-6">
                        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check text-green-600 text-4xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold text-slate-800 mb-2">Pembayaran Berhasil!</h1>
                        <p class="text-slate-600 text-lg">Terima kasih, pesanan Anda telah dikonfirmasi</p>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-receipt text-green-600 text-2xl mr-3"></i>
                            <div>
                                <p class="font-bold text-green-800">ID Pesanan: {{ $order->order_number }}</p>
                                <p class="text-green-700 text-sm">Tanggal:
                                    {{ $order->paid_at->translatedFormat('l, d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mb-8">
                        <a href="{{ route('tickets.downloads', $order->id) }}"
                            class="bg-teal-600 text-white py-3 px-6 rounded-lg hover:bg-teal-700 transition-colors font-semibold flex items-center justify-center gap-2">
                            <i class="fas fa-download"></i>
                            Download E-Ticket
                        </a>
                        <a href="{{ $order->event->url_group ?? '#'}}"
                            class="bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors font-semibold flex items-center justify-center gap-2">
                            <i class="fas fa-envelope"></i>
                            Gabung Ke Grup Sosmed
                    </a>
                    </div>
                </div>

                <div class="grid lg:grid-cols-3 gap-8">
                    <!-- Event & Ticket Details -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Event Info -->
                        <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
                            <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                                <i class="fas fa-calendar-alt text-teal-600 mr-3"></i>
                                Detail Event
                            </h2>

                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="md:w-1/3">
                                    <img src="{{ asset($order->event->image_path ?? '/storage/events/default.svg') }}"
                                        alt="{{ $order->event->title }}" class="w-full h-48 object-cover rounded-lg">
                                </div>
                                <div class="md:w-2/3">
                                    <h3 class="text-xl font-bold text-slate-800 mb-4">{{ $order->event->title }}</h3>

                                    <div class="space-y-3 text-slate-600">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-calendar text-teal-600 w-5"></i>
                                            <span><strong>Tanggal:</strong>
                                                {{ \Carbon\Carbon::parse($order->event->start_datetime)->translatedFormat('l, d F Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-clock text-teal-600 w-5"></i>
                                            <span><strong>Waktu:</strong>
                                                {{ \Carbon\Carbon::parse($order->event->start_datetime)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($order->event->end_datetime)->format('H:i') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-map-marker-alt text-teal-600 w-5"></i>
                                            <span><strong>Lokasi:</strong> {{ $order->event->EventsLocation->city }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Details -->
                        <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
                            <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                                <i class="fas fa-ticket-alt text-teal-600 mr-3"></i>
                                Detail Tiket
                            </h2>

                            <div class="space-y-4">
                                @foreach ($order->tickets->groupBy('order_item_id') as $orderItemId => $tickets)
                                    @php
                                        $orderItem = $order->items->firstWhere('id', $orderItemId);
                                    @endphp
                                    <div class="border border-slate-200 rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <span
                                                class="font-semibold text-slate-800">{{ $orderItem->ticketType->name }}</span>
                                            <span class="text-teal-600 font-bold">{{ $orderItem->quantity }}x</span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm text-slate-600">
                                            <span>Rp{{ number_format($orderItem->price, 0, ',', '.') }} per tiket</span>
                                            <span
                                                class="font-semibold">Rp{{ number_format($orderItem->price * $orderItem->quantity, 0, ',', '.') }}</span>
                                        </div>

                                        <div class="mt-4">
                                            <h4 class="font-medium text-slate-800 mb-2">Peserta:</h4>
                                            <ul class="space-y-2">
                                                @foreach ($tickets as $ticket)
                                                    <li class="text-sm text-slate-600">
                                                        {{ $ticket->attendee_name }}
                                                        @if ($ticket->attendee_email)
                                                            ({{ $ticket->attendee_email }})
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200 sticky top-24">
                            <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                                <i class="fas fa-file-invoice text-teal-600 mr-3"></i>
                                Rincian Pembayaran
                            </h2>

                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600">Subtotal Tiket</span>
                                    <span
                                        class="font-semibold">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600">Biaya Layanan</span>
                                    <span
                                        class="font-semibold">Rp{{ number_format($order->admin_fee, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600">PPN (11%)</span>
                                    <span class="font-semibold">Rp{{ number_format($order->ppn_fee, 0, ',', '.') }}</span>
                                </div>
                                <hr class="border-slate-200">
                                <div class="flex justify-between items-center text-lg">
                                    <span class="font-bold text-slate-800">Total Dibayar</span>
                                    <span
                                        class="font-bold text-teal-600">Rp{{ number_format($order->TotalPayAmount + $order->admin_fee, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="bg-slate-50 rounded-lg p-4 mb-6">
                                <h3 class="font-semibold text-slate-800 mb-3 flex items-center">
                                    <i class="fas fa-credit-card text-teal-600 mr-2"></i>
                                    Metode Pembayaran
                                </h3>
                                @php
                                    $statusLabels = [
                                        'PAID' => 'Pembayaran Berhasil',
                                        'UNPAID' => 'Menunggu Pembayaran',
                                        'FAILED' => 'Pembayaran Gagal',
                                        'EXPIRED' => 'Pembayaran Kadaluarsa',
                                        'REFUND' => 'Dana Dikembalikan',
                                    ];

                                    $statusColors = [
                                        'PAID' => 'bg-green-500',
                                        'UNPAID' => 'bg-amber-500',
                                        'FAILED' => 'bg-red-500',
                                        'EXPIRED' => 'bg-gray-500',
                                        'REFUND' => 'bg-blue-500',
                                    ];

                                    $currentStatus = strtoupper($order->status);
                                @endphp
                                <p class="text-slate-600 text-sm">Status:
                                <div
                                    class="w-3 h-3 {{ $statusColors[$currentStatus] ?? 'bg-slate-400' }} rounded-full pulse-animation">
                                </div>
                                <span class="text-sm text-slate-700 font-medium">
                                    {{ $statusLabels[$currentStatus] ?? ucfirst(strtolower($currentStatus)) }}
                                </span>
                                </p>
                            </div>

                            <div class="space-y-3">
                                <a href="{{ route('dashboard') }}"
                                    class="w-full bg-teal-600 text-white py-3 rounded-lg hover:bg-teal-700 transition-colors font-semibold text-center block">
                                    Lihat Pesanan Lain
                                </a>
                                <a href="{{ route('events.list') }}"
                                    class="w-full bg-slate-100 text-slate-700 py-3 rounded-lg hover:bg-slate-200 transition-colors font-semibold text-center block">
                                    Pesan Event Lain
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
