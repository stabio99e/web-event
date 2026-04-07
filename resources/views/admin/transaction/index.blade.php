@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Transaksi</h1>
                <p></p>
            </div>

            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" class="mb-3">
                                <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                                    placeholder="Cari nama atau email">
                            </form>


                            <div class="table-responsive">
                                <table class="table table-striped" id="transactionTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th scope="col">Order Number</th>
                                            <th scope="col">Billing Name</th>
                                            <th scope="col">Billing Email</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Payment Status</th>
                                            <th scope="col">Payment Method</th>
                                            <th scope="col">action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $trx)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <th scope="row">#{{ $trx->order->order_number ?? 'N/A' }}</th>
                                                <td>{{ $trx->order->user->name ?? '-' }}</td>
                                                <td>{{ $trx->order->user->email ?? '-' }}</td>
                                                <td>{{ $trx->created_at->format('d M, Y') }}</td>
                                                <td>Rp{{ number_format($trx->order->TotalPayAmount + $trx->order->admin_fee ?? 0, 0, ',', '.') }}
                                                </td>

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


                                                <td>{{ ucfirst($trx->payment_method ?? '-') }}</td>
                                                <td>
                                                    <a
                                                        href="{{ route('admin.transaction.edit', ['orderID' => $trx->order->id]) }}">
                                                        Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                                <div class="mt-3">
                                    {{ $transactions->appends(request()->all())->links() }}
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
