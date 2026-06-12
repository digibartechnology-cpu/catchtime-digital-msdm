<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest; 
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class GuestController extends Controller
{
    // 1. Dashboard Admin: Lihat Daftar Tamu
    public function adminIndex(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = Guest::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $guests = $query->latest()->get();

        return view('admin.guest_index', compact('guests', 'startDate', 'endDate'));
    }

    // 2. Simpan Data Tamu + Notifikasi WA Otomatis
    public function store(Request $request)
    {
        $request->validate([
            'nama_tamu' => 'required',
            'instansi_asal' => 'required',
            'no_hp' => 'required',
            'tujuan_keperluan' => 'required',
        ]);

        $guest = Guest::create([
            'nama_tamu' => $request->nama_tamu,
            'instansi_asal' => $request->instansi_asal,
            'no_hp' => $request->no_hp,
            'tujuan_keperluan' => $request->tujuan_keperluan,
        ]);

        // PESAN WHATSAPP
        $pesan = "🔔 *TAMU BARU - LSP CITRA INSAN X PT ANANTA JAYA UTAMA ABADI*\n";
        $pesan .= "👤 Nama: " . $guest->nama_tamu . "\n";
        $pesan .= "🏢 Instansi: " . $guest->instansi_asal . "\n";
        $pesan .= "📝 Keperluan: " . $guest->tujuan_keperluan . "\n";
        $pesan .= "⏰ Waktu: " . now()->format('d/m/Y H:i') . " WIB";
        $pesan .= "\n\n_(Ini merupakan pesan otomatis, harap untuk tidak membalasnya)_"; // Pesan tambahan miring

        // KIRIM WA (Fix cURL error 77 dan cegah error env di Laravel Cloud)
        $token = env('FONNTE_TOKEN', 'KvH5jtTzc6yagwZsy6qa');
        $target = env('WA_HRD', '6281348467817'); // <-- INI NOMOR PENERIMA

        \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $target,
            'message' => $pesan,
        ]);

        return back()->with('success', 'Data tamu tersimpan & WA Terkirim!');
    }

    // 3. Download Laporan PDF
    public function downloadPdf(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $query = Guest::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $guests = $query->latest()->get();
        $pdf = Pdf::loadView('admin.guest_pdf', compact('guests', 'startDate', 'endDate'));
        return $pdf->download('Laporan_Buku_Tamu.pdf');
    }
}