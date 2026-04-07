@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Update Status Transaksi</h1>
                <p></p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.transaction.update', $order->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="order_id" class="form-label">Order Number</label>
                            <input type="text" class="form-control" value="{{ $order->order_number }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="billing_name" class="form-label">Billing Name</label>
                            <input type="text" class="form-control" value="{{ $order->user->name ?? '-' }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="created_at" class="form-label">Date</label>
                            <input type="text" class="form-control" value="{{ $order->created_at->format('d M Y H:i') }}"
                                disabled>
                        </div>

                        <div class="mb-3">
                            <label for="total" class="form-label">Total</label>
                            <input type="text" class="form-control"
                                value="Rp {{ number_format($order->TotalPayAmount + $order->admin_fee, 0, ',', '.') }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label>Status (Order & Transaksi)</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror">
                                @php
                                    $statusOptions = ['PAID', 'FAILED', 'EXPIRED', 'UNPAID', 'REFUND'];
                                @endphp
                                @foreach ($statusOptions as $status)
                                    <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('admin.transaction.show') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
