@extends('admin.layouts.app')

@section('content')
    <div class="content-start transition">
        <div class="container-fluid dashboard">
            <div class="content-header">
                <h1>Tambah Event Baru</h1>
                <p></p>
            </div>

            <div class="row">
                <div class="col-12">
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

                            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data"
                                id="event-form">
                                @csrf

                                <div class="row">
                                    <!-- Informasi Dasar Event -->
                                    <div class="col-md-8">
                                        <div class="card mb-4">
                                            <div class="card-header text-white mb-4">
                                                <h5 class="mb-0">Informasi Dasar Event</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Judul Event *</label>
                                                    <input type="text"
                                                        class="form-control @error('title') is-invalid @enderror"
                                                        id="title" name="title" value="{{ old('title') }}" required>
                                                    @error('title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="description" class="form-label">Deskripsi Singkat *</label>
                                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                        rows="3" required>{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">*Singkat untuk seo</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="content" class="form-label">Konten Lengkap</label>
                                                    <textarea class="form-control @error('content') is-invalid @enderror" id="editor" name="content" rows="6">{{ old('content') }}</textarea>
                                                    @error('content')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Detail lengkap tentang event Anda</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="image" class="form-label">Gambar Event</label>
                                                    <input type="file"
                                                        class="form-control @error('image') is-invalid @enderror"
                                                        id="image" name="image" accept="image/*">
                                                    @error('image')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div id="image-preview" class="mt-2"></div>
                                                    <small class="text-muted">Format: JPG, PNG (Max: 5MB)</small>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="link_group" class="form-label">Link Group Sosmed</label>
                                                    <input type="text"
                                                        class="form-control @error('link_group') is-invalid @enderror"
                                                        id="link_group" name="link_group" value="{{ old('link_group') }}">
                                                    @error('link_group')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">*Kosongkan jika blum ada</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Waktu Event -->
                                        <div class="card mb-4">
                                            <div class="card-header text-white mb-4">
                                                <h5 class="mb-0">Waktu Event</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="start_datetime" class="form-label">Tanggal & Waktu Mulai
                                                            *</label>
                                                        <input type="datetime-local"
                                                            class="form-control @error('start_datetime') is-invalid @enderror"
                                                            id="start_datetime" name="start_datetime"
                                                            value="{{ old('start_datetime') }}" required>
                                                        @error('start_datetime')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="end_datetime" class="form-label">Tanggal & Waktu
                                                            Selesai</label>
                                                        <input type="datetime-local"
                                                            class="form-control @error('end_datetime') is-invalid @enderror"
                                                            id="end_datetime" name="end_datetime"
                                                            value="{{ old('end_datetime') }}">
                                                        @error('end_datetime')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <label for="max_attendees" class="form-label">Kapasitas Maksimal
                                                            Peserta</label>
                                                        <input type="number"
                                                            class="form-control @error('max_attendees') is-invalid @enderror"
                                                            id="max_attendees" name="max_attendees" min="0"
                                                            value="{{ old('max_attendees', 1) }}">
                                                        @error('max_attendees')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <small class="text-muted">*Max ini untuk seluruh events dan di bagi
                                                            ke jenis tiket</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Lokasi Event -->
                                        <div class="card mb-4">
                                            <div class="card-header text-white mb-4">
                                                <h5 class="mb-0">Lokasi Event</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="location_name" class="form-label">Nama Venue *</label>
                                                    <input type="text"
                                                        class="form-control @error('location_name') is-invalid @enderror"
                                                        id="location_name" name="location_name"
                                                        value="{{ old('location_name') }}" required>
                                                    @error('location_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="location_address" class="form-label">Alamat Lengkap
                                                        *</label>
                                                    <textarea class="form-control @error('location_address') is-invalid @enderror" id="location_address"
                                                        name="location_address" rows="3" required>{{ old('location_address') }}</textarea>
                                                    @error('location_address')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="location_city" class="form-label">Kota *</label>
                                                        <input type="text"
                                                            class="form-control @error('location_city') is-invalid @enderror"
                                                            id="location_city" name="location_city"
                                                            value="{{ old('location_city') }}" required>
                                                        @error('location_city')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="location_province" class="form-label">Provinsi
                                                            *</label>
                                                        <select
                                                            class="form-select @error('location_province') is-invalid @enderror"
                                                            id="location_province" name="location_province" required>
                                                            <option value="">Pilih Provinsi</option>
                                                            @foreach (['DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Banten', 'Yogyakarta', 'Bali', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Sumatera Selatan', 'Lampung', 'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Papua', 'Papua Barat'] as $province)
                                                                <option value="{{ $province }}"
                                                                    {{ old('location_province') == $province ? 'selected' : '' }}>
                                                                    {{ $province }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('location_province')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="location_country" class="form-label">Negara</label>
                                                    <input type="text"
                                                        class="form-control @error('location_country') is-invalid @enderror"
                                                        id="location_country" name="location_country"
                                                        value="{{ old('location_country', 'Indonesia') }}">
                                                    @error('location_country')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="location_map_url" class="form-label">Link Peta (Google
                                                        Maps)</label>
                                                    <input type="url"
                                                        class="form-control @error('location_map_url') is-invalid @enderror"
                                                        id="location_map_url" name="location_map_url"
                                                        placeholder="https://maps.google.com/..."
                                                        value="{{ old('location_map_url') }}">
                                                    @error('location_map_url')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tiket -->
                                    <div class="col-md-4">
                                        <div class="card mb-4">
                                            <div class="card-header text-white mb-4">
                                                <h5 class="mb-0">Tiket</h5>
                                            </div>
                                            <div class="card-body">
                                                <div id="ticket-types-container">
                                                    @php
                                                        $oldTickets = old('ticket_types', [
                                                            [
                                                                'name' => '',
                                                                'description' => '',
                                                                'price' => '',
                                                                'quantity_available' => '',
                                                                'is_premium' => false,
                                                            ],
                                                        ]);
                                                    @endphp

                                                    @foreach ($oldTickets as $index => $ticket)
                                                        <div class="ticket-type mb-4 p-3 border rounded">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <h6 class="mb-0">Tiket #{{ $index + 1 }}</h6>
                                                                @if ($index > 0)
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger remove-ticket">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                @endif
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Nama Tiket *</label>
                                                                <input type="text"
                                                                    class="form-control @if ($errors->has('ticket_types.' . $index . '.name')) is-invalid @endif"
                                                                    name="ticket_types[{{ $index }}][name]"
                                                                    value="{{ $ticket['name'] }}" required>
                                                                @if ($errors->has('ticket_types.' . $index . '.name'))
                                                                    <div class="invalid-feedback">
                                                                        {{ $errors->first('ticket_types.' . $index . '.name') }}
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Deskripsi</label>
                                                                <textarea class="form-control @if ($errors->has('ticket_types.' . $index . '.description')) is-invalid @endif"
                                                                    name="ticket_types[{{ $index }}][description]" rows="2">{{ $ticket['description'] }}</textarea>
                                                                @if ($errors->has('ticket_types.' . $index . '.description'))
                                                                    <div class="invalid-feedback">
                                                                        {{ $errors->first('ticket_types.' . $index . '.description') }}
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Harga (Rp) *</label>
                                                                    <input type="number"
                                                                        class="form-control @if ($errors->has('ticket_types.' . $index . '.price')) is-invalid @endif"
                                                                        name="ticket_types[{{ $index }}][price]"
                                                                        min="0" value="{{ $ticket['price'] }}"
                                                                        required>
                                                                    @if ($errors->has('ticket_types.' . $index . '.price'))
                                                                        <div class="invalid-feedback">
                                                                            {{ $errors->first('ticket_types.' . $index . '.price') }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Kuota *</label>
                                                                    <input type="number"
                                                                        class="form-control @if ($errors->has('ticket_types.' . $index . '.quantity_available')) is-invalid @endif"
                                                                        name="ticket_types[{{ $index }}][quantity_available]"
                                                                        min="0"
                                                                        value="{{ $ticket['quantity_available'] }}"
                                                                        required>
                                                                    @if ($errors->has('ticket_types.' . $index . '.quantity_available'))
                                                                        <div class="invalid-feedback">
                                                                            {{ $errors->first('ticket_types.' . $index . '.quantity_available') }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="form-check mb-3">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="ticket_types[{{ $index }}][is_premium]"
                                                                    id="ticket_premium_{{ $index }}"
                                                                    value="1"
                                                                    {{ isset($ticket['is_premium']) && $ticket['is_premium'] ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="ticket_premium_{{ $index }}">
                                                                    Tiket Premium (VIP)
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <button type="button" id="add-ticket-type"
                                                    class="btn btn-outline-primary w-100">
                                                    <i class="fas fa-plus me-2"></i>Tambah Jenis Tiket
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Tombol Submit -->
                                        <div class="card">
                                            <div class="card-body">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-save me-2"></i>Simpan Event
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/modules/ckeditor/ckeditor.js') }}"></script>
    <script>
        // Inisialisasi CKEditor
        ClassicEditor
            .create(document.querySelector('#editor'), {
                removePlugins: ['ImageUpload', 'MediaEmbed', 'Table', 'TableToolbar'],
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                        'undo', 'redo'
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });

        document.addEventListener("DOMContentLoaded", function() {
            let ticketCount =
                {{ count(old('ticket_types', [['name' => '', 'description' => '', 'price' => '', 'quantity_available' => '', 'is_premium' => false]])) }};

            function addNewTicket() {
                const container = document.getElementById('ticket-types-container');
                const index = ticketCount;

                const newTicketHtml = `
                <div class="ticket-type mb-4 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Tiket #${ticketCount + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-ticket">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Tiket *</label>
                        <input type="text" class="form-control" name="ticket_types[${index}][name]" required>
                        <div class="invalid-feedback" id="ticket_types_${index}_name_error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="ticket_types[${index}][description]" rows="2"></textarea>
                        <div class="invalid-feedback" id="ticket_types_${index}_description_error"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga (Rp) *</label>
                            <input type="number" class="form-control" name="ticket_types[${index}][price]" min="0" required>
                            <div class="invalid-feedback" id="ticket_types_${index}_price_error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kuota *</label>
                            <input type="number" class="form-control" name="ticket_types[${index}][quantity_available]" min="0" required>
                            <div class="invalid-feedback" id="ticket_types_${index}_quantity_error"></div>
                        </div>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="ticket_types[${index}][is_premium]" id="ticket_premium_${index}" value="1">
                        <label class="form-check-label" for="ticket_premium_${index}">
                            Tiket Premium (VIP)
                        </label>
                    </div>
                </div>
                `;

                container.insertAdjacentHTML('beforeend', newTicketHtml);
                ticketCount++;
                if (ticketCount > 1) {
                    document.querySelectorAll('.remove-ticket').forEach(btn => {
                        btn.style.display = 'block';
                    });
                }
            }

            function removeTicket(event) {
                const ticketToRemove = event.target.closest('.ticket-type');
                if (ticketToRemove) {
                    ticketToRemove.remove();
                    ticketCount--;

                    document.querySelectorAll('.ticket-type').forEach((ticket, index) => {
                        ticket.querySelector('h6').textContent = `Tiket #${index + 1}`;

                        const inputs = ticket.querySelectorAll('input, textarea');
                        inputs.forEach(input => {
                            const name = input.name.replace(/\[\d+\]/, `[${index}]`);
                            input.name = name;
                        });

                        const checkbox = ticket.querySelector('input[type="checkbox"]');
                        if (checkbox) {
                            const newId = `ticket_premium_${index}`;
                            checkbox.id = newId;
                            ticket.querySelector('label[for^="ticket_premium_"]').htmlFor = newId;
                        }
                    });

                    if (ticketCount === 1) {
                        document.querySelector('.remove-ticket').style.display = 'none';
                    }
                }
            }

            document.getElementById('add-ticket-type').addEventListener('click', addNewTicket);

            document.getElementById('ticket-types-container').addEventListener('click', function(e) {
                if (e.target.closest('.remove-ticket')) {
                    removeTicket(e);
                }
            });

            // Preview gambar yang diupload
            document.getElementById('image').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const preview = document.getElementById('image-preview');
                        preview.innerHTML =
                            `<img src="${event.target.result}" class="img-fluid rounded mt-2" style="max-height: 200px;" alt="Preview">`;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Validasi form sebelum submit
            document.getElementById('event-form').addEventListener('submit', function(e) {
                // Validasi minimal 2 tiket, maksimal 4 tiket
                if (ticketCount < 2) {
                    e.preventDefault();
                    alert(
                        'Anda harus menambahkan minimal 2 jenis tiket (misalnya Tiket Reguler dan Tiket VIP)');
                    return false;
                }

                if (ticketCount > 4) {
                    e.preventDefault();
                    alert('Maksimal hanya boleh 4 jenis tiket');
                    return false;
                }


                const startDate = new Date(document.getElementById('start_datetime').value);
                const endDate = new Date(document.getElementById('end_datetime').value);

                if (endDate && endDate <= startDate) {
                    e.preventDefault();
                    alert('Waktu selesai harus setelah waktu mulai');
                    return false;
                }

                const maxAttendees = parseInt(document.getElementById('max_attendees').value);
                if (maxAttendees < 0) {
                    e.preventDefault();
                    alert('Kapasitas maksimal tidak boleh negatif');
                    return false;
                }
            });

            // Tampilkan error validasi untuk ticket types jika ada
            @if ($errors->any())
                @foreach (old('ticket_types', []) as $index => $ticket)
                    @if ($errors->has('ticket_types.' . $index . '.name'))
                        document.getElementById('ticket_types_{{ $index }}_name_error').textContent =
                            '{{ $errors->first('ticket_types.' . $index . '.name') }}';
                        document.querySelector('[name="ticket_types[{{ $index }}][name]"]').classList.add(
                            'is-invalid');
                    @endif
                    @if ($errors->has('ticket_types.' . $index . '.description'))
                        document.getElementById('ticket_types_{{ $index }}_description_error').textContent =
                            '{{ $errors->first('ticket_types.' . $index . '.description') }}';
                        document.querySelector('[name="ticket_types[{{ $index }}][description]"]').classList
                            .add('is-invalid');
                    @endif
                    @if ($errors->has('ticket_types.' . $index . '.price'))
                        document.getElementById('ticket_types_{{ $index }}_price_error').textContent =
                            '{{ $errors->first('ticket_types.' . $index . '.price') }}';
                        document.querySelector('[name="ticket_types[{{ $index }}][price]"]').classList.add(
                            'is-invalid');
                    @endif
                    @if ($errors->has('ticket_types.' . $index . '.quantity_available'))
                        document.getElementById('ticket_types_{{ $index }}_quantity_error').textContent =
                            '{{ $errors->first('ticket_types.' . $index . '.quantity_available') }}';
                        document.querySelector('[name="ticket_types[{{ $index }}][quantity_available]"]')
                            .classList.add('is-invalid');
                    @endif
                @endforeach
            @endif
        });
    </script>
@endsection
