@extends('admin.layouts.app')

@section('content')
<div class="content-start transition">
    <div class="container-fluid dashboard">
        <div class="content-header">
            <h1>Edit Hero Slider</h1>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.hero.update', $slider->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label>Title</label>
                            <input type="text" name="title" value="{{ old('title', $slider->title) }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Subtitle</label>
                            <textarea name="subtitle" class="form-control">{{ old('subtitle', $slider->subtitle) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label>Upload Gambar</label>
                            @if ($slider->image_url)
                                <div class="mb-2">
                                    <img src="{{ asset($slider->image_url) }}" class="img-thumbnail" width="200">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar</small>
                        </div>

                        <div class="mb-3">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" {{ $slider->is_active ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$slider->is_active ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $slider->sort_order) }}" placeholder="Kosongkan untuk otomatis">
                        </div>

                        <div class="mb-3">
                            <label>Buttons</label>
                            <div id="button-list">
                                @php
                                    $buttons = old('buttons', $slider->buttons ?? []);
                                @endphp
                                @foreach ($buttons as $i => $btn)
                                    <div class="d-flex gap-2 mb-2">
                                        <select name="buttons[{{ $i }}][icon]" class="form-control w-25">
                                            @foreach (['instagram', 'facebook', 'youtube', 'tiktok'] as $icon)
                                                <option value="{{ $icon }}" {{ $btn['icon'] === $icon ? 'selected' : '' }}>
                                                    {{ ucfirst($icon) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="buttons[{{ $i }}][label]" value="{{ $btn['label'] ?? '' }}" class="form-control w-25" placeholder="Label (opsional)">
                                        <input type="url" name="buttons[{{ $i }}][link]" value="{{ $btn['link'] ?? '' }}" class="form-control w-50" placeholder="https://example.com">
                                        <button type="button" class="btn btn-danger remove-button">X</button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-primary mt-2" id="add-button">+ Tambah Button</button>
                        </div>

                        <button type="submit" class="btn btn-success mt-3">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    let buttonIndex = {{ count($buttons) }};

    document.getElementById('add-button').addEventListener('click', function () {
        const wrapper = document.getElementById('button-list');
        const div = document.createElement('div');
        div.classList.add('d-flex', 'gap-2', 'mb-2');

        div.innerHTML = `
            <select name="buttons[${buttonIndex}][icon]" class="form-control w-25">
                <option value="instagram">Instagram</option>
                <option value="facebook">Facebook</option>
                <option value="youtube">YouTube</option>
                <option value="tiktok">TikTok</option>
            </select>
            <input type="text" name="buttons[${buttonIndex}][label]" class="form-control w-25" placeholder="Label (opsional)">
            <input type="url" name="buttons[${buttonIndex}][link]" class="form-control w-50" placeholder="https://example.com">
            <button type="button" class="btn btn-danger remove-button">X</button>
        `;
        wrapper.appendChild(div);
        buttonIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-button')) {
            e.target.closest('div').remove();
        }
    });
</script>
@endsection
