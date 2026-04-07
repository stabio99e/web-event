<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Title & Meta -->
    <title>@yield('site_name', $webConfig->site_name ?? 'Eventsku')</title>

    <meta name="description" content="@yield('meta_description', $webConfig->meta_description ?? 'Deskripsi default dari web')">
    <meta name="keywords" content="@yield('meta_keywords', $webConfig->meta_keywords ?? 'kata, kunci, default')">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset($webConfig->favicon_path ?? '/storage/images/default.png') }}">
    <!-- OG Meta -->
    <meta property="og:title" content="@yield('meta_og_title', $webConfig->meta_title ?? 'Eventsku')">
    <meta property="og:description" content="@yield('meta_og_description', $webConfig->meta_description ?? 'Deskripsi default dari web')">
    <meta property="og:image" content="@yield('meta_og_image', asset($webConfig->logo_path ?? '/storage/images/default.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <!-- Stylesheets -->
    @vite(['resources/css/app.css'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome6.1.1/css/all.css') }}">
</head>

<body class="min-h-screen">
    <header class="bg-white backdrop-blur-sm shadow sticky top-0 z-50">
        <div class="container mx-auto px-4 py-1 flex justify-between items-center">
            <div class="flex items-center">
                <button id="mobile-menu-button"
                    class="md:hidden mr-2 text-indigo-300 hover:text-indigo-100 transition-colors">
                    <i class="fas fa-bars text-base"></i>
                </button>
                <a href="{{ route('home') }}">
                    <img src="{{ asset($webConfig->logo_path ?? '/storage/images/logo.png') }}" alt="Logo"
                        class="w-20 h-auto max-w-full object-contain" />
                </a>
            </div>

            @guest
                <a href="{{ route('login') }}"
                    class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl font-semibold text-xs border border-teal-700">
                    <i class="fas fa-sign-in-alt text-xs"></i>
                    Login
                </a>
            @else
                <!-- Dropdown User -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 text-sm focus:outline-none">
                        @if (Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" class="w-8 h-8 rounded-full object-cover" alt="Avatar">
                        @else
                            <i class="fas fa-user-circle text-2xl text-slate-600"></i>
                        @endif
                        <span class="hidden sm:inline font-medium text-slate-700">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs text-slate-500"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.outside="open = false"
                        class="absolute right-0 mt-2 w-40 bg-white border border-slate-200 rounded-xl shadow-lg py-2 z-50 transition-all"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1">
                        <a href="{{ route('dashboard') }}"
                            class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Riwayat Transaksi</a>
                        <a href="{{ route('users.profile') }}"
                            class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Profile</a>
                        @if (Auth::check() && Auth::user()->roles == 'admin')
                            <a href="{{ route('admin.home') }}"
                                class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">
                                Panel
                            </a>
                        @endif

                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    </div>
                </div>
            @endguest
        </div>
    </header>


    <!-- Mobile Menu -->
    <div id="mobile-menu"
        class="mobile-menu fixed inset-0 bg-white/95 backdrop-blur-md z-40 pt-24 px-6 overflow-y-auto">
        <div class="flex flex-col space-y-8 text-slate-700">
            <a href="{{ route('home') }}"
                class="text-xl py-3 border-b border-slate-200 hover:text-teal-600 transition-colors">Beranda</a>
            <a href="{{ route('events.list') }}"
                class="text-xl py-3 border-b border-slate-200 hover:text-teal-600 transition-colors">Daftar Events
                Berdasarkan Wilayah</a>
            <div class="dropdown-container">
                <button
                    class="dropdown-btn text-xl py-3 border-b border-slate-200 hover:text-teal-600 transition-colors w-full text-left flex justify-between items-center">
                    Lainnya
                    <svg class="dropdown-icon w-5 h-5 ml-2 transition-transform" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="dropdown-content hidden pl-4">
                    <div>
                        <ul class="space-y-2 text-slate-600">
                            @foreach ($footerPages as $page2)
                                <li>
                                    <a href="{{ route('pages.show', ['slug' => $page2->slug]) }}"
                                        class="block text-lg py-2 hover:text-teal-600 transition-colors">
                                        {{ $page2->title }}
                                    </a>
                                </li>
                            @endforeach
                            <li>
                                <a href="{{ route('certificate.form') }}"
                                    class="hover:text-teal-600 transition-colors">
                                    Cetak Sertifikat
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    @yield('content')


    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center w-full md:text-left md:w-auto mb-6 md:mb-0">
                    <div class="flex justify-center md:block">

                        <img src="{{ $webConfig->logo_path ?? '/storage/images/logo.png' }}" alt="Logo"
                            class="w-24 max-w-full object-contain" />
                    </div>
                    <p class="text-slate-600 max-w-md mx-auto md:mx-0">
                        {{ $webConfig->site_tagline ?? 'Tagline default' }}
                    </p>
                </div>

                <div class="flex flex-wrap justify-center gap-6 md:gap-8 text-center">
                    @foreach ($footerPages as $page)
                        <div>
                            <ul class="space-y-2 text-slate-600">
                                <li>
                                    <a href="{{ route('pages.show', ['slug' => $page->slug]) }}"
                                        class="hover:text-teal-600 transition-colors">
                                        {{ $page->title }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endforeach

                    <div>
                        <ul class="space-y-2 text-slate-600">
                            <li>
                                <a href="{{ route('certificate.form') }}"
                                    class="hover:text-teal-600 transition-colors">
                                    Cetak Sertifikat
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 mt-6 pt-6 text-center">
                <p class="text-sm text-slate-500">
                    Copyright © 2025 - All right reserved, {{ $webConfig->meta_title ?? 'Eventsku' }}
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('active');
            });
            document.addEventListener('click', function(event) {
                if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                    mobileMenu.classList.remove('active');
                }
            });

            const dropdownBtn = document.querySelector('.dropdown-btn');
            if (dropdownBtn) {
                dropdownBtn.addEventListener('click', function() {
                    const dropdownContent = this.nextElementSibling;
                    const dropdownIcon = this.querySelector('.dropdown-icon');

                    dropdownContent.classList.toggle('hidden');
                    dropdownIcon.classList.toggle('rotate-180');
                });
            }
        });
    </script>
    @yield('scripts')
</body>

</html>
