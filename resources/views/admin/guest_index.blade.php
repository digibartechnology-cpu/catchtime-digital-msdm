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
                <a href="/admin/buku-tamu" class="block px-4 py-3 bg-red-50 text-red-700 rounded-lg font-semibold border-l-4 border-red-600">
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
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Daftar Buku Tamu</h1>
                        <p class="text-gray-500 mt-1 text-sm">Monitor pengunjung yang datang ke kantor.</p>
                    </header>

                    <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
                        <form action="/admin/buku-tamu" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                            <div class="w-full md:w-auto">
                                <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                            </div>
                            <div class="w-full md:w-auto">
                                <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-red-500">
                            </div>
                            <div class="flex gap-2 w-full md:w-auto">
                                <button type="submit" class="flex-1 md:flex-none bg-gray-800 hover:bg-black text-white px-6 py-2 rounded-lg text-sm font-semibold transition">Filter</button>
                                @if($startDate && $endDate)
                                    <a href="/admin/buku-tamu/download-pdf?start_date={{ $startDate }}&end_date={{ $endDate }}" class="flex-1 md:flex-none bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg text-sm font-semibold text-center transition">PDF</a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse min-w-[800px]">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-600 text-[10px] uppercase tracking-widest">
                                        <th class="p-4 border-b">No</th>
                                        <th class="p-4 border-b">Waktu Kedatangan</th>
                                        <th class="p-4 border-b">Nama Tamu</th>
                                        <th class="p-4 border-b">Instansi</th>
                                        <th class="p-4 border-b">Keperluan</th>
                                        <th class="p-4 border-b">No. HP</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm">
                                    @forelse($guests as $index => $guest)
                                    <tr class="hover:bg-gray-50 border-b border-gray-50 transition-colors">
                                        <td class="p-4 text-gray-400">{{ $index + 1 }}</td>
                                        <td class="p-4 font-medium text-red-600 whitespace-nowrap">
                                            {{ $guest->created_at->format('d/m/y, H:i') }} WIB
                                        </td>
                                        <td class="p-4 font-bold text-gray-900">{{ $guest->nama_tamu }}</td>
                                        <td class="p-4 text-gray-600">{{ $guest->instansi_asal ?? '-' }}</td>
                                        <td class="p-4 text-gray-600">{{ $guest->tujuan_keperluan }}</td>
                                        <td class="p-4 whitespace-nowrap">{{ $guest->no_hp }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-gray-400 italic text-sm">
                                            Belum ada data tamu untuk periode ini.
                                        </td>
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
</body>
</html>