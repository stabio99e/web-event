@extends('users.layouts.app')

@section('content')
    <!-- Header -->
    <div class="no-print bg-teal-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-4xl font-bold mb-2">Cetak Sertifikat</h1>
                    <p class="text-xl opacity-90">Dapatkan sertifikat kehadiran event berdasarkan nomor tiket Anda</p>
                </div>
                <div class="hidden lg:block">
                    <i class="fas fa-certificate text-6xl opacity-30"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="no-print bg-white border-b border-gray-200 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('home') }}" class="text-primary-500 hover:text-primary-600 flex items-center">
                            <i class="fas fa-home mr-1"></i>Home
                        </a>
                    </li>
                     
                    <li class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Cetak Sertifikat</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search Section -->
        <div class="no-print bg-white rounded-xl shadow-sm p-8 mb-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-search text-primary-500 mr-2"></i>Cari Sertifikat Anda
                </h2>
                <p class="text-gray-600">Masukkan nomor tiket untuk mendapatkan sertifikat kehadiran event</p>
            </div>

            <form class="max-w-2xl mx-auto" id="certificateForm" onsubmit="event.preventDefault(); searchCertificate();">
                @csrf
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label for="ticketNumber" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-ticket-alt mr-1"></i>Nomor Tiket
                        </label>
                        <input type="text" name="ticket_number" id="ticketNumber"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="Contoh: TKT-2024-001234" required value="{{ old('ticket_number') }}">
                    </div>
                    <div class="md:w-48">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">&nbsp;</label>
                        <button type="submit"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:-translate-y-0.5">
                            <i class="fas fa-search mr-2"></i>Cari Sertifikat
                        </button>
                    </div>
                </div>
            </form>


            <!-- Process Steps -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-ticket-alt text-primary-500 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Masukkan Nomor Tiket</h3>
                    <p class="text-gray-600 text-sm">Nomor tiket yang valid</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Verifikasi Kehadiran</h3>
                    <p class="text-gray-600 text-sm">Sistem akan memverifikasi</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-download text-blue-500 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Download Sertifikat</h3>
                    <p class="text-gray-600 text-sm">Simpan sertifikat</p>
                </div>
            </div>


        </div>

        <!-- Loading State -->
        <div class="no-print bg-white rounded-xl shadow-sm p-12 text-center hidden" id="loadingState">
            <div class="w-12 h-12 border-4 border-gray-200 border-t-primary-500 rounded-full spinner mx-auto mb-4"></div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Mencari sertifikat...</h3>
            <p class="text-gray-600">Mohon tunggu sebentar</p>
        </div>

        <!-- Error State -->
        <div class="no-print bg-white rounded-xl shadow-sm p-12 text-center hidden" id="errorState">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-red-500 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Sertifikat Tidak Ditemukan</h3>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                Nomor tiket yang Anda masukkan tidak valid atau sertifikat belum tersedia.
                Pastikan Anda telah menghadiri event dan nomor tiket benar.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left max-w-md mx-auto">
                <h4 class="font-semibold text-blue-900 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>Kemungkinan Penyebab:
                </h4>
                <ul class="text-blue-800 text-sm space-y-1">
                    <li>• Nomor tiket salah atau tidak valid</li>
                    <li>• Anda belum menghadiri event</li>
                    <li>• Sertifikat belum diproses (tunggu 1-2 hari setelah event)</li>
                    <li>• Event tidak menyediakan sertifikat</li>
                </ul>
            </div>

            <button onclick="resetForm()"
                class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                <i class="fas fa-redo mr-2"></i>Coba Lagi
            </button>
        </div>

        <!-- Certificate Preview -->
        <div id="certificatePreview" class="hidden bg-white rounded-xl shadow-sm p-8 mb-8 fade-in">
            <div class="max-w-4xl mx-auto py-12 text-center">
                <h1 class="text-3xl font-bold mb-4">Sertifikat Atas Nama:</h1>
                <p class="text-xl text-gray-700 mb-6" id="participantName"></p>

                <img src="" id="certificateImage" alt="Sertifikat Preview" class="w-full rounded border">

                <div class="mt-6 space-x-4">
                    <a href="#" id="downloadLink" download
                        class="bg-green-600 text-white px-4 py-2 rounded inline-flex items-center">
                        <i class="fas fa-download mr-2"></i> Download
                    </a>
                </div>
            </div>
        </div>


    </div>
    <div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>
@endsection
@section('scripts')
    <script>
        function hideAllStates() {
            document.getElementById('loadingState')?.classList.add('hidden');
            document.getElementById('errorState')?.classList.add('hidden');
            document.getElementById('certificatePreview')?.classList.add('hidden');
        }

        function showError() {
            document.getElementById('errorState')?.classList.remove('hidden');
            showNotification('Sertifikat tidak ditemukan.', 'error');
        }

        function resetForm() {
            document.getElementById('ticketNumber').value = '';
            hideAllStates();
            document.getElementById('ticketNumber').focus();
        }


        function showNotification(message, type = 'info') {
            const container = document.getElementById('notificationContainer');
            const alert = document.createElement('div');

            let bgColor = 'bg-gray-700';
            if (type === 'success') bgColor = 'bg-green-600';
            else if (type === 'error') bgColor = 'bg-red-600';
            else if (type === 'warning') bgColor = 'bg-yellow-600';

            alert.className = `${bgColor} text-white px-4 py-2 rounded shadow fade-in`;
            alert.textContent = message;

            container.appendChild(alert);

            setTimeout(() => {
                alert.classList.add('fade-out');
                setTimeout(() => alert.remove(), 500);
            }, 4000);
        }

        function searchCertificate() {
            const ticketNumber = document.getElementById('ticketNumber').value.trim();

            if (!ticketNumber) {
                showNotification('Mohon masukkan nomor tiket', 'warning');
                return;
            }

            hideAllStates();
            document.getElementById('loadingState').classList.remove('hidden');

            fetch("{{ route('certificates.generate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ticket_number: ticketNumber
                    })
                })
                .then(async response => {
                    if (!response.ok) {
                        const errorText = await response.text();

                        // Cek jika 404, berarti tiket tidak ditemukan
                        if (response.status === 404) {
                            hideAllStates();
                            showError();
                            return;
                        }
 
                    }

                    return response.json();
                })
                .then(data => {
                    if (!data) return;  

                    hideAllStates();

                    if (data.certificate_base64 && data.participant_name) {
                        document.getElementById('certificatePreview').classList.remove('hidden');
                        document.getElementById('certificateImage').src = data.certificate_base64;
                        document.getElementById('participantName').textContent = data.participant_name;
                        document.getElementById('downloadLink').href = data.certificate_base64;
                        document.getElementById('downloadLink').download =
                            `sertifikat_${data.participant_name.replace(/\s+/g, '_')}.png`;
                        document.getElementById('certificatePreview').scrollIntoView({
                            behavior: 'smooth'
                        });
                        showNotification('Sertifikat berhasil ditemukan!', 'success');
                    } else {
                        showError();
                    }
                })
                .catch(error => {
                    hideAllStates();
                    showError();
                });

        }
    </script>
@endsection
