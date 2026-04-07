@extends('admin.layouts.app')

@section('content')
    <!--Content Start-->
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Seputar Pertanyaan dan Jawaban</h1>
                <p>Daftar pertanyaan dan jawabannya</p>
            </div>

            <div class="col-md-12">
                <div class="card">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Pertanyaan</th>
                                        <th>Jawaban</th>
                                        <th>Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($qnas as $index => $qna)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $qna->question }}</td>
                                            <td>{{ $qna->answer ?? '-' }}</td>
                                            <td>{{ $qna->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.qnas.edit', $qna->id) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('admin.qnas.destroy', $qna->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus Q&A ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada data Q&A.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
