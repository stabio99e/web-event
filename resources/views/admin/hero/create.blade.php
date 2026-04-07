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

                        <form method="POST" action="{{ route('admin.hero.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label>Title</label>
                                <input name="title" class="form-control" />
                            </div>

                            <div class="mb-3">
                                <label>Subtitle</label>
                                <textarea name="subtitle" class="form-control"></textarea>
                            </div>

                            <div class="mb-3">
                                <label>Upload Gambar</label>
                                <input type="file" name="image" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Status</label>
                                <select name="is_active" class="form-control">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0" required>
                            </div>

                            <div class="mb-3">
                                <label>Buttons</label>
                                <div id="button-list"></div>
                                <button type="button" class="btn btn-primary mt-2" id="add-button">+ Tambah Button</button>

                            </div>

                            <button type="submit" class="btn btn-success mt-3">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        let buttonIndex = 0;

        document.getElementById('add-button').addEventListener('click', function() {
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

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-button')) {
                e.target.parentElement.remove();
            }
        });
    </script>
@endsection
