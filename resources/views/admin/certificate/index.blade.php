@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Sertifikat</h1>
                <p></p>
            </div>
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
                                        <th scope="col">Nama Events</th>
                                        <th scope="col">Nama Sertifikat</th>
                                        <th scope="col">Template Sertifikat</th>
                                        <th scope="col">Link Sertifikat</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($getCertificateTemplates as $certificateTemplate)
                                        <tr>
                                            <th scope="row">{{ $certificateTemplate->id }}</th>
                                            <td><a
                                                    href="{{ route('admin.events.details', ['eventsid' => $certificateTemplate->event->id]) }}">{{ $certificateTemplate->event->title ?? 'N/A' }}</a>
                                            </td>
                                            <td>{{ $certificateTemplate->name }}</td>
                                            <td>
                                                <a href="{{ asset($certificateTemplate->image_path) }}"
                                                    target="_blank">Lihat</a>
                                            </td>
                                            <td>
                                                <a href="{{ route("certificate.form")}}"
                                                    target="_blank">Lihat</a>
                                            </td>
                                            <td>
                                                @if ($certificateTemplate->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a
                                                    href="{{ route('admin.certificate.edit', $certificateTemplate->id) }}">Edit</a>
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
