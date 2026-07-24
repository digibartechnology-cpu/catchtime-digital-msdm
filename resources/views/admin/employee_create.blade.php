<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Karyawan</title>
    <link rel="icon" href="{{ asset('images/logo_icon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans" x-data="{ sidebarOpen: false, detailOpen: false, selectedEmp: {}, logoutModalOpen: false, deleteModalOpen: false, deleteUrl: '' }">

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
                <a href="/admin/karyawan" class="block px-4 py-3 bg-red-50 text-red-700 rounded-lg font-semibold border-l-4 border-red-600">
                    Data Karyawan
                </a>
                <a href="/admin/absensi" class="block px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-colors">
                    Data Absensi
                </a>
                <a href="/admin/buku-tamu" class="block px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-colors">
                    Buku Tamu
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

                    <header class="mb-8">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Kelola Data Karyawan</h1>
                        <p class="text-gray-500 mt-1">Tambah karyawan baru dan lihat daftar seluruh karyawan.</p>
                    </header>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm font-bold">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Tambah Karyawan Baru</h2>
                        
                        <!-- CEK LIMIT KARYAWAN -->
                        @php
                            $isLimitReached = count($employees) >= 15;
                        @endphp

                        @if($isLimitReached)
                            <!-- Alert Batas Maksimal -->
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-start gap-3">
                                <svg class="w-6 h-6 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <div>
                                    <p class="font-bold">Batas Maksimal Tercapai!</p>
                                    <p class="text-sm mt-1">Anda sudah memiliki 15 karyawan. Anda tidak dapat menambahkan data baru lagi kecuali menghapus data yang sudah ada terlebih dahulu.</p>
                                </div>
                            </div>
                        @endif

                        <form action="/admin/karyawan" method="POST" @if($isLimitReached) onsubmit="event.preventDefault(); alert('Akses Ditolak: Batas maksimal 15 karyawan telah tercapai!');" @endif>
                            @csrf
                            <!-- Fieldset akan otomatis mengunci/mendisable seluruh input jika limit tercapai -->
                            <fieldset @if($isLimitReached) disabled class="opacity-50 cursor-not-allowed" @endif>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 text-sm">
                                    <div>
                                        <label class="block text-gray-700 font-bold mb-2">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 disabled:bg-gray-100" required>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-bold mb-2">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" class="w-full px-3 py-2 border rounded-lg bg-white disabled:bg-gray-100" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-bold mb-2">No. WhatsApp</label>
                                        <input type="text" name="no_whatsapp" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 disabled:bg-gray-100" required>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-bold mb-2">Jabatan / Posisi</label>
                                        <input type="text" name="jabatan_posisi" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 disabled:bg-gray-100" required>
                                    </div>
                                </div>
                                <div class="mb-6 text-sm">
                                    <label class="block text-gray-700 font-bold mb-2">Alamat Tempat Tinggal</label>
                                    <textarea name="alamat_tempat_tinggal" rows="2" class="w-full px-3 py-2 border rounded-lg disabled:bg-gray-100" required></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="w-full md:w-auto text-white font-bold py-3 px-8 rounded-xl transition shadow-lg 
                                        @if($isLimitReached) bg-gray-400 hover:bg-gray-400 cursor-not-allowed shadow-none 
                                        @else bg-red-600 hover:bg-red-700 shadow-red-100 @endif">
                                        Simpan Data
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-gray-50">
                            <h2 class="text-lg font-bold text-gray-800">Daftar Karyawan (<span x-text="{{ count($employees) }}"></span>/15)</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[800px]">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-widest">
                                        <th class="p-4 border-b">No</th>
                                        <th class="p-4 border-b">Nama Lengkap</th>
                                        <th class="p-4 border-b">L/P</th>
                                        <th class="p-4 border-b">WhatsApp</th>
                                        <th class="p-4 border-b">Jabatan</th>
                                        <th class="p-4 border-b text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm">
                                    @forelse($employees as $index => $emp)
                                    <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                                        <td class="p-4 text-gray-400">{{ $index + 1 }}</td>
                                        <td class="p-4 font-bold text-gray-900">{{ $emp->nama_lengkap }}</td>
                                        <td class="p-4">{{ $emp->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</td>
                                        <td class="p-4">{{ $emp->no_whatsapp }}</td>
                                        <td class="p-4">
                                            <span class="bg-red-50 text-red-700 text-[10px] font-black px-2 py-1 rounded border border-red-100 uppercase">
                                                {{ $emp->jabatan_posisi }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-center flex items-center justify-center gap-4">
                                            <button 
                                                @click="selectedEmp = {{ json_encode($emp) }}; detailOpen = true"
                                                class="text-blue-600 font-bold hover:underline">
                                                Detail
                                            </button>

                                            <button type="button" 
                                                @click="deleteUrl = '/admin/karyawan/{{ $emp->id }}'; deleteModalOpen = true"
                                                class="text-red-500 font-bold hover:underline">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="p-8 text-center text-gray-400 italic">Belum ada data karyawan.</td></tr>
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

    <div x-show="detailOpen" 
         class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" 
         x-transition.opacity 
         style="display: none;">
        
        <div @click.away="detailOpen = false" 
             class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all p-6">
            
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-lg font-bold text-gray-800">Biodata Lengkap Karyawan</h3>
                <button @click="detailOpen = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>

            <div class="space-y-4 text-sm">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</label>
                    <p class="text-base font-bold text-gray-800 mt-0.5" x-text="selectedEmp.nama_lengkap"></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis Kelamin</label>
                        <p class="text-gray-700 font-semibold mt-0.5" x-text="selectedEmp.jenis_kelamin"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Jabatan</label>
                        <p class="text-red-700 font-bold mt-0.5" x-text="selectedEmp.jabatan_posisi"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">No. WhatsApp</label>
                    <p class="text-gray-700 font-semibold mt-0.5" x-text="selectedEmp.no_whatsapp"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat Tempat Tinggal</label>
                    <p class="text-gray-600 mt-0.5 leading-relaxed bg-gray-50 p-2.5 rounded-lg border border-gray-100" x-text="selectedEmp.alamat_tempat_tinggal"></p>
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t pt-3">
                <button @click="detailOpen = false" 
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded-xl transition duration-200">
                    Kembali
                </button>
            </div>
        </div>
    </div>

    <div x-show="logoutModalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" 
         x-transition.opacity 
         style="display: none;">
        
        <div @click.away="logoutModalOpen = false" 
             class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden transform transition-all p-6 text-center">
            
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-red-100">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2">Yakin ingin keluar?</h3>
            <p class="text-gray-500 text-sm mb-6">Anda akan keluar dari sesi Portal saat ini.</p>
            
            <div class="flex gap-3 justify-center">
                <button @click="logoutModalOpen = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-xl transition duration-200">
                    Batal
                </button>
                <form action="/logout" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl transition duration-200 shadow-lg shadow-red-100">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div x-show="deleteModalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" 
         x-transition.opacity 
         style="display: none;">
         
        <div @click.away="deleteModalOpen = false" 
             class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden transform transition-all p-6 text-center">
            
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-red-100">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Karyawan Ini?</h3>
            <p class="text-gray-500 text-sm mb-6">Data karyawan ini akan dihapus permanen dan tidak dapat dikembalikan. Data absensi yang terkait mungkin juga akan terpengaruh.</p>
            
            <div class="flex gap-3 justify-center">
                <button @click="deleteModalOpen = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-xl transition duration-200">
                    Batal
                </button>
                <form :action="deleteUrl" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl transition duration-200 shadow-lg shadow-red-100">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>