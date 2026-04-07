@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Dashboard</h1>
                <p></p>
            </div>

            <div class="row">

                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body py-3 px-4">
                            <div class="row align-items-center">
                                <div class="col-4 text-center">
                                    <i class="fas fa-inbox icon-home bg-primary text-light"
                                        style="font-size: 1.2rem; padding: 10px;"></i>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 text-muted small">Total Revenue [+PPN]</p>
                                    <p class="mb-0 fw-bold small">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                                    <p class="mt-1 mb-0 text-muted small">Jumlah PPN</p>
                                    <p class="mb-0 fw-bold small">Rp {{ number_format($revenuePPN, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4 d-flex align-items-center">
                                    <i class="fas fa-clipboard-list icon-home bg-success text-light"></i>
                                </div>
                                <div class="col-8">
                                    <p>Orders</p>
                                    <h5>{{ $totalOrders }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4 d-flex align-items-center">
                                    <i class="fas fa-chart-bar  icon-home bg-info text-light"></i>
                                </div>
                                <div class="col-8">
                                    <p>Sales</p>
                                    <h5>{{ $totalEvents }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4 d-flex align-items-center">
                                    <i class="fas fa-id-card  icon-home bg-warning text-light"></i>
                                </div>
                                <div class="col-8">
                                    <p>User</p>
                                    <h5>{{ $totalUsers }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Statistik Pengunjung</h4>
                            <select id="filterType" class="form-select w-auto">
                                <option value="daily">Harian (30 Hari)</option>
                                <option value="monthly">Bulanan (12 Bulan)</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <canvas id="visitorChart" height="100"></canvas>
                        </div>
                    </div>
                </div>



                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Latest Transaction
                                <a href="{{ route('admin.transaction.show') }}" class="float-end">Lihat semua</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Order Id</th>
                                            <th scope="col">Billing Name</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Payment Status</th>
                                            <th scope="col">Payment Method</th>
                                            <th scope="col">View Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($latestTransactions as $trx)
                                            <tr>
                                                <th scope="row">#{{ $trx->order->id ?? '-' }}</th>
                                                <td>{{ $trx->order->user->name ?? '-' }}</td>
                                                <td>{{ $trx->created_at->format('d M, Y') }}</td>
                                                <td>Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                        $status = strtoupper($trx->order->status);
                                                        $badgeClass = match ($status) {
                                                            'PAID' => 'bg-success',
                                                            'UNPAID' => 'bg-secondary',
                                                            'FAILED' => 'bg-danger',
                                                            'EXPIRED' => 'bg-warning text-dark',
                                                            'REFUND' => 'bg-primary',
                                                            default => 'bg-light text-dark',
                                                        };
                                                    @endphp

                                                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                                </td>

                                                <td>{{ $trx->payment_method ?? '-' }}</td>
                                                <td>
                                                    <a
                                                        href="{{ route('admin.transaction.edit', ['orderID' => $trx->order->id]) }}">Edit</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Belum ada transaksi</td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let ctx = document.getElementById('visitorChart').getContext('2d');
        let visitorChart;

        function renderChart(type = 'daily') {
            fetch("{{ route('admin.visitor.chart') }}?type=" + type)
                .then(response => response.json())
                .then(data => {
                    let labels = data.map(item => item.label);
                    let counts = data.map(item => item.count);

                    if (visitorChart) visitorChart.destroy();

                    visitorChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Jumlah Pengunjung',
                                data: counts,
                                fill: true,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
        }

        document.getElementById('filterType').addEventListener('change', function() {
            renderChart(this.value);
        });

        // Load awal
        renderChart();
    </script>
@endsection
