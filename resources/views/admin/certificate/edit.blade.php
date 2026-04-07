@extends('admin.layouts.app')

@section('content')
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Edit Sertifikat</h1>
            </div>

            <div class="row">
                <!-- Form Edit Sertifikat -->
                <div class="col-md-6">
                    <div class="card">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form id="formCertificate" action="{{ route('admin.certificate.update', $certificate->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="card-header">
                                <h4>Form Sertifikat</h4>
                            </div>
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

                                <div class="mb-3">
                                    <label>Nama Event</label>
                                    <select name="event_id" class="form-control" required>
                                        @foreach ($events as $event)
                                            <option value="{{ $event->id }}"
                                                {{ old('event_id', $certificate->event_id) == $event->id ? 'selected' : '' }}>
                                                {{ $event->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label>Nama Sertifikat</label>
                                    <input type="text" name="name" id="name" class="form-control preview-input"
                                        value="{{ old('name', $certificate->name) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label>Gambar Sertifikat (Template)</label>
                                    <input type="file" name="image" id="templateImage"
                                        class="form-control preview-input" accept="image/*">
                                    @if ($certificate->image_path)
                                        <small class="text-muted">Saat ini: <a href="{{ asset($certificate->image_path) }}"
                                                target="_blank">Lihat Gambar</a></small>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label>Font Sertifikat (.ttf/.otf)</label>
                                    <input type="file" name="font" id="fontFile" class="form-control preview-input"
                                        accept=".ttf,.otf">
                                    @if ($certificate->font_path)
                                        <small class="text-muted">Saat ini: <a href="{{ asset($certificate->font_path) }}"
                                                target="_blank">Lihat Font</a></small>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label>Font Size</label>
                                    <input type="number" name="font_size" id="fontSize" class="form-control preview-input"
                                        value="{{ old('font_size', $certificate->font_size) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label>Posisi X</label>
                                    <input type="text" name="position_x" id="posX"
                                        class="form-control preview-input"
                                        value="{{ old('position_x', $certificate->position_x) }}" required>
                                    <small class="text-muted">Gunakan <code>auto</code> untuk tengah otomatis</small>
                                </div>

                                <div class="mb-3">
                                    <label>Posisi Y</label>
                                    <input type="number" name="position_y" id="posY"
                                        class="form-control preview-input"
                                        value="{{ old('position_y', $certificate->position_y) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label>Warna Teks</label>
                                    <input type="color" name="text_color" id="textColor"
                                        class="form-control preview-input"
                                        value="{{ old('text_color', $certificate->text_color) }}" required>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" {{ old('is_active', $certificate->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktifkan Sertifikat</label>
                                </div>

                            </div>
                            <div class="card-footer text-end">
                                <a href="{{ route('admin.certificate.show') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Perbarui Sertifikat</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Realtime -->
                <div class="col-md-6">
                    <div class="card mt-3 mt-md-0">
                        <div class="card-header">
                            <h4>Preview Sertifikat</h4>
                        </div>
                        <div class="card-body text-center">
                            <canvas id="certificateCanvas" width="1200" height="800"
                                style="border: 1px solid #ccc; max-width: 100%; height: auto;"></canvas>
                            <small class="text-muted d-block mt-2">Perubahan langsung ditampilkan di atas</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        let fontLoaded = false;
        let fallbackFontUrl = "{{ asset($certificate->font_path) }}";
        let fallbackImageUrl = "{{ asset($certificate->image_path) }}";

        function loadFontFromURL(url, fontName = 'CustomFont') {
            const font = new FontFace(fontName, `url(${url})`);
            return font.load().then(f => {
                document.fonts.add(f);
                return f;
            });
        }

        function loadFontFromFile(file, fontName) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const font = new FontFace(fontName, e.target.result);
                    font.load().then(f => {
                        document.fonts.add(f);
                        resolve();
                    }).catch(reject);
                };
                reader.onerror = reject;
                reader.readAsArrayBuffer(file);
            });
        }

        function renderCertificate() {
            const canvas = document.getElementById('certificateCanvas');
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            const name = document.getElementById('name').value;
            const fontSize = parseInt(document.getElementById('fontSize').value);
            const posX = document.getElementById('posX').value;
            const posY = parseInt(document.getElementById('posY').value);
            const textColor = document.getElementById('textColor').value;
            const fontInput = document.getElementById('fontFile');
            const imageInput = document.getElementById('templateImage');

            const drawText = () => {
                ctx.fillStyle = textColor;
                ctx.font = `${fontSize}px CustomFont, sans-serif`;
                let x = posX === 'auto' ? (canvas.width - ctx.measureText(name).width) / 2 : parseInt(posX);
                ctx.fillText(name, x, posY);
            };

            const drawImageAndText = (img) => {
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                drawText();
            };

            const loadAndDraw = (imgUrl) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => {
                    if (fontInput.files.length > 0) {
                        loadFontFromFile(fontInput.files[0], 'CustomFont').then(() => {
                            drawImageAndText(img);
                        }).catch(() => {
                            console.error('Gagal memuat font.');
                            drawImageAndText(img);  
                        });
                    } else {
                        loadFontFromURL(fallbackFontUrl).then(() => {
                            drawImageAndText(img);
                        }).catch(() => {
                            console.error('Gagal memuat font bawaan.');
                            drawImageAndText(img);
                        });
                    }

                };
                img.src = imgUrl;
            };

            if (imageInput.files.length > 0) {
                const reader = new FileReader();
                reader.onload = (e) => loadAndDraw(e.target.result);
                reader.readAsDataURL(imageInput.files[0]);
            } else {
                loadAndDraw(fallbackImageUrl);
            }

            if (!fontLoaded && fontInput.files.length === 0) {
                loadFontFromURL(fallbackFontUrl).then(() => fontLoaded = true);
            }
        }

        // Event listener
        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.preview-input').forEach(el => {
                el.addEventListener('input', renderCertificate);
                el.addEventListener('change', renderCertificate);
            });
            renderCertificate();
        });
    </script>
@endsection
