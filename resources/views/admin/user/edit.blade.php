@extends('admin.layouts.app')

@section('content')
<div class="content-start transition">
    <div class="container-fluid dashboard">
        <div class="content-header">
            <h1>Edit Saldo Pengguna</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="text" class="form-control" value="{{ $user->email }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label>Nomor Telepon</label>
                        <input type="text" class="form-control" value="{{ $user->phone ?? '-' }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label>Roles</label>
                        <input type="text" class="form-control" value="{{ $user->roles }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label>Tanggal Bergabung</label>
                        <input type="text" class="form-control" value="{{ $user->created_at->format('d M Y') }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label>Saldo</label>
                        <input type="number" step="0.01" name="saldo" class="form-control @error('saldo') is-invalid @enderror" value="{{ old('saldo', $user->saldo) }}" required>
                        @error('saldo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Update Saldo</button>
                        <a href="{{ route('admin.user.show') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
