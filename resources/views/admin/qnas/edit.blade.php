@extends('admin.layouts.app')

@section('content')
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Edit Q&A</h1>
                <p>Perbarui pertanyaan dan jawabannya</p>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.qnas.update', $qna->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="question" class="form-label">Pertanyaan</label>
                                <input type="text" name="question"
                                    class="form-control @error('question') is-invalid @enderror"
                                    value="{{ old('question', $qna->question) }}" required>
                                @error('question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="answer" class="form-label">Jawaban</label>
                                <textarea name="answer" class="form-control @error('answer') is-invalid @enderror" rows="4" required>{{ old('answer', $qna->answer) }}</textarea>
                                @error('answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Perbarui</button>
                            <a href="{{ route('admin.qnas.show') }}" class="btn btn-secondary">Kembali</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
