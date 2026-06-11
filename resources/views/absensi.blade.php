<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan</title>
    <link rel="icon" href="{{ asset('images/logo-digibar.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4" 
      x-data="{ showSuccess: {{ session('success') ? 'true' : 'false' }}, showError: {{ $errors->any() ? 'true' : 'false' }} }">

    <div x-show="showSuccess" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" style="display: none;">
        <div @click.away="showSuccess = false" class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Berhasil!</h3>
            <p class="text-gray-500 text-sm mb-6">{{ session('success') }}</p>
            <button @click="showSuccess = false" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl transition">Tutup</button>
        </div>
    </div>

    <div x-show="showError" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" style="display: none;">
        <div @click.away="showError = false" class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Terjadi Kesalahan!</h3>
            <p class="text-gray-500 text-sm mb-6">@foreach ($errors->all() as $error) {{ $error }} @endforeach</p>
            <button @click="showError = false" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl transition">Tutup</button>
        </div>
    </div>

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-blue-700 px-6 py-8 text-center">
            <h1 class="text-2xl font-bold text-white tracking-wide">LSP CITRA INSAN X PT ANANTA JAYA UTAMA ABADI</h1>
            <p class="text-blue-100 text-sm mt-1 font-medium">DigiBAR DIGITAL MSDM</p>
        </div>
        <div class="p-6 sm:p-8">
            <form action="/absen/store" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Karyawan</label>
                    <select name="employee_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm" required>
                        <option value="">-- Pilih Nama Anda --</option>
                        @foreach($employees as $emp) <option value="{{ $emp->id }}">{{ $emp->nama_lengkap }}</option> @endforeach
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Tipe Kehadiran</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label><input type="radio" name="type" value="Masuk" class="peer sr-only" onclick="aturFormIjin()" checked><div class="px-4 py-3 text-center border border-green-300 rounded-lg peer-checked:bg-green-50 peer-checked:border-green-600 text-sm font-semibold text-green-600">Masuk</div></label>
                        <label><input type="radio" name="type" value="Leave Office" class="peer sr-only" onclick="aturFormIjin()"><div class="px-4 py-3 text-center border border-yellow-300 rounded-lg peer-checked:bg-yellow-50 peer-checked:border-yellow-600 text-sm font-semibold text-yellow-600">Ijin Keluar</div></label>
                        <label class="col-span-2"><input type="radio" name="type" value="Pulang" class="peer sr-only" onclick="aturFormIjin()"><div class="px-4 py-3 text-center border border-red-300 rounded-lg peer-checked:bg-red-50 peer-checked:border-red-600 text-sm font-semibold text-red-600">Pulang</div></label>
                    </div>
                </div>
                <div id="container_alasan" class="mb-5" style="display: none;">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Alasan Keluar</label>
                    <select name="alasan_ijin" id="pilihan_alasan" onchange="aturAlasanLainnya()" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm">
                        <option value="">-- Pilih Alasan --</option>
                        <option value="Pergi ke Bank">Pergi ke Bank</option>
                        <option value="Antarkan Dokumen">Antarkan Dokumen</option>
                        <option value="Lainnya">Lainnya / Dll...</option>
                    </select>
                </div>
                <div id="container_alasan_lainnya" class="mb-5" style="display: none;">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Ketik Alasan Spesifik</label>
                    <input type="text" name="alasan_lainnya" id="input_alasan_lainnya" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm" placeholder="Ketik alasan spesifik">
                </div>
                <div id="container_foto" class="mb-8">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Bukti Kehadiran (Foto)</label>
                    <input type="file" name="foto_bukti" accept="image/*" capture="user" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm" required id="camera-input">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">Kirim Absensi</button>
                <div class="text-center pt-6"><a href="/" class="text-sm text-gray-500 hover:text-blue-600">&larr; Kembali ke Menu Utama</a></div>
            </form>
        </div>
    </div>
    <script>
        function aturFormIjin() {
            let tipe = document.querySelector('input[name="type"]:checked').value;
            let containerFoto = document.getElementById('container_foto');
            let inputFoto = document.getElementById('camera-input');
            let containerAlasan = document.getElementById('container_alasan');
            let pilihanAlasan = document.getElementById('pilihan_alasan');

            if (tipe === 'Leave Office') {
                containerFoto.style.display = 'none';
                inputFoto.removeAttribute('required'); // Menghapus kewajiban foto agar form tidak stuck
                containerAlasan.style.display = 'block';
                pilihanAlasan.setAttribute('required', 'required'); // Mewajibkan pengisian alasan
            } else {
                containerFoto.style.display = 'block';
                inputFoto.setAttribute('required', 'required'); // Mewajibkan foto kembali untuk Masuk/Pulang
                containerAlasan.style.display = 'none';
                pilihanAlasan.removeAttribute('required');
            }
            aturAlasanLainnya();
        }

        function aturAlasanLainnya() {
            let pilihan = document.getElementById('pilihan_alasan').value;
            let containerLainnya = document.getElementById('container_alasan_lainnya');
            let inputLainnya = document.getElementById('input_alasan_lainnya');
            let tipe = document.querySelector('input[name="type"]:checked').value;
            
            if (tipe === 'Leave Office' && pilihan === 'Lainnya') {
                containerLainnya.style.display = 'block';
                inputLainnya.setAttribute('required', 'required');
            } else {
                containerLainnya.style.display = 'none';
                inputLainnya.removeAttribute('required');
            }
        }

        window.onload = function() {
            aturFormIjin();
        };
    </script>
</body>
</html>