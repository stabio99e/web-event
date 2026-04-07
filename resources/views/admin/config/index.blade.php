@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Pengaturan Website</h1>
                <p></p>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12 col-md-10 col-lg-8">
                    <div class="card">
                        <form method="POST" action="{{ route('admin.webconfig.update') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header">
                                <h4>Web Pengaturan</h4>
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <label>Nama Website</label>
                                    <input type="text" name="site_name" class="form-control @error('site_name') is-invalid @enderror"
                                           value="{{ old('site_name', $webConfig->site_name ?? '') }}" required>
                                    @error('site_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label>Tagline</label>
                                    <input type="text" name="site_tagline" class="form-control"
                                           value="{{ old('site_tagline', $webConfig->site_tagline ?? '') }}">
                                </div>

                                <div class="mb-3">
                                    <label>Email Kontak</label>
                                    <input type="email" name="contact_email" class="form-control"
                                           value="{{ old('contact_email', $webConfig->contact_email ?? '') }}">
                                </div>

                                <div class="mb-3">
                                    <label>WhatsApp Kontak</label>
                                    <input type="text" name="contact_whatsapp" class="form-control"
                                           value="{{ old('contact_whatsapp', $webConfig->contact_whatsapp ?? '') }}">
                                </div>

                                <div class="mb-3">
                                    <label>Deskripsi Website</label>
                                    <textarea name="site_description" class="form-control" rows="4">{{ old('site_description', $webConfig->site_description ?? '') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label>Logo Website</label><br>
                                    @if ($webConfig->logo_path ?? '')
                                        <img src="{{ asset($webConfig->logo_path) }}" alt="Logo" width="100" class="mb-2">
                                    @endif
                                    <input type="file" name="logo_path" class="form-control">
                                    <small class="text-muted">File: png, jpg, jpeg, svg</small>
                                </div>

                                <div class="mb-3">
                                    <label>Favicon Website</label><br>
                                    @if ($webConfig->favicon_path ?? '')
                                        <img src="{{ asset($webConfig->favicon_path) }}" alt="Favicon" width="32" class="mb-2">
                                    @endif
                                    <input type="file" name="favicon_path" class="form-control">
                                    <small class="text-muted">File: png, jpg, jpeg, ico</small>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div><!-- End Container-->
    </div><!-- End Content-->
@endsection
