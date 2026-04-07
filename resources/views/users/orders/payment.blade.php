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
                        class="step-indicator active w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm border-2 border-teal-600">
                        2
                    </div>
                    <span class="ml-2 text-sm font-medium text-slate-700 hidden md:block">Pembayaran</span>
                </div>
                <div class="w-8 md:w-16 h-0.5 bg-slate-300"></div>
                <div class="flex items-center">
                    <div
                        class="step-indicator w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm border-2 border-slate-300 bg-slate-100 text-slate-500">
                        3
                    </div>
                    <span class="ml-2 text-sm font-medium text-slate-500 hidden md:block">Selesai</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Page 2: Pilih Metode Pembayaran -->
    <div id="page-2" class="page active">
        <section class="container mx-auto px-4 py-8">
            @if (session('error'))
                <div class="bg-teal-100 text-teal-800 px-4 py-2 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="max-w-4xl mx-auto">
                <div class="grid lg:grid-cols-3 gap-8">
                    <!-- Payment Methods -->
                    <div class="lg:col-span-2">
                        <form method="POST" action="{{ route('orders.process-payment', ['event' => $event->id]) }}"
                            id="payment-form">
                            @csrf
                            <input type="hidden" name="channel_code" id="channel-code" value="">

                            <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
                                <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                                    <i class="fas fa-credit-card text-teal-600 mr-3"></i>
                                    Pilih Metode Pembayaran
                                </h2>

                                @php
                                    $groupLabels = [];

                                    foreach ($channelsByGroup as $group => $channels) {
                                        $groupLabels[$group] = [
                                            'label' => match ($group) {
                                                'VA' => 'Virtual Account',
                                                'Ewallet' => 'E-Wallet',
                                                'Qris' => 'QRIS',
                                                default => ucfirst($group),
                                            },
                                            'desc' => implode(', ', $channels->pluck('channel_name')->toArray()),
                                            'icon' => match ($group) {
                                                'VA' => 'fas fa-university',
                                                'Ewallet' => 'fas fa-mobile-alt',
                                                'Qris' => 'fas fa-qrcode',
                                                default => 'fas fa-money-check-alt',
                                            },
                                            'color' => match ($group) {
                                                'VA' => 'blue',
                                                'Ewallet' => 'green',
                                                'Qris' => 'red',
                                                default => 'gray',
                                            },
                                        ];
                                    }
                                @endphp


                                @foreach ($channelsByGroup as $group => $channels)
                                    @php
                                        $info = $groupLabels[$group] ?? [
                                            'label' => ucfirst($group),
                                            'desc' => implode(', ', $channels->pluck('channel_name')->toArray()),
                                            'icon' => 'fas fa-money-check-alt',
                                            'color' => 'gray',
                                        ];
                                    @endphp

                                    <div x-data="{ open: false }" class="border-2 border-slate-200 rounded-lg mb-4">
                                        <button @click="open = !open" type="button"
                                            class="w-full flex items-center justify-between px-6 py-4 text-left cursor-pointer hover:bg-slate-50">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-12 h-12 bg-{{ $info['color'] }}-100 rounded-lg flex items-center justify-center">
                                                    <i
                                                        class="{{ $info['icon'] }} text-{{ $info['color'] }}-600 text-xl"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-slate-800">{{ $info['label'] }}</h4>
                                                    <p class="text-slate-600 text-sm">{{ $info['desc'] }}</p>
                                                </div>
                                            </div>
                                            <i class="fas fa-chevron-down text-slate-400"
                                                :class="{ 'rotate-180': open }"></i>
                                        </button>

                                        <div x-show="open" x-collapse class="border-t border-slate-200 bg-slate-50">
                                            @foreach ($channels as $channel)
                                                <label
                                                    class="flex items-center justify-between px-6 py-4 hover:bg-white cursor-pointer">
                                                    <div class="flex items-center gap-3">
                                                        <input type="radio" name="channel_code"
                                                            value="{{ $channel->channel_code }}"
                                                            class="w-5 h-5 text-teal-600" required
                                                            onclick="selectPayment('{{ $group }}', '{{ $channel->channel_code }}')">

                                                        <span
                                                            class="text-slate-700 font-medium">{{ $channel->channel_name }}</span>
                                                    </div>
                                                    <span class="text-sm text-slate-500">
                                                        @if (in_array($group, ['Ewallet', 'Qris']))
                                                            Biaya: Rp{{ number_format($channel->biaya_flat, 0, ',', '.') }}
                                                            + {{ $channel->biaya_percent }}%
                                                        @else
                                                            Biaya: Rp{{ number_format($channel->biaya_flat, 0, ',', '.') }}
                                                        @endif
                                                    </span>

                                                </label>
                                            @endforeach

                                        </div>
                                    </div>
                                @endforeach

                            </div>
                            <div class="mt-8">
                                <button type="submit" id="pay-button"
                                    class="w-full bg-teal-600 text-white py-4 rounded-lg hover:bg-teal-700 transition-colors font-semibold text-lg">
                                    Bayar Sekarang
                                </button>
                            </div>
                    </div>
                    </form>


                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200 sticky top-24">
                            <h5 class="text-2xl font-bold text-slate-800 mb-6">Ringkasan Pesanan</h5>

                            <div class="space-y-4 mb-6">
                                @foreach ($order->items as $item)
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">{{ $item->ticketType->name }}
                                            ({{ $item->quantity }}x)
                                        </span>
                                        <span
                                            class="font-semibold">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach

                                <div id="admin-fee-section" class="flex justify-between items-center">
                                    <span class="text-slate-600">Biaya Admin</span>
                                    <span id="admin-fee-amount" class="font-semibold"></span>
                                </div>

                                <div id="ppn-section" class="flex justify-between items-center">
                                    <span class="text-slate-600">PPN (11%)</span>
                                    <span id="ppn-amount" class="font-semibold"></span>
                                </div>

                                <hr class="border-slate-200">
                                <div class="flex justify-between items-center text-lg">
                                    <span class="font-bold text-slate-800">Total</span>
                                    <span id="grand-total" class="font-bold text-teal-600">
                                        Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Event details remain the same -->
                            <div class="bg-slate-50 rounded-lg p-4">
                                <h3 class="font-semibold text-slate-800 mb-2">Detail Event</h3>
                                <p class="text-slate-600 text-sm">{{ $order->event->title }}</p>
                                <p class="text-slate-600 text-sm">
                                    {{ \Carbon\Carbon::parse($order->event->start_datetime)->translatedFormat('l, d F Y') }}
                                </p>
                                <p class="text-slate-600 text-sm">
                                    {{ $order->event->EventsLocation->name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        const channelsData = {
            @foreach ($channelsByGroup as $group => $channels)
                '{{ $group }}': [
                    @foreach ($channels as $channel)
                        {
                            channel_code: '{{ $channel->channel_code }}',
                            biaya_flat: {{ $channel->biaya_flat }},
                            biaya_percent: {{ $channel->biaya_percent }},
                            ppn: {{ $channel->ppn ?? 0 }},
                        },
                    @endforeach
                ],
            @endforeach
        };

        function selectPayment(group, channelCode) {
            const selectedChannel = channelsData[group].find(c => c.channel_code === channelCode);
            if (!selectedChannel) return;

            const subtotal = {{ $order->total_amount }};
            const flat = selectedChannel.biaya_flat;

            const percent = ['Ewallet', 'Qris'].includes(group) ? selectedChannel.biaya_percent : 0;
            const ppnRate = selectedChannel.ppn ?? 0;

            const adminFee = flat + Math.round(subtotal * (percent / 100));
            const ppnValue = Math.round(adminFee * (ppnRate / 100));
            const grandTotal = subtotal + adminFee + ppnValue;

            document.getElementById('channel-code').value = selectedChannel.channel_code;

            document.getElementById('admin-fee-amount').textContent = `Rp${adminFee.toLocaleString('id-ID')}`;
            document.getElementById('ppn-amount').textContent = `Rp${ppnValue.toLocaleString('id-ID')}`;
            document.getElementById('grand-total').textContent = `Rp${grandTotal.toLocaleString('id-ID')}`;

            document.getElementById('admin-fee-section').classList.remove('hidden');
            document.getElementById('ppn-section').classList.remove('hidden');
        }
    </script>
@endsection
