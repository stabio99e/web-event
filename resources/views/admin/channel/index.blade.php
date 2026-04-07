@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Pembayaran</h1>
                <p></p>
            </div>

            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Channel Nama</th>
                                            <th scope="col">Channel Code</th>
                                            <th scope="col">Channel Group</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">biaya_flat</th>
                                            <th scope="col">biaya_percent</th>
                                            <th scope="col">PPN</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($getChannel as $pay)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>{{ $pay->channel_name }}</td>
                                                <td>{{ $pay->channel_code }}</td>
                                                <td>{{ $pay->channel_group }}</td>
                                                <td>{{ $pay->type }}</td>
                                                <td>Rp{{ number_format($pay->biaya_flat, 0, '.', '.') }}</td>
                                                <td>{{ $pay->biaya_percent }}</td>
                                                <td>{{ number_format($pay->ppn, 0, '.', '.') }}%</td>
                                                <td>{{ $pay->status }}</td>
                                                <td>
                                                    <a href="{{ route('admin.channel.edit', $pay->id) }}"
                                                        class="btn btn-warning btn-sm">Edit</a>
                                                </td>

                                            </tr>
                                        @endforeach
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
