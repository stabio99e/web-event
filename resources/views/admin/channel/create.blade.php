@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Tambah Channel Pembayaran</h1>
                <p></p>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 col-lg-6">
                    <div class="card">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.channel.store') }}">
                            @csrf
                            <div class="card-header">
                                <h4>Form Tambah Channel</h4>
                            </div>
                            <div class="card-body">

                                <div class="mb-3">
                                    <label>Nama Channel</label>
                                    <input type="text" name="channel_name"
                                        class="form-control @error('channel_name') is-invalid @enderror"
                                        value="{{ old('channel_name') }}" required>
                                    @error('channel_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Kode Channel</label>
                                    <input type="text" name="channel_code"
                                        class="form-control @error('channel_code') is-invalid @enderror"
                                        value="{{ old('channel_code') }}" required>
                                    @error('channel_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Grup Channel</label>
                                    <select name="channel_group"
                                        class="form-control @error('channel_group') is-invalid @enderror" required>
                                        <option value="" disabled selected>Pilih Grup</option>
                                        <option value="VA" {{ old('channel_group') == 'VA' ? 'selected' : '' }}>VA
                                        <option value="Qris" {{ old('channel_group') == 'Qris' ? 'selected' : '' }}>Qris
                                        <option value="Ewallet" {{ old('channel_group') == 'Ewallet' ? 'selected' : '' }}>
                                            Ewallet
                                        </option>

                                    </select>
                                    @error('channel_group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Type</label>
                                    <select name="type" class="form-control @error('type') is-invalid @enderror"
                                        required>
                                        <option value="" disabled selected>Pilih Type</option>
                                        <option value="DIRECT" {{ old('type') == 'DIRECT' ? 'selected' : '' }}>DIRECT
                                        </option>
                                        <option value="REDIRECT" {{ old('type') == 'REDIRECT' ? 'selected' : '' }}>REDIRECT
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>biaya_flat</label>
                                    <input type="number" name="biaya_flat"
                                        class="form-control @error('biaya_flat') is-invalid @enderror"
                                        value="{{ old('biaya_flat') }}" required>
                                    @error('biaya_flat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label>biaya_percent</label>
                                    <input type="number" name="biaya_percent" min="0"
                                        class="form-control @error('biaya_percent') is-invalid @enderror"
                                        value="{{ old('biaya_percent', 0) }}">
                                    @error('biaya_percent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                

                                <div class="mb-3">
                                    <label>PPN</label>
                                    <input type="number" name="ppn" min="0"
                                        class="form-control @error('ppn') is-invalid @enderror" value="{{ old('ppn', 0) }}"
                                        required>
                                    @error('ppn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <div class="card-footer text-right">
                                <button class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
