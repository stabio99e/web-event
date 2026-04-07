@extends('users.layouts.app')

@section('content')
    <!-- Main Content -->
    <section class="container mx-auto px-4 py-8 h-[70vh] flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200 text-center">
                <!-- Profile Summary -->
                <h3 class="text-2xl font-bold text-slate-800 mb-2">{{ $user->name }}</h3>
                <p class="text-slate-600 text-sm mb-4">{{ $user->email }} - {{ $user->phone }}</p>
                <span class="inline-block bg-teal-100 text-teal-800 px-4 py-2 rounded-full text-sm font-semibold">
                    Saldo Refund: Rp {{ number_format($user->saldo, 0, ',', '.') }}
                </span>

                <!-- Contact Admin Link -->
                <div class="mt-6">
                    <a href="https://wa.me/+62{{ $webConfig->contact_whatsapp ?? '628123131' }}?text=Halo%20admin" target="_blank"
                        class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 transition-colors">
                        <i class="fab fa-whatsapp"></i>
                        Hubungi Admin via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
