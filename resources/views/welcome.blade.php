<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital MSDM</title>
    <link rel="icon" href="{{ asset('images/logo-digibar.png') }}" type="image/png">
    <!-- Menggunakan Tailwind CSS untuk styling cepat -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center font-sans">
    
    <!-- MODAL IKLAN -->
    <div id="customIklanModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background-color: rgba(0,0,0,0.7); align-items: center; justify-content: center;">
        <div style="position: relative; max-width: 450px; width: 100%; margin: 0 20px; background: #222; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.5); text-align: center;">
            <div style="width: 100%; display: block; line-height: 0;">
                <img src="{{ asset('images/zoom.jpg') }}" alt="Iklan DigiBAR" style="width: 100%; height: auto; max-height: 75vh; object-fit: contain;">
            </div>
            <div style="padding: 12px; background-color: white">
                <button onclick="tutupIklanManual()" style="background-color: blue; color: white; border: none; padding: 8px 30px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.2s;">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="bg-white p-10 rounded-xl shadow-lg max-w-2xl w-full text-center border-t-4 border-blue-500">
        
        <!-- Judul Halaman -->
        <header class="text-center">
            <h1 id="greeting" class="text-3xl font-bold text-gray-800 mb-2 leading-tight">
                <!-- Teks akan diisi oleh JavaScript -->
            </h1>
            <p id="datetime" class="text-gray-500 mb-8 text-sm font-medium">
                <!-- Waktu akan diisi oleh JavaScript -->
            </p>
        </header>
        
        <!-- Sub-judul -->
        <p class="text-gray-600 mb-8 text-lg">
            Silakan pilih menu yang ada di bawah ini:
        </p>

        <!-- Pilihan Menu (Grid 2 Kolom) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tombol Absensi -->
            <a href="/absen" class="group block p-6 bg-blue-50 hover:bg-blue-500 rounded-lg border border-blue-100 transition duration-300 shadow-sm">
                <div class="text-4xl mb-3">👨‍💼</div>
                <h2 class="text-xl font-semibold text-blue-800 group-hover:text-white mb-2">Absensi Karyawan</h2>
                <p class="text-sm text-blue-600 group-hover:text-blue-100">
                    Masuk ke portal absensi harian karyawan.
                </p>
            </a>

            <!-- Tombol Buku Tamu -->
            <a href="/buku-tamu-form" class="group block p-6 bg-green-50 hover:bg-green-500 rounded-lg border border-green-100 transition duration-300 shadow-sm">
                <div class="text-4xl mb-3">📖</div>
                <h2 class="text-xl font-semibold text-green-800 group-hover:text-white mb-2">Buku Tamu</h2>
                <p class="text-sm text-green-600 group-hover:text-green-100">
                    Isi data kunjungan bagi tamu yang datang.
                </p>
            </a>
        </div>
        <!-- AKHIR GRID 2 KOLOM -->

        <!-- TOMBOL OTOMATIS MUNCUL UNTUK LSP (DI LUAR GRID AGAR FULL LEBAR) -->
        @if(isset($sesiLspAktif))
        <div class="mt-6 border-t-2 border-dashed border-gray-200 pt-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest text-center mb-3">Kegiatan Sedang Berlangsung</h3>
            <a href="/absen-lsp/{{ $sesiLspAktif->id }}" class="block w-full bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-white text-center py-4 rounded-xl shadow-xl transition-all transform hover:scale-105 border border-red-400 relative overflow-hidden">
                
                <!-- Efek Kedip/Pulse -->
                <span class="absolute top-0 right-0 flex h-3 w-3 mt-2 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-300 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                </span>

                <p class="font-bold text-lg flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    ABSENSI: {{ strtoupper($sesiLspAktif->nama_kegiatan) }}
                </p>
                <p class="text-xs font-medium text-red-200 mt-1">
                    Batas Waktu: {{ \Carbon\Carbon::parse($sesiLspAktif->waktu_tutup)->format('H:i WIB') }}
                </p>
            </a>
        </div>
        @endif

        <div class="mt-8 text-sm text-gray-400">
            &copy; 2026 DigiBAR Group
        </div>
    </div>

    <!-- SCRIPT WAKTU & GREETING -->
    <script>
        function updateGreetingAndTime() {
            const now = new Date();

            // Greeting
            const hour = now.getHours();
            let greeting = "";
            if (hour >= 3 && hour < 10) greeting = "Selamat Pagi";
            else if (hour >= 10 && hour < 15) greeting = "Selamat Siang";
            else if (hour >= 15 && hour < 18) greeting = "Selamat Sore";
            else greeting = "Selamat Malam";

            document.getElementById('greeting').innerHTML = `
                ${greeting}<br> 
                <span class="text-xl font-medium text-gray-600 mt-2 block">
                    Selamat Datang di Kantor <span class="text-blue-600 font-bold">LSP Citra Insan x PT Ananta Jaya Utama Abadi</span>
                </span>
            `;

            // Date & Time
            const options = { day: '2-digit', month: 'long', year: 'numeric' };
            const dateStr = now.toLocaleDateString('id-ID', options);
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            
            document.getElementById('datetime').textContent = `Hari ini: ${dateStr} | Pukul ${timeStr} WIB`;
        }

        setInterval(updateGreetingAndTime, 1000);
        updateGreetingAndTime(); 
    </script>

    <!-- SCRIPT MODAL IKLAN -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modalBox = document.getElementById("customIklanModal");
            if (modalBox) {
                modalBox.style.display = "flex"; // Memperbaiki penulisan js yang salah sebelumnya
            }
        });

        function tutupIklanManual() {
            const modalBox = document.getElementById("customIklanModal");
            if (modalBox) {
                modalBox.style.display = "none";
            }
        }
    </script>
</body>
</html>