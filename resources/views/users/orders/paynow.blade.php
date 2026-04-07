@extends('users.layouts.app')

@section('content')
    <section class="container mx-auto px-4 py-8">
        <div class="mx-auto">
            <!-- Payment Status Header -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-slate-200">
                <!-- Payment Header Layout -->
                <div class="flex flex-col md:flex-row md:items-start md:gap-4 mb-6">
                    <!-- Kolom Kiri -->
                    <div class="flex items-center flex-grow">
                        <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-credit-card text-teal-600 text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800">Pembayaran</h1>
                            <p class="text-slate-600 text-sm">ID Pesanan: #{{ $order->order_number }}</p>
                        </div>
                    </div>

                    <!-- Kolom Kanan (Countdown + Batalkan) -->
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
 
                        <div class="bg-teal-600 rounded-lg p-4 text-center text-white min-w-[220px]">
                            <p class="font-semibold mb-2">Selesaikan pembayaran dalam:</p>
                            <div id="countdown-timer"
                                data-expired="{{ \Carbon\Carbon::parse($transaction->expired_time)->timezone('Asia/Jakarta')->toIso8601String() }}"
                                class="text-2xl font-bold"></div>
                            <p class="text-xs mt-1">Pesanan akan dibatalkan otomatis</p>
                        </div>
 
                    </div>
                </div>




                <!-- Payment Status -->
                <div id="payment-status" class="bg-teal-600 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="loading-spinner mr-3"></div>
                        <div>
                            <p class="font-semibold text-white">Menunggu Pembayaran</p>
                            <p class="text-sm text-white">Silakan lakukan pembayaran sesuai instruksi di bawah</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Payment Instructions -->
                <div class="lg:col-span-2 space-y-8">
                    <div id="payment-instructions"
                        class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200 fade-in">
                        <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                            <i class="fas fa-list-ol text-teal-600 mr-3"></i>
                            Instruksi Pembayaran
                        </h2>

                        @if (str_contains($transaction->payment_method, 'VA'))
                            <!-- Bank Transfer Instructions -->
                            <div id="bank-instructions" class="payment-instruction">
                                <div class="bg-teal-600 p-6 rounded-lg mb-6">
                                    <h3 class="font-bold text-white mb-4 flex items-center">
                                        <i class="fas fa-university text-white mr-2"></i>
                                        Informasi Rekening
                                    </h3>
                                    <div class="bg-white p-4 rounded-lg border border-blue-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="font-semibold text-slate-800">{{ $transaction->payment_name }}</span>
                                        </div>
                                        <p class="text-sm text-slate-600 mb-1">No. Virtual Account:</p>
                                        <div class="flex items-center justify-between">
                                            <span class="font-mono font-bold text-lg">{{ $transaction->pay_code }}</span>
                                            <button onclick="copyToClipboard('{{ $transaction->pay_code }}')"
                                                class="text-teal-600 hover:text-teal-700 transition-colors">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="payment-steps space-y-4">
                                    <h3 class="font-bold text-slate-800 mb-4">Langkah-langkah Pembayaran:</h3>
                                    <div class="payment-step">
                                        <h4 class="font-semibold">Buka aplikasi banking</h4>
                                    </div>
                                    <div class="payment-step">
                                        <h4 class="font-semibold">Pilih Transfer VA</h4>
                                    </div>
                                    <div class="payment-step">
                                        <h4 class="font-semibold">Masukkan nomor VA: <span
                                                class="font-bold text-teal-600">{{ $transaction->pay_code }}</span></h4>
                                    </div>
                                    <div class="payment-step">
                                        <h4 class="font-semibold">Transfer sesuai nominal: <span
                                                class="font-bold text-teal-600">Rp
                                                {{ number_format($transaction->amount, 0, ',', '.') }}</span></h4>
                                    </div>
                                </div>
                            </div>
                        @elseif (str_contains($transaction->payment_method, 'QRIS'))
                            <div id="qris-instructions">
                                <div class="qr-code p-6 rounded-lg mb-6 text-center">
                                    <h3 class="font-bold text-slate-800 mb-4">QRIS Payment</h3>
                                    <img src="{{ $transaction->pay_code }}" alt="QRIS Code"
                                        class="w-100 h-100 mx-auto mb-4">
                                    <p class="text-sm text-slate-600">Scan menggunakan e-wallet apapun</p>
                                </div>
                            </div>
                        @else
                            <!-- E-Wallet Default -->
                            <div id="ewallet-instructions">
                                <div class="qr-code p-6 rounded-lg mb-6 text-center">
                                    <h3 class="font-bold text-slate-800 mb-4">Bayar via E-Wallet</h3>
                                    <a href="{{ $transaction->checkout_url }}" target="_blank"
                                        class="text-blue-600 underline">Klik untuk Bayar</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200 sticky top-24">
                        <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                            <i class="fas fa-file-invoice text-teal-600 mr-3"></i>
                            Ringkasan Pesanan
                        </h2>

                        <!-- Event Info -->
                        <div class="mb-6">
                            <img src="{{ $event->image_path ?? '/storage/events/default.svg' }}" alt="Event"
                                class="w-full object-cover rounded-lg mb-4">
                            <h3 class="font-bold text-slate-800 mb-2">{{ $event->title }}</h3>
                            <div class="space-y-2 text-sm text-slate-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar text-teal-600 w-4"></i>
                                    <span>{{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-teal-600 w-4"></i>
                                    <span>{{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} WIB</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-teal-600 w-4"></i>
                                    <span>{{ $event->EventsLocation->city }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-4 mb-6">
                            @foreach ($orderItems as $item)
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600">{{ $item->ticketType->name }}
                                        ({{ $item->quantity }}x)
                                    </span>
                                    <span class="font-semibold">Rp
                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Biaya Layanan</span>
                                <span class="font-semibold">Rp {{ number_format($order->admin_fee, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">PPN (11%)</span>
                                <span class="font-semibold">Rp {{ number_format($order->ppn_fee, 0, ',', '.') }}</span>
                            </div>
                            <hr class="border-slate-200">
                            <div class="flex justify-between items-center text-lg">
                                <span class="font-bold text-slate-800">Total Pembayaran</span>
                                <span class="font-bold text-teal-600">Rp
                                    {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="bg-slate-50 rounded-lg p-4 mb-6">
                            <h3 class="font-semibold text-slate-800 mb-3 flex items-center">
                                <i class="fas fa-info-circle text-teal-600 mr-2"></i>
                                Status Pembayaran
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

                                $currentStatus = strtoupper($transaction->status);
                            @endphp

                            <div class="flex items-center gap-2">
                                <div
                                    class="w-3 h-3 {{ $statusColors[$currentStatus] ?? 'bg-slate-400' }} rounded-full pulse-animation">
                                </div>
                                <span class="text-sm text-slate-700 font-medium">
                                    {{ $statusLabels[$currentStatus] ?? ucfirst(strtolower($currentStatus)) }}
                                </span>
                            </div>
                        </div>

                        <!-- Help Section -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-800 mb-2 flex items-center">
                                <i class="fas fa-question-circle text-blue-600 mr-2"></i>
                                Butuh Bantuan?
                            </h3>
                            <p class="text-sm text-blue-700 mb-3">Hubungi tim kami</p>
                            <a href="https://wa.me/+62{{ $webConfig->contact_whatsapp ?? '628123131' }}?text=Halo%20admin"
                                target="_blank" class="text-blue-600 hover:underline text-sm">WhatsApp:
                                {{ $webConfig->contact_whatsapp ?? '628123131' }}</a><br>
                            <a href="" class="text-blue-600 hover:underline text-sm">Email:
                                {{ $webConfig->contact_email ?? 'example@gmail.com' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Nomor berhasil disalin!');
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            const timerEl = document.getElementById('countdown-timer');
            const expiredTime = new Date(timerEl.dataset.expired).getTime();

            const timer = setInterval(() => {
                const now = new Date().getTime();
                const distance = expiredTime - now;

                if (distance <= 0) {
                    clearInterval(timer);
                    timerEl.innerHTML = '00:00:00';
                    return;
                }

                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timerEl.innerHTML =
                    `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }, 1000);
        });
    </script>
@endsection
