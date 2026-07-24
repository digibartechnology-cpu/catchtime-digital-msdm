<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu</title>
    <link rel="icon" href="{{ asset('images/logo_icon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4"
      x-data="{ showSuccess: {{ session('success') ? 'true' : 'false' }}, showError: {{ $errors->any() ? 'true' : 'false' }} }">

    <div x-show="showSuccess" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" style="display: none;">
        <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Berhasil!</h3>
            <p class="text-gray-500 text-sm mb-6">{{ session('success') }}</p>
            <button @click="showSuccess = false" class="w-full bg-green-600 text-white font-bold py-2.5 rounded-xl">Tutup</button>
        </div>
    </div>

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-blue-700 px-6 py-8 text-center">
            <h1 class="text-2xl font-bold text-white tracking-wide">BUKU TAMU PT Loka Karya Nusantara</h1>
        </div>
        <div class="p-6">
            <form action="/buku-tamu/store" method="POST">
                @csrf
                <div class="mb-4"><label class="block text-sm font-semibold mb-2">Nama Lengkap</label><input type="text" name="nama_tamu" class="w-full px-4 py-3 bg-gray-50 border rounded-lg text-sm" required></div>
                <div class="mb-4"><label class="block text-sm font-semibold mb-2">Instansi</label><input type="text" name="instansi_asal" class="w-full px-4 py-3 bg-gray-50 border rounded-lg text-sm" required></div>
                <div class="mb-4"><label class="block text-sm font-semibold mb-2">No. WA</label><input type="tel" name="no_hp" class="w-full px-4 py-3 bg-gray-50 border rounded-lg text-sm" required></div>
                <div class="mb-8"><label class="block text-sm font-semibold mb-2">Keperluan</label><textarea name="tujuan_keperluan" rows="3" class="w-full px-4 py-3 bg-gray-50 border rounded-lg text-sm" required></textarea></div>
                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg">Kirim Data Tamu</button>
                <div class="text-center pt-4"><a href="/" class="text-sm text-gray-500">&larr; Kembali ke Menu Utama</a></div>
            </form>
        </div>
    </div>
</body>
</html>