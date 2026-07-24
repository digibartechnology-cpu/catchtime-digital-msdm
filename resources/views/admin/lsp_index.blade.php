<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu</title>
    <link rel="icon" href="{{ asset('images/logo_icon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans" x-data="{ sidebarOpen: false, logoutModalOpen: false }">

    <div class="flex h-screen overflow-hidden">
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-2xl transform transition-transform duration-300 ease-in-out border-r border-gray-200 flex flex-col h-full">
            
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-red-700">Menu</h2>
                <button @click="sidebarOpen = false" class="text-gray-400 hover:text-red-600 text-3xl font-bold transition-colors focus:outline-none">&times;</button>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="/admin/dashboard" class="block px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-colors">
                    Dashboard Overview
                </a>
                <a href="/admin/karyawan" class="block px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-colors">
                    Data Karyawan
                </a>
                <a href="/admin/absensi" class="block px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-colors">
                    Data Absensi
                </a>
                <a href="/admin/absensi" class="block px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-colors">
                    Buku Tamu
                </a>
                <a href="/admin/buku-tamu" class="block px-4 py-3 bg-red-50 text-red-700 rounded-lg font-semibold border-l-4 border-red-600">
                    Input Kegiatan
                </a>
            </nav>

            <div class="p-4 border-t border-gray-100 mt-auto">
                <button @click="logoutModalOpen = true" type="button" class="w-full flex items-center justify-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 active:bg-red-100 rounded-xl transition-all duration-200 font-semibold border border-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Keluar Sistem</span>
                </button>
            </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 backdrop-blur-sm" x-transition.opacity style="display: none;"></div>

        <main class="flex-1 overflow-y-auto w-full">
            <div class="bg-gray-50 min-h-screen p-4 sm:p-6 md:p-8">
                <div class="max-w-7xl mx-auto">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4 border-b border-gray-200 pb-4">
                        <div class="flex items-center gap-3 sm:gap-5">
                            <button @click="sidebarOpen = true" class="p-2 -ml-2 text-gray-600 hover:bg-gray-200 hover:text-red-700 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                                <svg class="w-7 h-7 sm:w-9 sm:h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
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


        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <header class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Kelola Jadwal Pelatihan & Pengujian</h1>
                <p class="text-gray-500 mt-1">Buat jadwal buka dan tutup absensi otomatis untuk peserta.</p>
            </header>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Buat Jadwal Baru</h2>
                <form action="/admin/lsp/store" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold mb-2">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" class="w-full px-3 py-2 border rounded-lg" placeholder="Cth: Pengujian / Pelatihan Receptionist" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Waktu Buka Absen</label>
                            <input type="datetime-local" name="waktu_buka" class="w-full px-3 py-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Waktu Tutup Absen</label>
                            <input type="datetime-local" name="waktu_tutup" class="w-full px-3 py-2 border rounded-lg" required>
                        </div>
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-xl">Simpan Jadwal</button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Daftar Jadwal & Peserta</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-widest">
                                <th class="p-4 border-b">Nama Kegiatan</th>
                                <th class="p-4 border-b">Jadwal Buka - Tutup</th>
                                <th class="p-4 border-b text-center">Jumlah Hadir</th>
                                <th class="p-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($sessions as $sesi)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-4 font-bold">{{ $sesi->nama_kegiatan }}</td>
                                <td class="p-4 text-xs">
                                    <span class="text-green-600 font-bold">Buka:</span> {{ \Carbon\Carbon::parse($sesi->waktu_buka)->format('d/m/Y H:i') }} <br>
                                    <span class="text-red-600 font-bold">Tutup:</span> {{ \Carbon\Carbon::parse($sesi->waktu_tutup)->format('d/m/Y H:i') }}
                                </td>
                                <td class="p-4 text-center">
                                    <span id="count-{{ $sesi->id }}" class="bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded-full transition-all duration-500">{{ $sesi->attendances_count }} Peserta</span>
                                </td>
                                <td class="p-4 text-center flex items-center justify-center gap-3">
                                    
                                    <button @click="bukaModalPeserta({{ $sesi->id }}, '{{ $sesi->nama_kegiatan }}')" class="text-blue-600 font-bold hover:underline">Lihat Data</button>
                                    
                                    <a href="/admin/lsp/{{ $sesi->id }}/download-pdf" class="text-green-600 font-bold hover:underline" target="_blank">Unduh PDF</a>
                                    
                                    <form action="/admin/lsp/{{ $sesi->id }}" method="POST" onsubmit="return confirm('Yakin hapus jadwal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" style="display: none;">
        <div @click.away="modalOpen = false" class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden p-6 max-h-[80vh] flex flex-col">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-lg font-bold text-gray-800" x-text="'Daftar Kehadiran: ' + kegiatanNama"></h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            
            <div class="overflow-y-auto flex-1">
                <table class="w-full text-left text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 border-b">Nama Peserta</th>
                            <th class="p-3 border-b">Instansi</th>
                            <th class="p-3 border-b text-center">Foto Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr x-show="isLoading">
                            <td colspan="3" class="p-8 text-center text-blue-600 font-bold animate-pulse">
                                Mengambil data terbaru...
                            </td>
                        </tr>

                        <template x-if="!isLoading">
                            <template x-for="peserta in pesertaList" :key="peserta.id">
                                <tr class="border-b">
                                    <td class="p-3 font-bold text-gray-800" x-text="peserta.nama_peserta"></td>
                                    <td class="p-3 text-gray-600" x-text="peserta.instansi_asal"></td>
                                    <td class="p-3 text-center">
                                        <a :href="'/images/absensi_lsp/' + peserta.foto_bukti" target="_blank" class="text-blue-500 hover:text-blue-700 underline text-xs font-bold">Lihat Foto</a>
                                    </td>
                                </tr>
                            </template>
                        </template>
                        
                        <tr x-show="!isLoading && pesertaList.length === 0">
                            <td colspan="3" class="p-4 text-center text-gray-400 italic">Belum ada peserta yang absen.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('lspData', () => ({
                sidebarOpen: false,
                modalOpen: false,
                pesertaList: [],
                kegiatanNama: '',
                isLoading: false,

                bukaModalPeserta(id, nama) {
                    this.kegiatanNama = nama;
                    this.pesertaList = []; 
                    this.isLoading = true; 
                    this.modalOpen = true; 
                    
                    fetch('/admin/lsp/' + id + '/attendances')
                        .then(response => response.json())
                        .then(data => {
                            this.pesertaList = data; 
                            this.isLoading = false; 
                        })
                        .catch(error => {
                            console.error('Error fetching data:', error);
                            this.isLoading = false;
                        });
                }
            }))
        })
    </script>
    <script>
        // Sistem akan mengecek setiap 5 detik (5000 milidetik)
        setInterval(function() {
            fetch('/admin/lsp/live-counts')
                .then(response => response.json())
                .then(data => {
                    // Masukkan angka terbaru dari database ke layar Anda
                    for (const [id, jumlah] of Object.entries(data)) {
                        let badge = document.getElementById('count-' + id);
                        
                        // Jika angkanya berubah, update layarnya
                        if (badge && badge.innerText !== jumlah + ' Peserta') {
                            badge.innerText = jumlah + ' Peserta';
                            
                            // Beri efek kedip kuning sebentar agar admin sadar ada yang baru absen
                            badge.classList.remove('bg-blue-100', 'text-blue-800');
                            badge.classList.add('bg-yellow-300', 'text-yellow-900');
                            
                            setTimeout(() => {
                                badge.classList.remove('bg-yellow-300', 'text-yellow-900');
                                badge.classList.add('bg-blue-100', 'text-blue-800');
                            }, 1500);
                        }
                    }
                })
                .catch(error => console.log('Sistem sedang menyinkronkan data...'));
        }, 5000);
    </script>
</body>
</html>