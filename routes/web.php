<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LspSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LspAttendance;

// Import semua Controller yang dibutuhkan
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AuthController;

// ==========================================
// RUTE LAYAR TERKUNCI (IJIN KELUAR)
// ==========================================
// 1. Halaman Gembok (INI YANG HILANG SEBELUMNYA)
Route::get('/absen/terkunci', function () {
    if (!session()->has('kunci_ijin_keluar')) {
        return redirect('/'); // Tendang balik jika tidak ada status ijin
    }
    return view('lock_screen');
});

// 2. Proses Buka Kunci (Saat Klik Tombol "Sudah Kembali") -> FIXED 405
Route::post('/absen/buka-kunci', [AttendanceController::class, 'kembaliKeKantor'])->name('absen.kembali');

// ==========================================
// ROUTE HALAMAN DEPAN (Cek Jadwal LSP & Cek Gembok)
// ==========================================
Route::get('/', function () {
    // PELINDUNG: Cegah akses jika karyawan sedang ijin keluar
    if (session()->has('kunci_ijin_keluar')) {
        return redirect('/absen/terkunci');
    }

    $sekarang = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
    
    $sesiLspAktif = \App\Models\LspSession::where('is_active', true)
        ->where('waktu_buka', '<=', $sekarang)
        ->where('waktu_tutup', '>=', $sekarang)
        ->first();

    return view('welcome', compact('sesiLspAktif'));
});

// ==========================================
// FITUR ABSENSI UNTUK USER/KARYAWAN
// ==========================================
Route::get('/absen', [AttendanceController::class, 'userIndex'])->name('absen.index');
Route::post('/absen/store', [AttendanceController::class, 'userStore']);

// Fitur Buku Tamu untuk Pengunjung DigiBAR
Route::get('/buku-tamu-form', function () {
    return view('buku_tamu');
})->name('tamu.form');
Route::post('/buku-tamu/store', [GuestController::class, 'store']);

// ==========================================
// RUTE LOGIN & LOGOUT
// ==========================================
Route::get('/login', function () {
    return view('login'); 
})->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// RUTE KHUSUS ADMIN (WAJIB LOGIN)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    
    // Manajemen Karyawan
    Route::get('/admin/karyawan', [EmployeeController::class, 'index']);
    Route::post('/admin/karyawan', [EmployeeController::class, 'store']);
    Route::delete('/admin/karyawan/{id}', [EmployeeController::class, 'destroy']);
    
    // Manajemen Absensi - JANGAN UBAH URUTAN INI
    Route::get('/admin/absensi', [AttendanceController::class, 'adminIndex'])->name('admin.absensi.index');
    
    // 1. Simpan harus di atas (agar tidak dikira ID 'store')
    Route::post('/admin/absensi/store', [AttendanceController::class, 'adminStore'])->name('admin.absensi.store');
    
    // 2. Download PDF
    Route::get('/admin/absensi/download-pdf', [AttendanceController::class, 'downloadPdf']);
    
    // 3. Hapus harus di bawah (wildcard {id})
    Route::delete('/admin/absensi/{id}', [AttendanceController::class, 'destroy'])->name('admin.absensi.destroy');
    
    // Manajemen Buku Tamu
    Route::get('/admin/buku-tamu', [GuestController::class, 'adminIndex']);
    Route::get('/admin/buku-tamu/download-pdf', [GuestController::class, 'downloadPdf']);
});

// ==========================================
// PINTU PEMBUAT AKUN ADMIN
// ==========================================
Route::get('/generate-admin', function () {
    User::updateOrCreate(
        ['email' => 'admin@digibar.com'],
        [
            'name' => 'Admin DigiBAR',
            'password' => Hash::make('admin') 
        ]
    );
    return "Akun admin berhasil disiapkan! Silakan kembali ke halaman login.";
});

// ==========================================
// ROUTE HALAMAN FORM ABSEN LSP
// ==========================================
Route::get('/absen-lsp/{id}', function ($id) {
    $sesi = LspSession::findOrFail($id);
    
    // Validasi pencegahan jika ada yang memaksa masuk link saat waktu sudah habis
    if (now() > $sesi->waktu_tutup) {
        return redirect('/')->with('error', 'Waktu absensi sudah ditutup!');
    }

    return view('lsp_absen', compact('sesi'));
});

// 3. ROUTE PROSES SIMPAN ABSEN LSP
Route::post('/absen-lsp/store', function (Request $request) {
    $request->validate([
        'lsp_session_id' => 'required',
        'nama_peserta' => 'required',
        'instansi_asal' => 'required',
        // NAIKKAN BATAS MENJADI 10MB (10240 KB)
        'foto_bukti' => 'required|image|max:10240', 
    ]);

    // Simpan Foto
    $imageName = time() . '.' . $request->foto_bukti->extension();
    $request->foto_bukti->move(public_path('images/absensi_lsp'), $imageName);

    // Simpan Data
    LspAttendance::create([
        'lsp_session_id' => $request->lsp_session_id,
        'nama_peserta' => $request->nama_peserta,
        'instansi_asal' => $request->instansi_asal,
        'foto_bukti' => $imageName
    ]);

    return back()->with('success', 'Absensi berhasil disimpan!');
});

// ==========================================
// MANAJEMEN LSP ADMIN
// ==========================================
Route::get('/admin/lsp', [AdminController::class, 'lspIndex']);
Route::post('/admin/lsp/store', [AdminController::class, 'lspStore']);
Route::delete('/admin/lsp/{id}', [AdminController::class, 'lspDestroy']);
Route::get('/admin/lsp/{id}/download-pdf', [AdminController::class, 'downloadPdf']);
Route::get('/admin/lsp/{id}/attendances', [AdminController::class, 'getAttendances']);
Route::get('/admin/lsp/live-counts', [AdminController::class, 'getLiveCounts']);