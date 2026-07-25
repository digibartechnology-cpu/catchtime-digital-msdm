<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Absensi</title>
    <link rel="icon" href="{{ asset('images/Logo_icon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans" x-data="{ sidebarOpen: false, logoutModalOpen: false, detailAbsenOpen: false, absenData: {}, deleteModalOpen: false, deleteUrl: '' }">

    <div class="flex h-screen overflow-hidden">
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl transform transition-transform duration-300 ease-in-out border-r border-gray-200 flex flex-col h-full">
            
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-red-700">DigiBAR MSDM</h2>
                <button @click="sidebarOpen = false" class="text-gray-400 hover:text-red-600 text-3xl font-bold transition-colors focus:outline-none">&times;</button>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="/admin/dashboard" class="block px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-colors">Dashboard Overview</a>
                <a href="/admin/karyawan" class="block px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-colors">Data Karyawan</a>
                <a href="/admin/absensi" class="block px-4 py-3 bg-red-50 text-red-700 rounded-lg font-semibold border-l-4 border-red-600">Data Absensi</a>
                <a href="/admin/buku-tamu" class="block px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-colors">Buku Tamu</a>
            </nav>

            <div class="p-4 border-t border-gray-100 mt-auto">
                <button @click="logoutModalOpen = true" type="button" class="w-full flex items-center justify-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 active:bg-red-100 rounded-xl transition-all duration-200 font-semibold border border-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Keluar Sistem</span>
                </button>
            </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 backdrop-blur-sm" x-transition.opacity style="display: none;"></div>

        <main class="flex-1 overflow-y-auto w-full relative">
            <div class="bg-gray-50 min-h-screen p-4 sm:p-6 md:p-8">
                <div class="max-w-7xl mx-auto">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4 border-b border-gray-200 pb-4">
                        <div class="flex items-center gap-3 sm:gap-5">
                            <button @click="sidebarOpen = true" class="p-2 -ml-2 text-gray-600 hover:bg-gray-200 hover:text-red-700 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                                <svg class="w-7 h-7 sm:w-9 sm:h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>

                            <img src="{{ asset('images/Logo_DigiBAR.png') }}" alt="Logo DigiBAR" class="h-10 sm:h-24 w-auto drop-shadow-sm">
                            <div>
                                <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-red-900 tracking-tight">
                                    DigiBAR Group <span class="block sm:inline font-light text-gray-500 text-sm sm:text-base md:text-lg">| (Human Resource Management)</span>
                                </h1>
                                <p class="text-gray-500 text-xs sm:text-sm mt-0.5 sm:mt-1">Digital Attendance and Guest Book</p>
                            </div>
                        </div>
                    </div>

                    <header class="mb-8 flex justify-between items-end">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Data Absensi</h1>
                            <p class="text-gray-500 mt-1">Catat kehadiran masuk dan pulang karyawan.</p>
                        </div>
                        <div class="hidden sm:flex items-center gap-2 text-xs font-bold text-green-600 bg-green-50 px-3 py-1.5 rounded-full border border-green-200">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            Live Update Aktif
                        </div>
                    </header>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6">
                            <span class="block sm:inline text-sm">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100 mb-8 max-w-2xl">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Input Absensi Manual</h2>
                        <form action="/admin/absensi/store" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Pilih Nama Karyawan</label>
                                    <select name="employee_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 text-sm bg-white" required>
                                        <option value="">-- Pilih Karyawan --</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->nama_lengkap }} ({{ $emp->jabatan_posisi }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Jenis Absen</label>
                                    <div class="flex gap-4 p-2 bg-gray-50 rounded-lg border">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="type" value="Masuk" class="form-radio text-red-600" checked>
                                            <span class="ml-2 text-sm text-gray-700">Masuk</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="type" value="Pulang" class="form-radio text-red-600">
                                            <span class="ml-2 text-sm text-gray-700">Pulang</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-6">
                                <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Ambil Foto Bukti</label>
                                <input type="file" name="foto_bukti" accept="image/*" capture="user" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 bg-gray-50 text-sm" required>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-xl transition duration-300 shadow-lg shadow-red-100">
                                    Simpan Absensi
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
                        <form action="/admin/absensi" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                            <div class="w-full md:w-auto">
                                <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                            <div class="w-full md:w-auto">
                                <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full px-3 py-2 border rounded-lg text-sm">
                            </div>
                            <div class="flex gap-2 w-full md:w-auto">
                                <button type="submit" class="flex-1 md:flex-none bg-gray-800 hover:bg-black text-white px-6 py-2 rounded-lg text-sm transition font-semibold">Filter</button>
                                @if($startDate && $endDate)
                                <a href="/admin/absensi/download-pdf?start_date={{ $startDate }}&end_date={{ $endDate }}" class="flex-1 md:flex-none bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg text-sm transition flex items-center justify-center gap-2 font-semibold">
                                    PDF
                                </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" id="wadah-tabel-absensi">
                        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-gray-800">Riwayat Absensi</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[800px]">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-600 text-[10px] md:text-xs uppercase tracking-widest">
                                        <th class="p-4 border-b">No</th>
                                        <th class="p-4 border-b">Waktu</th>
                                        <th class="p-4 border-b">Nama Karyawan</th>
                                        <th class="p-4 border-b">Jenis Absen</th>
                                        <th class="p-4 border-b">Status</th>
                                        <th class="p-4 border-b text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm">
                                    @forelse($attendances as $index => $absen)
                                    @php
                                        $jamAbsen = $absen->created_at->format('H:i');
                                        $statusWaktu = '-';
                                        $badgeColor = 'bg-gray-100 text-gray-800 border-gray-200';

                                        if ($absen->type == 'Masuk') {
                                            if ($jamAbsen > '09:00') {
                                                $statusWaktu = 'Telat';
                                                $badgeColor = 'bg-red-100 text-red-800 border-red-200';
                                            } else {
                                                $statusWaktu = 'On Time';
                                                $badgeColor = 'bg-green-100 text-green-800 border-green-200';
                                            }
                                        } elseif ($absen->type == 'Pulang') {
                                            if ($jamAbsen < '17:00') {
                                                $statusWaktu = 'Pulang Awal';
                                                $badgeColor = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                            } else {
                                                $statusWaktu = 'On Time';
                                                $badgeColor = 'bg-green-100 text-green-800 border-green-200';
                                            }
                                        } else {
                                            $statusWaktu = 'Terverifikasi';
                                            $badgeColor = 'bg-blue-100 text-blue-800 border-blue-200';
                                        }

                                        $urlFoto = '';
                                        if ($absen->foto_bukti && $absen->foto_bukti !== 'Tanpa Foto' && $absen->foto_bukti !== 'default.png') {
                                            $urlFoto = asset('images/absensi/' . $absen->foto_bukti);
                                        }
                                    @endphp

                                    <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                                        <td class="p-4 text-xs text-gray-400">{{ $index + 1 }}</td>
                                        <td class="p-4 font-semibold text-gray-800 whitespace-nowrap">
                                            {{ $absen->created_at->format('d/m/y') }} <br>
                                            <span class="text-red-600">{{ $absen->created_at->format('H:i') }} WIB</span>
                                        </td>
                                        <td class="p-4">
                                            <div class="font-bold text-gray-900">{{ $absen->employee->nama_lengkap ?? 'Dihapus' }}</div>
                                            <div class="text-[10px] text-gray-500 uppercase">{{ $absen->employee->jabatan_posisi ?? '-' }}</div>
                                        </td>
                                        <td class="p-4">
                                            <span class="bg-gray-100 text-gray-700 text-[10px] font-black px-2 py-1 rounded border border-gray-200 uppercase tracking-wide">
                                                {{ $absen->type }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <span class="{{ $badgeColor }} text-[10px] font-black px-2 py-1 rounded border uppercase tracking-wide">
                                                {{ $statusWaktu }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-center flex items-center justify-center gap-3">
                                            <button 
                                                @click="absenData = {
                                                    nama: {{ json_encode($absen->employee->nama_lengkap ?? 'Karyawan Dihapus') }},
                                                    jabatan: {{ json_encode($absen->employee->jabatan_posisi ?? '-') }},
                                                    waktu: '{{ $absen->created_at->format('d M Y, H:i') }} WIB',
                                                    tipe: '{{ $absen->type }}',
                                                    status: '{{ $statusWaktu }}',
                                                    statusWarna: '{{ $badgeColor }}',
                                                    foto: '{{ $urlFoto }}',
                                                    keterangan: {{ json_encode($absen->keterangan ?? '') }}
                                                }; detailAbsenOpen = true"
                                                class="text-blue-600 font-bold hover:underline text-xs uppercase">
                                                Detail
                                            </button>

                                            <button type="button" 
                                                @click="deleteUrl = '/admin/absensi/{{ $absen->id }}'; deleteModalOpen = true"
                                                class="text-red-500 hover:underline font-bold text-xs uppercase transition">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-gray-400 italic text-sm">Belum ada data untuk periode ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br><br>

                </div>
            </div>
        </main>
    </div>

    <div x-show="detailAbsenOpen" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm" x-transition.opacity style="display: none;">
        <div @click.away="detailAbsenOpen = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all p-0">
            <div class="relative w-full h-64 bg-gray-200">
                <img x-show="absenData.foto !== ''" :src="absenData.foto" class="w-full h-full object-cover">
                <div x-show="absenData.foto === ''" class="w-full h-full flex items-center justify-center text-gray-400 text-sm italic">Tidak ada foto</div>
                <button @click="detailAbsenOpen = false" class="absolute top-4 right-4 bg-black/50 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition-colors">&times;</button>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-start mb-4 border-b pb-4">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900" x-text="absenData.nama"></h3>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mt-1" x-text="absenData.jabatan"></p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Waktu Rekam</span>
                        <span class="text-sm font-bold text-red-600" x-text="absenData.waktu"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Jenis Absen</span>
                            <span class="inline-block bg-gray-100 text-gray-700 text-xs font-black px-2 py-1 rounded border border-gray-200 uppercase tracking-wide" x-text="absenData.tipe"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status Kehadiran</span>
                            <span :class="absenData.statusWarna" class="inline-block text-xs font-black px-2 py-1 rounded border uppercase tracking-wide" x-text="absenData.status"></span>
                        </div>
                    </div>
                    <div class="mt-4" x-show="absenData.keterangan !== '' && absenData.keterangan !== null">
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Keterangan / Alasan</span>
                        <div class="bg-yellow-50 text-yellow-800 text-sm px-3 py-2 rounded-lg border border-yellow-200 font-medium" x-text="absenData.keterangan"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="logoutModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" x-transition.opacity style="display: none;">
        <div @click.away="logoutModalOpen = false" class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden transform transition-all p-6 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-red-100">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Yakin ingin keluar?</h3>
            <p class="text-gray-500 text-sm mb-6">Anda akan keluar dari sesi saat ini.</p>
            <div class="flex gap-3 justify-center">
                <button @click="logoutModalOpen = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-xl transition duration-200">Batal</button>
                <form action="/logout" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl transition duration-200 shadow-lg shadow-red-100">Ya, Keluar</button>
                </form>
            </div>
        </div>
    </div>

    <div x-show="deleteModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" x-transition.opacity style="display: none;">
        <div @click.away="deleteModalOpen = false" class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden transform transition-all p-6 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-red-100">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Data Ini?</h3>
            <p class="text-gray-500 text-sm mb-6">Data absensi beserta foto buktinya akan dihapus permanen dan tidak dapat dikembalikan.</p>
            <div class="flex gap-3 justify-center">
                <button @click="deleteModalOpen = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-xl transition duration-200">Batal</button>
                <form :action="deleteUrl" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl transition duration-200 shadow-lg shadow-red-100">Hapus Permanen</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        setInterval(function() {
            // Mengambil data tabel saja tanpa mereload seluruh halaman
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    // Mengambil isi dari tabel yang ada di file attendance_create.blade.php
                    let newTable = doc.querySelector('table').innerHTML; 
                    document.querySelector('table').innerHTML = newTable;
                })
                .catch(err => console.log('Update otomatis berjalan...'));
        }, 5000); // 5 detik
    </script>
</body>
</html>