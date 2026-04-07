<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <!-- SEO Title & Meta -->
    <title>@yield('site_name', $webConfig->site_name ?? 'Eventsku')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset($webConfig->favicon_path ?? '/storage/images/default.png') }}">

    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-5.1.3/css/bootstrap.css') }}">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- FontAwesome CSS-->
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome6.1.1/css/all.css') }}">
    <!-- Boxicons CSS-->
    <link rel="stylesheet" href="{{ asset('assets/modules/boxicons/css/boxicons.min.css') }}">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

 
</head>

<body>
    <!--Topbar -->
    <div class="topbar transition">
        <div class="bars">
            <button type="button" class="btn transition" id="sidebar-toggle">
                <i class="fa fa-bars"></i>
            </button>
        </div>
        <div class="menu">
            <ul>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img src="{{ asset('assets/images/avatar/avatar-1.png') }}" alt="">
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                        <hr class="dropdown-divider">

                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="fa fa-sign-out-alt  size-icon-1"></i> <span>Logout</span></a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    </ul>
                </li>
            </ul>
        </div>
    </div>


    <!--Sidebar-->
    <div class="sidebar transition overlay-scrollbars">
        <div class="sidebar-content">
            <div id="sidebar">

                <!-- Logo -->
                <div class="logo">
                    <h2 class="mb-0">{{ $webConfig->site_name ?? 'Eventsku' }}</h2>
                </div>

                <ul class="side-menu">
                    <li class="divider" data-text="Umum">Umum</li>

                    <li>
                        <a href="{{ route('admin.home') }}"
                            class="{{ request()->routeIs('admin.home') ? 'active' : '' }}">
                            <i class='fa fa-house icon'></i> Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.user.show') }}"
                            class="{{ request()->routeIs('admin.user.show') ? 'active' : '' }}">
                            <i class='fa fa-user icon'></i> Pengguna
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.transaction.show') }}"
                            class="{{ request()->routeIs('admin.transaction.show') ? 'active' : '' }}">
                            <i class='fa-solid fa-tent-arrow-left-right icon'></i> Transaksi
                        </a>
                    </li>


                    <!-- Divider-->
                    <li class="divider" data-text="Content">Content</li>
                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                            <i class='fa-solid fa-calendar icon'></i>
                            Events
                            <i class='fa fa-chevron-right icon-right'></i>
                        </a>
                        <ul class="side-dropdown">
                            <li><a href="{{ route('admin.events.show') }}">Lihat Events</a></li>
                            <li><a href="{{ route('admin.events.create') }}">Tambah Events</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.certificate.*') ? 'active' : '' }}">
                            <i class='fa-solid fa-certificate icon'></i>
                            Sertifikat
                            <i class='fa fa-chevron-right icon-right'></i>
                        </a>
                        <ul class="side-dropdown">
                            <li><a href="{{ route('admin.certificate.show') }}">Lihat Sertifikat</a></li>
                            <li><a href="{{ route('admin.certificate.create') }}">Tambah Sertifikat</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.hero.*') ? 'active' : '' }}">
                            <i class='fa-solid fa-file icon'></i>
                            Hero
                            <i class='fa fa-chevron-right icon-right'></i>
                        </a>
                        <ul class="side-dropdown">
                            <li><a href="{{ route('admin.hero.show') }}">Lihat Hero</a></li>
                            <li><a href="{{ route('admin.hero.create') }}">Tambah Hero</a></li>
                        </ul>
                    </li>


                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                            <i class='fa fa-pager icon'></i>
                            Pages
                            <i class='fa fa-chevron-right icon-right'></i>
                        </a>
                        <ul class="side-dropdown">
                            <li><a href="{{ route('admin.pages.show') }}">Lihat Pages</a></li>
                            <li><a href="{{ route('admin.pages.create') }}">Tambah Pages</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.qnas.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-question icon"></i>
                            Q&A
                            <i class='fa fa-chevron-right icon-right'></i>
                        </a>
                        <ul class="side-dropdown">
                            <li><a href="{{ route('admin.qnas.show') }}">Lihat Q&a</a></li>
                            <li><a href="{{ route('admin.qnas.create') }}">Tambah Q&a</a></li>
                        </ul>
                    </li>

                    <!-- Divider-->
                    <li class="divider" data-text="Pengaturan">Pengaturan</li>

                    <li>
                        <a href="{{ route('admin.webconfig') }}"
                            class="{{ request()->routeIs('admin.webconfig') ? 'active' : '' }}">
                            <i class='fa fa-cog icon'></i>
                            Setting Website
                        </a>
                    </li>
 

                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.channel.*') ? 'active' : '' }}">
                            <i class='fa fa-pencil-ruler icon'></i>
                            Pembayaran
                            <i class='fa fa-chevron-right icon-right'></i>
                        </a>
                        <ul class="side-dropdown">
                            <li><a href="{{ route('admin.channel.CHPay') }}">Lihat Channel</a></li>
                            <li><a href="{{ route('admin.channel.create') }}">Tambah Channel</a></li>
                        </ul>
                    </li>

                </ul>


            </div>

        </div>
    </div>
    </div><!-- End Sidebar-->

    <div class="sidebar-overlay"></div>


    <!-- Main Content -->
    @yield('content')


    <!-- Footer -->
    <footer>
        <div class="footer">
            <div class="text-center">
                <p>{{ date('Y') }} &copy; {{ $webConfig->meta_title ?? 'Eventsku' }}</p>
            </div>

        </div>
        </div>
    </footer>

 
     

    <!-- General JS Scripts -->
    <script src="{{ asset('assets/js/atrana.js') }}"></script>

    <!-- JS Libraies -->
    <script src="{{ asset('assets/modules/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap-5.1.3/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/modules/popper/popper.min.js') }}"></script>

    <!-- Template JS File -->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    @yield('script')
</body>

</html>
