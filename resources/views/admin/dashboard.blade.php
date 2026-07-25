<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard DigiBAR Digital MSDM</title>
    <link rel="icon" href="{{ asset('images/Logo_icon.png') }}" type="image/png">
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
                <a href="/admin/dashboard" class="block px-4 py-3 bg-red-50 text-red-700 rounded-lg font-semibold border-l-4 border-red-600">
                    Dashboard Overview
                </a>
                <a href="/admin/karyawan" class="block px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-600 rounded-lg transition-colors">
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

                    <header class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard Overview</h1>
                            <p class="text-gray-500 mt-1">Ringkasan data hari ini</p>
                        </div>
                        <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200 self-start md:self-auto">
                            <span class="text-sm text-gray-600">Halo, <strong class="text-red-600">Admin</strong></span>
                        </div>
                    </header>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-8">
                        <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-red-500 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-gray-400 text-xs font-bold uppercase tracking-wider">Total Karyawan</h3>
                                    <p class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">{{ $totalKaryawan }}</p>
                                </div>
                                <div class="bg-red-50 p-3 rounded-lg text-red-500">
                                    <svg class="w-7 h-7 md:w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-blue-500 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-gray-400 text-xs font-bold uppercase tracking-wider">Absen Masuk & Pulang</h3>
                                    <p class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">{{ $absenHariIni }}</p>
                                </div>
                                <div class="bg-blue-50 p-3 rounded-lg text-blue-500">
                                    <svg class="w-7 h-7 md:w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-purple-500 hover:shadow-md transition-shadow sm:col-span-2 md:col-span-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-gray-400 text-xs font-bold uppercase tracking-wider">Tamu Hari Ini</h3>
                                    <p class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">{{ $tamuHariIni }}</p>
                                </div>
                                <div class="bg-purple-50 p-3 rounded-lg text-purple-500">
                                    <svg class="w-7 h-7 md:w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-100">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Selamat Datang di DigiBAR Digital MSDM</h2>
                        <p class="text-gray-600 text-sm md:text-base leading-relaxed">
                            Kelola data karyawan, verifikasi absensi, dan pantau tamu harian dengan mudah dalam satu sistem terintegrasi.
                        </p>
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