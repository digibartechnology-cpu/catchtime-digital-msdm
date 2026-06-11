<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Peserta</title>
    <link rel="icon" href="{{ asset('images/Logo DigiBAR PNG.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border-t-4 border-red-600">
        
        <!-- Header Form -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Absensi Peserta</h1>
            <p class="text-red-600 font-bold mt-2">{{ $sesi->nama_kegiatan }}</p>
            <span class="inline-block bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full mt-2 border border-gray-200">
                Sesi Berakhir: {{ \Carbon\Carbon::parse($sesi->waktu_tutup)->format('H:i WIB') }}
            </span>
        </div>

        <!-- Notifikasi Sukses -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 font-bold shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm shadow-sm">
                <p class="font-bold mb-1">Peringatan:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Input -->
        <form action="/absen-lsp/store" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <!-- ID Sesi (Tersembunyi tapi penting untuk database) -->
            <input type="hidden" name="lsp_session_id" value="{{ $sesi->id }}">

            <div>
                <label class="block text-gray-700 font-bold mb-2 text-sm">Nama Lengkap</label>
                <input type="text" name="nama_peserta" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-gray-50" placeholder="Masukkan nama Anda" required>
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2 text-sm">Instansi Asal</label>
                <input type="text" name="instansi_asal" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-gray-50" placeholder="Contoh: PT. ABC atau Kampus XYZ" required>
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2 text-sm">Foto Bukti Kehadiran / Selfie</label>
                <!-- capture="camera" akan otomatis membuka kamera di HP -->
                <input type="file" name="foto_bukti" accept="image/*" capture="camera" class="w-full px-4 py-2 border rounded-xl file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer" required>
            </div>

            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all transform hover:-translate-y-1 mt-6">
                Kirim Data Kehadiran
            </button>
            
            <a href="/" class="block text-center text-gray-400 hover:text-gray-600 mt-4 text-sm transition-colors">
                &larr; Kembali ke Halaman Utama
            </a>
        </form>
        
    </div>
</body>
</html>