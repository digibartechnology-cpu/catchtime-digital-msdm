<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Models\User;
use App\Models\LspSession;
use App\Models\LspAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// RUTE KEMBALI KANTOR (FIXED 405)
Route::post('/absen/buka-kunci', [AttendanceController::class, 'kembaliKeKantor'])->name('absen.kembali');

// RUTE LAINNYA
Route::get('/', function () {
    if (session()->has('kunci_ijin_keluar')) return redirect('/absen/terkunci');
    $sekarang = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
    $sesiLspAktif = LspSession::where('is_active', true)->where('waktu_buka', '<=', $sekarang)->where('waktu_tutup', '>=', $sekarang)->first();
    return view('welcome', compact('sesiLspAktif'));
});

Route::get('/absen', [AttendanceController::class, 'userIndex'])->name('absen.index');
Route::post('/absen/store', [AttendanceController::class, 'userStore']);
Route::get('/buku-tamu-form', function () { return view('buku_tamu'); })->name('tamu.form');
Route::post('/buku-tamu/store', [GuestController::class, 'store']);
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ADMIN MIDDLEWARE
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/absensi', [AttendanceController::class, 'adminIndex'])->name('admin.absensi.index');
    Route::post('/admin/absensi/store', [AttendanceController::class, 'adminStore'])->name('admin.absensi.store');
    Route::delete('/admin/absensi/{id}', [AttendanceController::class, 'destroy'])->name('admin.absensi.destroy');
    Route::get('/admin/buku-tamu', [GuestController::class, 'adminIndex']);
});

// ROUTE LSP
Route::post('/absen-lsp/store', function (Request $request) {
    $request->validate(['lsp_session_id' => 'required', 'nama_peserta' => 'required', 'instansi_asal' => 'required', 'foto_bukti' => 'required|image|max:10240']);
    $imageName = time() . '.' . $request->foto_bukti->extension();
    $request->foto_bukti->move(public_path('images/absensi_lsp'), $imageName);
    LspAttendance::create(['lsp_session_id' => $request->lsp_session_id, 'nama_peserta' => $request->nama_peserta, 'instansi_asal' => $request->instansi_asal, 'foto_bukti' => $imageName]);
    return back()->with('success', 'Absensi berhasil!');
});