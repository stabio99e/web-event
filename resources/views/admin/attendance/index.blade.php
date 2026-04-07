<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Peserta Event - {{ $event->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset($webConfig->favicon_path ?? '/storage/images/default.png') }}">
    <style>
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pulse-green {
            animation: pulseGreen 2s infinite;
        }

        @keyframes pulseGreen {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="font-inter bg-gray-50 text-gray-900">
    <!-- Header -->
    <div class="bg-teal-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-4xl font-bold mb-2">Daftar Peserta Event - {{ $event->title }}</h1>
                    <p class="text-xl opacity-90">Kelola absensi dan kehadiran peserta event</p>
                </div>
                <div class="hidden lg:block">
                    <i class="fas fa-users text-6xl opacity-30"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-200 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('admin.home') }}" class="text-primary-500 hover:text-primary-600 flex items-center">
                            <i class="fas fa-home mr-1"></i>Home
                        </a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('admin.events.details', ['eventsid' => $event->id ]) }}" class="text-primary-500 hover:text-primary-600">{{ $event->title }}</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Daftar Peserta</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Event Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $event->title }}</h2>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-calendar mr-2 text-primary-500"></i>
                            <span>{{ \Carbon\Carbon::parse($event->start_datetime)->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock mr-2 text-primary-500"></i>
                            <span>{{ \Carbon\Carbon::parse($event->start_datetime)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($event->end_datetime)->format('H:i') }} WIB</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-primary-500"></i>
                            <span>{{ $event->EventsLocation->name ?? '-' }},
                                {{ $event->EventsLocation->city ?? '' }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 lg:mt-0 lg:ml-6">
                    <div class="flex items-center space-x-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary-500">{{ $totalParticipants }}</div>
                            <div class="text-sm text-gray-600">Total Peserta</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-500">{{ $presentCount }}</div>
                            <div class="text-sm text-gray-600">Hadir</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-500">{{ $absentCount }}</div>
                            <div class="text-sm text-gray-600">Tidak Hadir</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Search -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="Cari nama, email, atau nomor tiket...">
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-3">
                    <select id="statusFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="all">Semua Status</option>
                        <option value="present">Hadir</option>
                        <option value="absent">Tidak Hadir</option>
                        <option value="pending">Belum Dicek</option>
                    </select>

                    <select id="categoryFilter"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="all">Semua Kategori</option>
                        @foreach ($ticketTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>

                    <button onclick="refreshData()"
                        class="bg-teal-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>


        </div>

        <!-- Participants List -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Peserta</h3>
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <span>Menampilkan</span>
                        <span class="font-semibold" id="showingCount">1-{{ min($participants->count(), 20) }}</span>
                        <span>dari</span>
                        <span class="font-semibold" id="totalCount">{{ $participants->count() }}</span>
                        <span>peserta</span>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll"
                                    class="rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Peserta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kontak
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tiket
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Waktu Check-in
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="participantsTable">
                        @foreach ($participants as $participant)
                            <tr class="hover:bg-gray-50 fade-in">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox"
                                        class="participant-checkbox rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                                        value="{{ $participant->ticket_id }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-primary-500"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $participant->name }}
                                            </div>
                                         </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $participant->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $participant->phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono text-gray-900">{{ $participant->ticket_number }}
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                        data-type-id="{{ $participant->ticket_type_id }}">
                                        {{ $participant->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap" data-status="{{ $participant->status }}">
                                    @if ($participant->status === 'present')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Hadir
                                        </span>
                                    @elseif($participant->status === 'absent')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Tidak Hadir
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Belum Dicek
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $participant->checkin_time ? \Carbon\Carbon::parse($participant->checkin_time)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button
                                            onclick="openAttendanceModal({{ $participant->ticket_id }}, '{{ $participant->name }}', '{{ $participant->email }}', '{{ $participant->status }}', `{{ $participant->note }}`)"
                                            class="text-primary-600 hover:text-primary-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-700">Tampilkan</span>
                        <select id="perPageSelect" class="border border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-700">per halaman</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button onclick="previousPage()"
                            class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-100 transition-colors disabled:opacity-50"
                            id="prevBtn" disabled>
                            <i class="fas fa-chevron-left mr-1"></i>Sebelumnya
                        </button>

                        <div class="flex items-center space-x-1" id="pageNumbers">
                            <button
                                class="px-3 py-1 border rounded text-sm bg-primary-500 text-white border-primary-500">1</button>
                        </div>

                        <button onclick="nextPage()"
                            class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-100 transition-colors disabled:opacity-50"
                            id="nextBtn" {{ $participants->count() <= 20 ? 'disabled' : '' }}>
                            Selanjutnya<i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Modal -->
    <div id="attendanceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Update Kehadiran</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-primary-500"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900" id="modalParticipantName"></div>
                            <div class="text-sm text-gray-600" id="modalParticipantEmail"></div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" name="attendanceStatus" value="present"
                                class="text-primary-500 focus:ring-primary-500">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span>Hadir</span>
                            </div>
                        </label>

                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" name="attendanceStatus" value="absent"
                                class="text-primary-500 focus:ring-primary-500">
                            <div class="flex items-center">
                                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                <span>Tidak Hadir</span>
                            </div>
                        </label>

                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="radio" name="attendanceStatus" value="pending"
                                class="text-primary-500 focus:ring-primary-500">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-yellow-500 mr-2"></i>
                                <span>Belum Dicek</span>
                            </div>
                        </label>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea id="attendanceNote"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            rows="3" placeholder="Tambahkan catatan..."></textarea>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button onclick="closeModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button onclick="saveAttendance()"
                        class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2 px-4 rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        let currentParticipantId = null;
        let currentPage = 1;
        let perPage = 20;
        document.getElementById('searchInput').addEventListener('input', filterParticipants);
        document.getElementById('statusFilter').addEventListener('change', filterParticipants);
        document.getElementById('categoryFilter').addEventListener('change', filterParticipants);

        function filterParticipants() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const statusValue = document.getElementById('statusFilter').value;
            const categoryValue = document.getElementById('categoryFilter').value;

            const rows = document.querySelectorAll('#participantsTable tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(2) .text-sm.font-medium')?.textContent.toLowerCase() ||
                    '';
                const email = row.querySelector('td:nth-child(3) .text-sm.text-gray-900')?.textContent
                    .toLowerCase() || '';
                const ticket = row.querySelector('td:nth-child(4) .font-mono')?.textContent.toLowerCase() || '';
                const statusCell = row.querySelector('td:nth-child(5)');
                const status = statusCell?.dataset.status || '';
                const categoryBadge = row.querySelector('td:nth-child(4) .inline-flex');
                const categoryText = categoryBadge?.textContent.trim().toLowerCase() || '';
                const categoryId = categoryBadge?.dataset.typeId || '';

                let isVisible = true;

                if (searchValue && !(name.includes(searchValue) || email.includes(searchValue) || ticket.includes(
                        searchValue))) {
                    isVisible = false;
                }
                if (statusValue !== 'all' && status !== statusValue) {
                    isVisible = false;
                }

                if (categoryValue !== 'all' && categoryId !== categoryValue) {
                    isVisible = false;
                }

                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });

            document.getElementById('showingCount').textContent = `1-${visibleCount}`;
        }



        function openAttendanceModal(id, name, email, status, note) {
            currentParticipantId = id;
            document.getElementById('modalParticipantName').textContent = name;
            document.getElementById('modalParticipantEmail').textContent = email;
            document.getElementById('attendanceNote').value = note || '';

            const statusRadio = document.querySelector(`input[name="attendanceStatus"][value="${status}"]`);
            if (statusRadio) {
                statusRadio.checked = true;
            }

            document.getElementById('attendanceModal').classList.remove('hidden');
            document.getElementById('attendanceModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('attendanceModal').classList.add('hidden');
            document.getElementById('attendanceModal').classList.remove('flex');
            currentParticipantId = null;
        }

        function saveAttendance() {
            if (!currentParticipantId) return;

            const selectedStatus = document.querySelector('input[name="attendanceStatus"]:checked');
            const note = document.getElementById('attendanceNote').value;

            if (!selectedStatus) {
                showNotification('Pilih status kehadiran', 'warning');
                return;
            }

            fetch("{{ route('admin.events.attendance.update', ['event' => $event->id, 'ticket' => 'PLACEHOLDER']) }}"
                    .replace('PLACEHOLDER', currentParticipantId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status: selectedStatus.value,
                            note: note
                        })
                    })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification('Status kehadiran berhasil diperbarui', 'success');
                        location.reload();
                    } else {
                        showNotification('Gagal memperbarui kehadiran: ' + (data.message || ''), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan saat menyimpan!', 'error');
                });
        }


        function getSelectedParticipantIds() {
            const checkboxes = document.querySelectorAll('.participant-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        function refreshData() {
            showNotification('Memperbarui data...', 'info');
            location.reload();
        }




        function showNotification(message, type = 'success') {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');

            const colors = {
                success: 'bg-green-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500',
                error: 'bg-red-500'
            };

            const icons = {
                success: 'fa-check-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle',
                error: 'fa-times-circle'
            };

            notification.className =
                `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg flex items-center min-w-80 transform transition-all duration-300 translate-x-full slide-in`;
            notification.innerHTML = `
                <i class="fas ${icons[type]} mr-3"></i>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Auto remove after 4 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 4000);
        }

        // Handle select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.participant-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Handle per page change
        document.getElementById('perPageSelect').addEventListener('change', function() {
            perPage = parseInt(this.value);
            showNotification(`Menampilkan ${perPage} peserta per halaman`, 'info');
        });

        // Pagination functions
        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                showNotification(`Pindah ke halaman ${currentPage}`, 'info');
            }
        }

        function nextPage() {
            const totalPages = Math.ceil({{ $participants->count() }} / perPage);
            if (currentPage < totalPages) {
                currentPage++;
                showNotification(`Pindah ke halaman ${currentPage}`, 'info');
            }
        }
    </script>
</body>

</html>
