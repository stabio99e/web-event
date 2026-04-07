@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Events</h1>
                <p></p>
            </div>

            <div class="row">
                @forelse ($events as $event)
                    <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                        <div class="card border-0 h-100 position-relative overflow-hidden">
                            <div class="position-relative">
                                <img src="{{ asset($event->banner_url ?? 'storage/events/default.svg') }}"
                                    class="card-img-top" alt="{{ $event->name }}">
                                <div class="position-absolute top-0 start-0 w-100 h-100"
                                    style="background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);"></div>
                                <span class="badge bg-primary position-absolute top-0 start-0 m-2 text-white">
                                    {{ strtoupper($event->EventsLocation->city ?? 'Unknown') }}
                                </span>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <a href="{{ route('admin.events.details', ['eventsid' => $event->id ]) }}" class="text-decoration-none">
                                    <button class="btn btn-teal w-100 mb-3 text-white fw-semibold">
                                        Lihat Events
                                    </button>
                                </a>

                                <h5 class="card-title fw-bold text-dark mb-3">
                                    {{ $event->title }}
                                </h5>

                                <ul class="list-unstyled text-muted small">
                                    <li class="mb-2 d-flex align-items-center">
                                        <i class="fas fa-calendar-alt text-teal me-2"></i>
                                        {{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y') }}
                                    </li>
                                    <li class="mb-2 d-flex align-items-center">
                                        <i class="fas fa-clock text-teal me-2"></i>
                                        {{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($event->end_datetime)->format('H:i') }}
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-teal me-2"></i>
                                        {{ $event->EventsLocation->city ?? 'Lokasi Tidak Diketahui' }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">Belum ada event aktif tersedia.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
