<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan | Catchtime</title>
    <link rel="icon" href="{{ asset('images/Logo_icon.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        video { transform: scaleX(-1); } 
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4" x-data="absensiCatchtime()" x-init="initApp()">

    <!-- Modal Success -->
    <div x-show="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="closeSuccessModal" class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center transform transition-all">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Berhasil!</h3>
            <p class="text-gray-500 text-sm mb-6" x-text="successMessage"></p>
            <button @click="closeSuccessModal" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl transition shadow-lg shadow-green-200">Tutup</button>
        </div>
    </div>

    <!-- Modal Error -->
    <div x-show="showErrorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="showErrorModal = false" class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center transform transition-all">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Terjadi Kesalahan!</h3>
            <p class="text-gray-500 text-sm mb-6" x-text="errorMessage"></p>
            <button @click="showErrorModal = false" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl transition shadow-lg shadow-red-200">Tutup</button>
        </div>
    </div>

    <!-- Kontainer Utama -->
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden relative z-10">
        <div class="bg-blue-700 px-6 py-8 text-center relative overflow-hidden">
            <h1 class="text-2xl font-bold text-white tracking-wide relative z-10">PT Loka Karya Nusantara</h1>
            <p class="text-blue-100 text-sm mt-1 font-medium relative z-10">Sistem Kehadiran Catchtime (GPS & Auto-Snap)</p>
        </div>
        
        <div class="p-6 sm:p-8">
            <form id="formAbsensi" action="/absen/store" method="POST" @submit.prevent="submitForm">
                @csrf
                
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Karyawan</label>
                    <select name="employee_id" x-model="employeeId" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Nama Anda --</option>
                        @foreach($employees as $emp) <option value="{{ $emp->id }}">{{ $emp->nama_lengkap }}</option> @endforeach
                    </select>
                </div>
                
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Tipe Kehadiran</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label>
                            <input type="radio" name="type" value="Masuk" x-model="tipeAbsen" class="peer sr-only">
                            <div class="px-4 py-3 text-center border-2 border-gray-200 rounded-lg peer-checked:bg-green-50 peer-checked:border-green-500 text-sm font-semibold text-gray-500 peer-checked:text-green-600 cursor-pointer transition-all">Masuk</div>
                        </label>
                        <label>
                            <input type="radio" name="type" value="Pulang" x-model="tipeAbsen" class="peer sr-only">
                            <div class="px-4 py-3 text-center border-2 border-gray-200 rounded-lg peer-checked:bg-red-50 peer-checked:border-red-500 text-sm font-semibold text-gray-500 peer-checked:text-red-600 cursor-pointer transition-all">Pulang</div>
                        </label>
                        <label class="col-span-2">
                            <input type="radio" name="type" value="Leave Office" x-model="tipeAbsen" class="peer sr-only">
                            <div class="px-4 py-3 text-center border-2 border-gray-200 rounded-lg peer-checked:bg-yellow-50 peer-checked:border-yellow-500 text-sm font-semibold text-gray-500 peer-checked:text-yellow-600 cursor-pointer transition-all">Ijin Keluar</div>
                        </label>
                    </div>
                </div>

                <!-- Bagian Ijin Keluar (Hanya muncul jika tipeAbsen == 'Leave Office') -->
                <div x-show="tipeAbsen === 'Leave Office'" x-collapse>
                    <div class="mb-5">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Alasan Keluar</label>
                        <select name="alasan_ijin" x-model="alasanIjin" :required="tipeAbsen === 'Leave Office'" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm">
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Makan Siang">Makan Siang</option>
                            <option value="Pergi ke Bank">Pergi ke Bank</option>
                            <option value="Antarkan Dokumen">Antarkan Dokumen</option>
                            <option value="Lainnya">Lainnya / Dll...</option>
                        </select>
                    </div>
                    <div class="mb-5" x-show="alasanIjin === 'Lainnya'">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Ketik Alasan Spesifik</label>
                        <input type="text" name="alasan_lainnya" x-model="alasanLainnya" :required="alasanIjin === 'Lainnya'" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm" placeholder="Ketik alasan spesifik">
                    </div>
                </div>

                <!-- Bagian Kamera & GPS (Hanya muncul jika Masuk atau Pulang) -->
                <div x-show="tipeAbsen !== 'Leave Office'" x-collapse class="mb-6">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Swafoto (Auto-Snap) & GPS</label>
                    
                    <div class="flex items-center gap-2 mb-3 bg-blue-50 px-3 py-2 rounded-lg border border-blue-100">
                        <div class="w-2 h-2 rounded-full" :class="locationReady ? 'bg-green-500' : 'bg-red-500 animate-pulse'"></div>
                        <span class="text-xs font-semibold" :class="locationReady ? 'text-green-700' : 'text-red-600'" x-text="locationReady ? 'Lokasi GPS Terkunci' : 'Mencari Lokasi GPS...'"></span>
                    </div>

                    <div class="relative bg-gray-200 rounded-xl overflow-hidden aspect-[3/4] flex items-center justify-center border-2 border-gray-300 group">
                        
                        <div x-show="!cameraReady && !photoTaken" class="text-gray-500 text-sm font-semibold flex flex-col items-center">
                            <svg class="animate-spin h-6 w-6 mb-2 text-blue-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Menyiapkan Kamera...
                        </div>

                        <video x-ref="video" autoplay playsinline class="absolute inset-0 w-full h-full object-cover" x-show="!photoTaken && cameraReady"></video>
                        
                        <div x-show="countdown > 0 && !photoTaken && cameraReady" class="absolute inset-0 flex items-center justify-center bg-black/40 z-20">
                            <span class="text-white text-6xl font-extrabold animate-ping" x-text="countdown"></span>
                        </div>

                        <img :src="fotoBase64" class="absolute inset-0 w-full h-full object-cover" x-show="photoTaken">
                        
                        <div class="absolute bottom-4 left-0 right-0 flex justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-30" x-show="photoTaken">
                            <button type="button" @click="retakePhoto" class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-full shadow-lg text-sm font-bold flex items-center gap-2 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Ulangi Foto
                            </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="foto_bukti" x-model="fotoBase64">
                <input type="hidden" name="latitude" x-model="latitude">
                <input type="hidden" name="longitude" x-model="longitude">
                <canvas x-ref="canvas" style="display:none;"></canvas>

                <button type="submit" :disabled="isSubmitting || (tipeAbsen !== 'Leave Office' && (!photoTaken || !locationReady))" 
                        class="w-full text-white font-semibold py-3 rounded-lg transition-all"
                        :class="(isSubmitting || (tipeAbsen !== 'Leave Office' && (!photoTaken || !locationReady))) ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-200'">
                    <span x-show="!isSubmitting">Kirim Absensi</span>
                    <span x-show="isSubmitting">Memproses...</span>
                </button>
                <div class="text-center pt-6"><a href="/" class="text-sm text-gray-500 hover:text-blue-600 transition">&larr; Kembali ke Menu Utama</a></div>
            </form>
        </div>
    </div>

    <script>
        function absensiCatchtime() {
            return {
                showSuccessModal: {{ session('success') ? 'true' : 'false' }},
                showErrorModal: {{ $errors->any() ? 'true' : 'false' }},
                successMessage: '{!! session('success') !!}',
                errorMessage: {!! json_encode($errors->first('pesan') ?? $errors->first()) !!},
                
                employeeId: '',
                tipeAbsen: 'Masuk',
                alasanIjin: '',
                alasanLainnya: '',
                
                cameraReady: false,
                photoTaken: false,
                fotoBase64: '',
                locationReady: false,
                latitude: '',
                longitude: '',
                countdown: 0,
                autoSnapTimer: null,
                stream: null,
                isSubmitting: false,

                initApp() {
                    this.getLocation();
                    
                    this.$watch('tipeAbsen', value => {
                        if (value !== 'Leave Office') {
                            if (!this.stream && !this.photoTaken) this.startCamera();
                        } else {
                            this.stopCamera();
                        }
                    });

                    this.startCamera();
                },

                getLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.latitude = position.coords.latitude;
                                this.longitude = position.coords.longitude;
                                this.locationReady = true;
                            },
                            (error) => {
                                alert("Mohon izinkan akses Lokasi (GPS) agar bisa absen masuk/pulang.");
                            },
                            { enableHighAccuracy: true }
                        );
                    }
                },

                startCamera() {
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                        .then(stream => {
                            this.stream = stream;
                            this.$refs.video.srcObject = stream;
                            this.cameraReady = true;
                            this.startCountdown();
                        })
                        .catch(err => alert("Kamera tidak dapat diakses."));
                },

                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                        this.cameraReady = false;
                        if(this.autoSnapTimer) clearInterval(this.autoSnapTimer);
                    }
                },

                startCountdown() {
                    if(this.autoSnapTimer) clearInterval(this.autoSnapTimer);
                    this.countdown = 3;
                    this.autoSnapTimer = setInterval(() => {
                        this.countdown--;
                        if (this.countdown <= 0) {
                            clearInterval(this.autoSnapTimer);
                            this.takePhoto();
                        }
                    }, 1000);
                },

                takePhoto() {
                    let video = this.$refs.video;
                    let canvas = this.$refs.canvas;
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    
                    let context = canvas.getContext('2d');
                    context.translate(canvas.width, 0);
                    context.scale(-1, 1);
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    this.fotoBase64 = canvas.toDataURL('image/png');
                    this.photoTaken = true;
                },

                retakePhoto() {
                    this.photoTaken = false;
                    this.fotoBase64 = '';
                    this.startCountdown();
                },

                submitForm() {
                    if (this.tipeAbsen !== 'Leave Office' && (!this.photoTaken || !this.locationReady)) {
                        alert('Foto dan Lokasi GPS wajib ada!'); return;
                    }
                    if (this.tipeAbsen === 'Leave Office' && !this.alasanIjin) {
                        alert('Pilih alasan ijin keluar!'); return;
                    }
                    this.isSubmitting = true;
                    this.stopCamera();
                    document.getElementById('formAbsensi').submit();
                },

                closeSuccessModal() {
                    this.showSuccessModal = false;
                    window.location.href = "/";
                }
            }
        }
    </script>
</body>
</html>