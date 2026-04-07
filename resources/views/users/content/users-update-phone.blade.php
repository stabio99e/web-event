@extends('users.layouts.app')

@section('content')
    <!-- Main Content -->
    <section class="container mx-auto px-4 py-8 h-[70vh] flex items-center justify-center">
        <div class="w-full max-w-md">
            <!-- Form Update Nomor WhatsApp -->
            <div class="mt-8 text-left">
                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4" role="alert">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm mb-2 sm:mb-0">
                                <strong class="font-semibold">Berhasil!</strong> Nomor WhatsApp Anda telah diperbarui.
                            </p>
                            <a href="{{ route('users.profile') }}"
                                class="inline-block mt-2 sm:mt-0 bg-green-500 hover:bg-green-600 text-white text-sm font-medium px-4 py-2 rounded-md transition">
                                Kembali ke Profil
                            </a>
                        </div>
                    </div>
                @endif

                <form action="{{ route('user.update.phone')}} " method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <label for="phone" class="block text-sm font-medium text-slate-700">Nomor WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
                        placeholder="Contoh: 81234567890">

                    @error('phone')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-md transition">
                        Simpan Nomor
                    </button>
                </form>
            </div>

        </div>
    </section>
@endsection
