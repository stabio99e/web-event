@extends('users.layouts.app')
@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
            @if (session('error'))
                <div class="mt-4 bg-red-100 text-red-800 p-3 mb-5 rounded">
                    {{ session('error') }}
                </div>
            @endif
            @if (request('from') == 'rindutenang')
                <form class="space-y-6" method="post" action="{{ route('go.login') }}">
                    @csrf
                    <div class="slide-in">
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i class="fas fa-envelope text-teal-600 mr-2"></i>Email
                        </label>
                        <input type="email" id="email" name="email" required
                            class="form-input w-full px-4 py-3 border border-slate-300 rounded-xl focus:outline-none"
                            placeholder="Masukkan email Anda">
                        
                    </div>

                    <div class="slide-in" style="animation-delay: 0.1s;">
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                            <i class="fas fa-lock text-teal-600 mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="form-input w-full px-4 py-3 pr-12 border border-slate-300 rounded-xl focus:outline-none"
                                placeholder="Masukkan password Anda">

                        </div>
                    </div>

                    <button type="submit"
                        class="bg-teal-600 hover:bg-teal-700 w-full text-white py-4 rounded-xl font-bold text-lg slide-in"
                        style="animation-delay: 0.3s;">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk
                    </button>
                </form>
            @else
                <h4 class="text-center text-xl font-semibold mb-6">Login with Google</h4>
                @if (request('redirect'))
                    <div class="bg-teal-100 text-white-800 px-4 py-2 rounded mb-4">
                        Silakan login terlebih dahulu untuk membeli tiket
                    </div>
                @endif


                <a href="{{ route('google.login') }}"
                    class="flex items-center justify-center gap-2 bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl font-semibold text-sm border border-teal-700">
                    <i class="fab fa-google text-base"></i>
                    <span>Login with Google</span>
                </a>
            @endif


        </div>
    </div>
@endsection
