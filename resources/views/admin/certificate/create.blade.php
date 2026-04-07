@extends('admin.layouts.app')

@section('content')
<div class="content-start transition">
    <div class="container-fluid dashboard">
        <div class="content-header">
            <h1>Tambah Sertifikat</h1>
        </div>

        <div class="row">
            <!-- Form Sertifikat -->
            <div class="col-md-6">
                <div class="card">
                    <form id="formCertificate" action="{{ route('admin.certificate.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-header"><h4>Form Sertifikat</h4></div>
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
                                <select name="event_id" id="event_id" class="form-control" required>
                                    <option value="">Pilih Event</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event->id }}">{{ $event->title }}</option>
                                    @endforeach
                                </select>
                                @if ($events->isEmpty())
                                    <small class="text-danger">Semua event sudah memiliki template sertifikat.</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label>Nama Sertifikat</label>
                                <input type="text" name="name" id="name" class="form-control preview-input" required>
                            </div>

                            <div class="mb-3">
                                <label>Template Sertifikat (Gambar)</label>
                                <input type="file" name="image" id="templateImage" class="form-control preview-input" accept="image/*" required>
                            </div>

                            <div class="mb-3">
                                <label>Font (TTF/OTF)</label>
                                <input type="file" name="font" id="fontFile" class="form-control preview-input" accept=".ttf,.otf">
                            </div>

                            <div class="mb-3">
                                <label>Ukuran Font</label>
                                <input type="number" name="font_size" id="fontSize" class="form-control preview-input" value="36" required>
                            </div>

                            <div class="mb-3">
                                <label>Posisi X</label>
                                <input type="text" name="position_x" id="posX" class="form-control preview-input" value="auto" required>
                                <small class="text-muted">Gunakan <code>auto</code> untuk tengah otomatis</small>
                            </div>

                            <div class="mb-3">
                                <label>Posisi Y</label>
                                <input type="number" name="position_y" id="posY" class="form-control preview-input" value="510" required>
                            </div>

                            <div class="mb-3">
                                <label>Warna Teks</label>
                                <input type="color" name="text_color" id="textColor" class="form-control preview-input" value="#000000" required>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1">
                                <label class="form-check-label" for="is_active">Aktifkan Sertifikat</label>
                            </div>

                        </div>

                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">Simpan Sertifikat</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Area -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h4>Preview Sertifikat</h4></div>
                    <div class="card-body text-center">
                        <canvas id="certificateCanvas" width="1200" height="800" style="border: 1px solid #ddd; max-width: 100%; height: auto;"></canvas>
                        <small class="text-muted d-block mt-2">Perubahan akan langsung terlihat di atas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Preview -->
<script>
let fontLoaded = false;

function loadFontFromFile(file, fontName) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const font = new FontFace(fontName, e.target.result);
            font.load().then(loadedFont => {
                document.fonts.add(loadedFont);
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

        let x = 0;
        if (posX === 'auto') {
            const textWidth = ctx.measureText(name).width;
            x = (canvas.width - textWidth) / 2;
        } else {
            x = parseInt(posX);
        }

        ctx.fillText(name, x, posY);
    };

    const drawImageAndText = (img) => {
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        drawText();
    };

    if (imageInput.files.length > 0) {
        const img = new Image();
        const reader = new FileReader();
        reader.onload = function (e) {
            img.onload = function () {
                if (fontInput.files.length > 0 && !fontLoaded) {
                    loadFontFromFile(fontInput.files[0], 'CustomFont').then(() => {
                        fontLoaded = true;
                        drawImageAndText(img);
                    });
                } else {
                    drawImageAndText(img);
                }
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(imageInput.files[0]);
    }
}

// Trigger render when inputs change
document.querySelectorAll('.preview-input').forEach(el => {
    el.addEventListener('input', renderCertificate);
    el.addEventListener('change', renderCertificate);
});
</script>
@endsection
