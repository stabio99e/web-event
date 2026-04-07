@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Pages</h1>
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
                                        <th scope="col">slug</th>
                                        <th scope="col">Judul</th>
                                        <th scope="col">Publikasi</th>
                                        <th scope="col">Tampilan Ke</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($getPages as $Pages)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $Pages->slug }}</td>
                                            <td>{{ $Pages->title }}</td>
                                            <td>
                                                @if ($Pages->is_published == 1)
                                                    Di Publish
                                                @else
                                                    Tidak Di Publish
                                                @endif
                                            </td>
                                            <td>{{ $Pages->order }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.pages.edit', $Pages->id) }}"
                                                        class="btn btn-sm btn-primary">Edit</a>
                                                    <form action="{{ route('admin.pages.destroy', $Pages->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus halaman ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                    </form>
                                                </div>
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
