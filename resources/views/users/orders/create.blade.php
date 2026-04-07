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

    <!-- Page 1: Rincian Pemesanan -->
    <div id="page-1" class="page active">
        <section class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="grid lg:grid-cols-1 gap-8">


                    <!-- Attendee Information -->
                    <div class="bg-white rounded-2xl shadow-xl p-8 mt-8 border border-slate-200">
                        <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
                            <i class="fas fa-user-friends text-teal-600 mr-3"></i>
                            Informasi Peserta
                        </h2>

                        @if (session('error'))
                            <div class="bg-teal-100 text-teal-800 px-4 py-2 rounded mb-4">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form id="attendee-form" action="{{ route('orders.validate-attendees', $event) }}" method="POST">
                            @csrf
                            @foreach ($selectedTickets as $ticketTypeId => $ticket)
                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-slate-800 mb-4">
                                        {{ $ticket['ticket_type']->name }} ({{ $ticket['quantity'] }}x)
                                    </h3>

                                    {{-- Peserta pertama dari user yang login --}}
                                    <div class="grid md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                                            <input type="text"
                                                name="tickets[{{ $ticketTypeId }}][attendees][0][name]"
                                                value="{{ old('tickets.' . $ticketTypeId . '.attendees.0.name', auth()->user()->name) }}"
                                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                            <input type="email"
                                                name="tickets[{{ $ticketTypeId }}][attendees][0][email]"
                                                value="{{ old('tickets.' . $ticketTypeId . '.attendees.0.email', auth()->user()->email) }}"
                                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                                required>
                                        </div>
                                    </div>
                                    

                                    @if ($ticket['quantity'] > 1)
                                        {{-- Mulai dari index 1 karena index 0 sudah terisi --}}
                                        @for ($i = 1; $i < $ticket['quantity']; $i++)
                                            <div class="border border-slate-200 rounded-lg p-6 mb-4">
                                                <h4 class="font-medium text-slate-800 mb-4">Peserta {{ $i + 1 }}
                                                </h4>

                                                <div class="grid md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama
                                                            Lengkap</label>
                                                        <input type="text"
                                                            name="tickets[{{ $ticketTypeId }}][attendees][{{ $i }}][name]"
                                                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                                            required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-slate-700 mb-1">Email

                                                        </label>
                                                        <input type="email"
                                                            name="tickets[{{ $ticketTypeId }}][attendees][{{ $i }}][email]"
                                                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                                            required>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    @endif

                                    <input type="hidden" name="tickets[{{ $ticketTypeId }}][quantity]"
                                        value="{{ $ticket['quantity'] }}">
                                </div>
                            @endforeach

                            <div class="flex justify-end mt-6">
                                <button type="submit"
                                    class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition-colors font-semibold">
                                    Lanjut ke Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
        </section>
    @endsection
