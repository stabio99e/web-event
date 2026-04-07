@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Pengguna</h1>
                <p></p>
            </div>

            <div class="row">


                <div class="col-md-12">
                    <div class="card">

                        <div class="card-body">
                            <form method="GET" class="mb-3">
                                <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                                    placeholder="Cari nama atau Order ID...">
                            </form>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Nama Lengkap</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Saldo</th>
                                            <th scope="col">Nomor Telepon</th>
                                            <th scope="col">Bergabung Pada</th>
                                            <th scope="col">Roles</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($getUsersALL as $user)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ number_format($user->saldo, 0, ',', '.') }}</td>
                                                <td>
                                                    @if ($user->phone === null)
                                                        Belum Diisi
                                                    @else
                                                        {{ $user->phone }}
                                                    @endif
                                                </td>
                                                <td>{{ $user->created_at }}</td>
                                                <td>
                                                    @if ($user->roles === 'user')
                                                        <span>User</span>
                                                    @else
                                                        <span>Admin</span>
                                                    @endif
                                                </td>
                                                <td><a href="{{ route('admin.user.edit', ['id' => $user->id]) }}">Edit</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3">
                                    {{ $getUsersALL->appends(request()->all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
