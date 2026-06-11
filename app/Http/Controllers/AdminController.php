<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Guest;
use App\Models\Attendance;
use App\Models\LspSession; // PENTING: Ditambahkan untuk fitur jadwal LSP
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    // =========================================================================
    // 1. FUNGSI DASHBOARD UTAMA
    // =========================================================================
    public function dashboard()
    {
        $today = Carbon::today();

        $data = [
            'totalKaryawan' => Employee::count(),
            'totalAbsensi'  => Attendance::count(),
            'totalTamu'     => Guest::count(),
            // Hitung absensi khusus hari ini
            'absenHariIni'  => Attendance::whereDate('created_at', $today)->count(),
            // Hitung tamu khusus hari ini (untuk memperbaiki error baris 112)
            'tamuHariIni'   => Guest::whereDate('created_at', $today)->count(),
        ];

        return view('admin.dashboard', $data); 
    }

    // =========================================================================
    // 2. FUNGSI KELOLA JADWAL LSP (EKSTERNAL)
    // =========================================================================
    
    // Menampilkan Halaman Kelola LSP
    public function lspIndex()
    {
        // Mengambil semua sesi beserta jumlah pesertanya (withCount)
        $sessions = LspSession::with('attendances')->withCount('attendances')->latest()->get();
        return view('admin.lsp_index', compact('sessions'));
    }

    // Menyimpan Jadwal LSP Baru
    public function lspStore(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required',
            'waktu_buka' => 'required|date',
            'waktu_tutup' => 'required|date|after:waktu_buka',
        ]);

        LspSession::create([
            'nama_kegiatan' => $request->nama_kegiatan,
            'waktu_buka' => $request->waktu_buka,
            'waktu_tutup' => $request->waktu_tutup,
            'is_active' => true
        ]);

        return back()->with('success', 'Jadwal kegiatan berhasil ditambahkan!');
    }

    // Menghapus Jadwal LSP
    public function lspDestroy($id)
    {
        LspSession::findOrFail($id)->delete();
        return back()->with('success', 'Jadwal kegiatan beserta data absensinya berhasil dihapus!');
    }
    public function downloadPdf($id)
    {
        // Ambil data sesi beserta seluruh orang yang absen di dalamnya
        $sesi = LspSession::with('attendances')->findOrFail($id);
        
        // Arahkan ke file tampilan (view) khusus untuk PDF
        $pdf = Pdf::loadView('admin.lsp_pdf', compact('sesi'));
        
        // Atur ukuran kertas menjadi A4 tegak (Portrait)
        $pdf->setPaper('A4', 'portrait');
        
        // Bersihkan spasi pada nama file agar rapi saat terdownload
        $namaFile = 'Rekap_Absen_LSP_' . str_replace(' ', '_', $sesi->nama_kegiatan) . '.pdf';
        
        return $pdf->download($namaFile);
    }
    public function getAttendances($id)
    {
        $sesi = LspSession::with('attendances')->findOrFail($id);
        
        // Kembalikan data dalam bentuk JSON agar bisa dibaca oleh JavaScript
        return response()->json($sesi->attendances);
    }
    public function getLiveCounts()
    {
        $counts = LspSession::withCount('attendances')->pluck('attendances_count', 'id');
        
        return response()->json($counts);
    }
}