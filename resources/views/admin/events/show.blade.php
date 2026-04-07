@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Detail Events</h1>
                <p>Kelola dan pantau event Anda </p>
            </div>


            <div class="container-fluid p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card border-0 mb-4 fade-in">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Event Image -->
                            <div class="col-lg-4">
                                <img src="{{ asset($event->image_path ?? '/storage/events/default.svg') }}"
                                    alt="{{ $event->title }}" class="img-fluid rounded-3"
                                    style=" width: 100%; object-fit: cover;">
                            </div>

                            <!-- Event Details -->
                            <div class="col-lg-8">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h3 class="fw-bold mb-2">{{ $event->title }}</h3>
                                        <p class="text-muted mb-3">{{ $event->description }}</p>
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            @php
                                                if ($event->status == 1) {
                                                    $status = [
                                                        'label' => 'Aktif',
                                                        'class' => 'success',
                                                        'icon' => 'check-circle',
                                                    ];
                                                } elseif ($event->status == 2) {
                                                    $status = [
                                                        'label' => 'Nonaktif',
                                                        'class' => 'secondary',
                                                        'icon' => 'ban',
                                                    ];
                                                } elseif ($event->status == 3) {
                                                    $status = [
                                                        'label' => 'Dibatalkan',
                                                        'class' => 'danger',
                                                        'icon' => 'times-circle',
                                                    ];
                                                } elseif ($event->status == 4) {
                                                    $status = [
                                                        'label' => 'Selesai',
                                                        'class' => 'info',
                                                        'icon' => 'flag-checkered',
                                                    ];
                                                } else {
                                                    $status = [
                                                        'label' => 'Tidak Diketahui',
                                                        'class' => 'dark',
                                                        'icon' => 'question-circle',
                                                    ];
                                                }
                                            @endphp

                                            <span class="badge bg-{{ $status['class'] }} px-3 py-2">
                                                <i class="fas fa-{{ $status['icon'] }} me-1"></i>
                                                {{ $status['label'] }}
                                            </span>


                                            <span class="badge bg-primary px-3 py-2">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.events.edit', ['eventsid' => $event->id]) }}"
                                            class="btn btn-primary btn-action">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>

                                    </div>
                                </div>

                                <div class="row g-4 text-dark">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-calendar text-primary me-3"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-clock text-primary me-3"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($event->end_datetime)->format('H:i') }} WIB</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt text-primary me-3"></i>
                                            <span>
                                                {{ $event->EventsLocation->name ?? '-' }},
                                                {{ $event->EventsLocation->city ?? '' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-users text-primary me-3"></i>
                                            <span>Kapasitas: {{ $event->max_attendees }} orang</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-ticket-alt text-primary me-3"></i>
                                            <span>Terdaftar: {{ $totalAttendees }} orang</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-money-bill text-primary me-3"></i>
                                            <span>
                                                Total Revenue (+PPN): Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                                                <br>

                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <!-- Total Peserta -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card stats-card border-0 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-users text-primary fs-4"></i>
                                    </div>
                                    <span class="fs-2 fw-bold text-primary">{{ $totalAttendees }}</span>
                                </div>
                                <h6 class="fw-semibold mb-1">Total Peserta</h6>
                                <p class="text-muted small mb-3">{{ $participantPercent }}% dari kapasitas</p>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar progress-bar-custom"
                                        style="width: {{ $participantPercent }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pembayaran Berhasil -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card stats-card border-0 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-check-circle text-success fs-4"></i>
                                    </div>
                                    <span class="fs-2 fw-bold text-success">{{ $paidCount }}</span>
                                </div>
                                <h6 class="fw-semibold mb-1">Pembayaran Berhasil</h6>
                                <p class="text-muted small mb-3">{{ $paidPercent }}% dari total</p>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $paidPercent }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Payment -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card stats-card border-0 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                        <i class="fa-regular fa-clock text-white fs-4"></i>
                                    </div>
                                    <span class="fs-2 fw-bold text-warning">{{ $unpaidCount }}</span>
                                </div>
                                <h6 class="fw-semibold mb-1">Pending Payment</h6>
                                <p class="text-muted small mb-3">{{ $unpaidPercent }}% dari total</p>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $unpaidPercent }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Revenue -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card stats-card border-0 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                        <i class="fas fa-money-bill-wave text-info fs-4"></i>
                                    </div>
                                    <span class="fs-2 fw-bold text-info">
                                        {{ Str::upper(Str::slug(number_format($totalRevenue / 1000000, 0))) }}M
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Total Revenue</h6>
                                <p class="text-muted small mb-3">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="card border-0">
                    <div class="card-header bg-white border-bottom">
                        <ul class="nav nav-tabs card-header-tabs" id="eventTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="participants-tab" data-bs-toggle="tab"
                                    data-bs-target="#participants" type="button" role="tab">
                                    <i class="fas fa-users me-2"></i>Peserta ({{ $totalAttendees }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="transactions-tab" data-bs-toggle="tab"
                                    data-bs-target="#transactions" type="button" role="tab">
                                    <i class="fas fa-credit-card me-2"></i>Transaksi ({{ $transactionCount }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="analytics-tab" data-bs-toggle="tab"
                                    data-bs-target="#analytics" type="button" role="tab">
                                    <i class="fas fa-chart-line me-2"></i>Analytics
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="settings-tab" data-bs-toggle="tab"
                                    data-bs-target="#settings" type="button" role="tab">
                                    <i class="fas fa-cog me-2"></i>Pengaturan
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="eventTabsContent">
                        <!-- Participants Tab -->
                        <div class="tab-pane fade show active" id="participants" role="tabpanel">
                            <div class="card-body p-4">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                                    <h5 class="fw-bold mb-3 mb-md-0">Daftar Peserta</h5>
                                    <form method="GET" class="d-flex flex-column flex-sm-row gap-2">
                                        <div class="position-relative">
                                            <input type="text" name="search" class="form-control ps-5"
                                                value="{{ request('search') }}" placeholder="Cari peserta..."
                                                style="width: 250px;">
                                            <i class="fas fa-search position-absolute translate-middle-y ms-3 text-muted"
                                                style="margin-top: -19px;"></i>
                                        </div>
                                        <select name="status" class="form-select" style="height: 40px">
                                            <option value="">Semua Status</option>
                                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>
                                                Sudah Bayar</option>
                                            <option value="pending"
                                                {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        </select>

                                        <a href="{{ route('admin.events.absensi', ['id' => $event->id]) }}" class="btn btn-info btn-action" style="margin-top: -1.5px;">
                                            <i class="fas fa-download me-2"></i>Absensi
                                        </a>
                                    </form>
                                </div>


                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="form-check-input">
                                                </th>
                                                <th scope="col">Peserta</th>
                                                <th scope="col">Tipe Tiket</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Tanggal Daftar</th>
                                                <th scope="col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tickets as $ticket)
                                                @php
                                                    $order = $ticket->orderItem->order;
                                                    $type = $ticket->orderItem->ticketType;
                                                    $ordertrx = $ticket->orderItem;
                                                    $status = $order->status;
                                                @endphp
                                                <tr>
                                                    <td><input type="checkbox" class="form-check-input"></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div>
                                                                <div class="fw-semibold">{{ $ticket->attendee_name }}
                                                                </div>
                                                                <small
                                                                    class="text-muted">{{ $ticket->attendee_email }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $type->is_premium ? 'secondary' : 'success' }}">
                                                            {{ $type->name }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $status === 'PAID' ? 'badge-success' : 'badge-pending' }}">
                                                            {{ $status === 'PAID' ? 'Sudah Bayar' : 'Pending' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $ticket->created_at->format('d M Y') }}</td>

                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button onclick="viewParticipant({{ $ticket->id }})"
                                                                class="btn btn-sm btn-outline-primary"><i
                                                                    class="fas fa-eye"></i></button>
                                                            <button onclick="editParticipant({{ $ticket->id }})"
                                                                class="btn btn-sm btn-outline-success"><i
                                                                    class="fas fa-edit"></i></button>
                                                            <button onclick="deleteParticipant({{ $ticket->id }})"
                                                                class="btn btn-sm btn-outline-danger"><i
                                                                    class="fas fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>


                                    </table>
                                </div>
                                <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <p class="text-muted mb-0">
                                        Menampilkan {{ $tickets->firstItem() }}-{{ $tickets->lastItem() }} dari
                                        {{ $tickets->total() }} peserta
                                    </p>
                                    {{ $tickets->links('pagination::bootstrap-5') }}
                                </div>

                            </div>
                        </div>

                        <!-- Transactions Tab -->
                        <div class="tab-pane fade" id="transactions" role="tabpanel">
                            <div class="card-body p-4">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                                    <h5 class="fw-bold mb-3 mb-md-0">Riwayat Transaksi</h5>

                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">ID Transaksi</th>
                                                <th scope="col">Peserta</th>
                                                <th scope="col">Metode Pembayaran</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Tanggal</th>
                                                <th scope="col">Total</th>
                                                <th scope="col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $trx)
                                                <tr class="participant-row">
                                                    <td><code class="small">{{ $trx->merchant_ref }}</code></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="fw-semibold">{{ $trx->customer_name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if (Str::contains(strtolower($trx->payment_method), 'bank'))
                                                                <i class="fas fa-university text-primary me-2"></i>
                                                            @elseif (Str::contains(strtolower($trx->payment_method), 'wallet'))
                                                                <i class="fas fa-credit-card text-success me-2"></i>
                                                            @else
                                                                <i class="fas fa-money-bill text-info me-2"></i>
                                                            @endif
                                                            <span>{{ $trx->payment_name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($trx->status === 'PAID')
                                                            <span class="badge badge-success">Berhasil</span>
                                                        @elseif ($trx->status === 'UNPAID')
                                                            <span class="badge badge-pending">Pending</span>
                                                        @else
                                                            <span
                                                                class="badge badge-danger">{{ ucfirst($trx->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $trx->created_at->format('d M Y, H:i') }}</td>
                                                    <td class="fw-semibold">Rp
                                                        {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button onclick="viewTransaction({{ $trx->id }})"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if ($trx->status !== 'PAID')
                                                                <button onclick="confirmPayment({{ $trx->id }})"
                                                                    class="btn btn-sm btn-outline-success">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            @else
                                                                <button onclick="downloadInvoice({{ $trx->id }})"
                                                                    class="btn btn-sm btn-outline-success">
                                                                    <i class="fas fa-download"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                        <!-- Analytics Tab -->
                        <div class="tab-pane fade" id="analytics" role="tabpanel">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4">Analytics & Insights</h5>

                                <div class="row g-4">
                                    <!-- Registration Chart -->
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <h6 class="fw-semibold mb-3">Pendaftaran Harian</h6>
                                                <div class="chart-container bg-white rounded p-3">
                                                    <div class="d-flex align-items-end justify-content-between"
                                                        style="height: 200px;">
                                                        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                                            @php
                                                                $value = $registrationChart[$day] ?? 0;
                                                                $maxPrice = $event->ticketTypes->isNotEmpty()
                                                                    ? $event->ticketTypes->max('price')
                                                                    : 1;
                                                                $height = round(($value / max($maxPrice, 1)) * 100);
                                                            @endphp

                                                            <div class="chart-bar bg-primary"
                                                                style="height: {{ $height }}%; width: 12%;"></div>
                                                        @endforeach
                                                    </div>
                                                    <div class="d-flex justify-content-between small text-muted mt-2">
                                                        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $label)
                                                            <span>{{ $label }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ticket Types -->
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <h6 class="fw-semibold mb-3">Distribusi Tipe Tiket</h6>
                                                @foreach ($ticketTypes as $type)
                                                    @php
                                                        $percent =
                                                            $totalSold > 0
                                                                ? round(($type->sold / $totalSold) * 100, 1)
                                                                : 0;
                                                        $badge = $type->is_premium ? 'bg-secondary' : 'bg-success';
                                                    @endphp
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>{{ $type->name }} (Rp
                                                                {{ number_format($type->price, 0, ',', '.') }})</span>
                                                            <span class="fw-semibold">{{ $type->sold }} tiket</span>
                                                        </div>
                                                        <div class="progress" style="height: 12px;">
                                                            <div class="progress-bar {{ $badge }}"
                                                                style="width: {{ $percent }}%"></div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <!-- Settings Tab -->
                        <div class="tab-pane fade" id="settings" role="tabpanel">
                            <div class="card-body p-4">
                                <form action="{{ route('admin.events.updateSettings', $event->id) }}" method="POST">
                                    @csrf
                                    <div class="card-body p-4">
                                        <h5 class="fw-bold mb-4">Pengaturan Event</h5>

                                        <div class="row g-4">
                                            <!-- Basic Settings -->
                                            <div class="col-12">
                                                <div class="card bg-light border-0">
                                                    <div class="card-body">
                                                        <h6 class="fw-semibold mb-3">Pengaturan Dasar</h6>
                                                        <div class="row g-3">
                                                            <div class="col-md-12">
                                                                <label class="form-label fw-medium">Status Event</label>
                                                                <select name="status" class="form-select">
                                                                    <option value="1"
                                                                        {{ $event->status == 1 ? 'selected' : '' }}>
                                                                        Aktif</option>
                                                                    <option value="2"
                                                                        {{ $event->status == 2 ? 'selected' : '' }}>
                                                                        Draft</option>
                                                                    <option value="3"
                                                                        {{ $event->status == 3 ? 'selected' : '' }}>
                                                                        Dibatalkan</option>
                                                                    <option value="4"
                                                                        {{ $event->status == 4 ? 'selected' : '' }}>
                                                                        Selesai</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 mt-4">
                                                            <label class="form-label fw-medium">Total Kapasitas
                                                                (max_attendees)</label>
                                                            <input type="number" name="max_attendees"
                                                                class="form-control" value="{{ $event->max_attendees }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Ticket Settings -->
                                            <div class="col-12">
                                                <div class="card bg-light border-0">
                                                    <div class="card-body">
                                                        <h6 class="fw-semibold mb-3">Pengaturan Tiket</h6>

                                                        @foreach ($event->ticketTypes as $ticketType)
                                                            <div class="mb-4">
                                                                <h6 class="mb-3">Tipe Tiket {{ $ticketType->name }}</h6>
                                                                <input type="hidden"
                                                                    name="ticket_types[{{ $ticketType->id }}][id]"
                                                                    value="{{ $ticketType->id }}">

                                                                <div class="row g-3">
                                                                    <div class="col-md-3">
                                                                        <label class="form-label fw-medium">Harga</label>
                                                                        <input type="number" class="form-control"
                                                                            name="ticket_types[{{ $ticketType->id }}][price]"
                                                                            value="{{ $ticketType->price }}">
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <label class="form-label fw-medium">Kuota</label>
                                                                        <input type="number" class="form-control"
                                                                            name="ticket_types[{{ $ticketType->id }}][quantity_available]"
                                                                            value="{{ $ticketType->quantity_available }}">
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <label class="form-label fw-medium">Terjual</label>
                                                                        <input type="number"
                                                                            class="form-control bg-light"
                                                                            value="{{ $ticketType->sold }}" readonly>
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <label class="form-label fw-medium">Status
                                                                            Tiket</label>
                                                                        <select
                                                                            name="ticket_types[{{ $ticketType->id }}][is_active]"
                                                                            class="form-select">
                                                                            <option value="1"
                                                                                {{ $ticketType->is_active ? 'selected' : '' }}>
                                                                                Aktif</option>
                                                                            <option value="0"
                                                                                {{ !$ticketType->is_active ? 'selected' : '' }}>
                                                                                Nonaktif</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach


                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Save Button -->
                                            <div class="col-12">
                                                <div class="text-end">
                                                    <button class="btn btn-primary btn-lg btn-action">
                                                        <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participant Detail Modal -->
        <div class="modal fade" id="participantModal" tabindex="-1" aria-labelledby="participantModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="participantModalLabel">Detail Peserta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex align-items-center mb-4">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
                                alt="User" class="rounded-circle me-4" width="64" height="64">
                            <div>
                                <h5 class="fw-bold mb-1">Ahmad Rizki Pratama</h5>
                                <p class="text-muted mb-2">ahmad.rizki@email.com</p>
                                <span class="badge badge-success">Sudah Bayar</span>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">Informasi Pribadi</h6>
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">No. Telepon:</span>
                                        <span class="fw-semibold">+62 812-3456-7890</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Tanggal Lahir:</span>
                                        <span class="fw-semibold">15 Mei 1990</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Pekerjaan:</span>
                                        <span class="fw-semibold">Software Engineer</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">Informasi Tiket</h6>
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Tipe Tiket:</span>
                                        <span class="badge bg-secondary">VIP</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Nomor Kursi:</span>
                                        <span class="fw-semibold">A-15</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Check-in:</span>
                                        <span class="text-danger fw-semibold">Belum Check-in</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button onclick="sendEmail()" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i>Kirim Email
                        </button>
                        <button onclick="downloadTicket()" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Download Tiket
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
    </div>
@endsection
