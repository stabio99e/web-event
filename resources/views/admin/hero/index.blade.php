@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Hero Slider Home</h1>
                <p></p>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Judul</th>
                                        <th scope="col">Subjudul</th>
                                        <th scope="col">Gambar</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Urutan</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($heros as $hero)
                                        <tr>
                                            <td>{{ $hero->title }}</td>
                                            <td>{{ $hero->subtitle }}</td>
                                            <td><img src="{{ asset($hero->image_url) }}" width="100"></td>
                                            <td>
                                                <span class="badge {{ $hero->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $hero->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $hero->sort_order }}</td>
                                            <td>
                                                <a href="{{ route('admin.hero.edit', $hero->id) }}"
                                                    class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('admin.hero.destroy', $hero->id) }}" method="POST"
                                                    style="display:inline-block;"
                                                    onsubmit="return confirm('Yakin ingin hapus?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
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
