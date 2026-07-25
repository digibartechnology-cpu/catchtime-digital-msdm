<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function adminIndex(Request $request)
    {
        $employees = Employee::orderBy('nama_lengkap', 'asc')->get();
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = Attendance::with('employee');
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $attendances = $query->latest()->get();
        return view('admin.attendance_create', compact('employees', 'attendances', 'startDate', 'endDate'));
    }

    public function adminStore(Request $request)
{
    $request->validate([
        'employee_id' => 'required',
        'type' => 'required|in:Masuk,Pulang',
        'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $imageName = 'default.png';
    
    // --- UBAH BAGIAN INI ---
    if ($request->hasFile('foto_bukti')) {
        $imageName = time() . '_' . uniqid() . '.' . $request->foto_bukti->extension();
        
        // Upload file biasa ke Cloud (S3) di folder 'absensi'
        $request->file('foto_bukti')->storeAs('absensi', $imageName, 's3', 'public');
    }
    // -----------------------

    $attendance = Attendance::create([
        'employee_id' => $request->employee_id,
        'type' => $request->type,
        'foto_bukti' => $imageName,
    ]);

    // --- AWAL TAMBAHAN LOGIKA JAM KERJA ---
    // Menggunakan timezone WIB agar akurat dengan jam lokal Pontianak
    $jamAbsen = now()->timezone('Asia/Jakarta')->format('H:i');
    $statusWaktu = '';
    
    if ($attendance->type == 'Masuk') {
        $statusWaktu = ($jamAbsen > '09:00') ? '🔴 *TELAT*' : '🟢 *On Time*';
    } elseif ($attendance->type == 'Pulang') {
        $statusWaktu = ($jamAbsen < '17:00') ? '🟡 *Pulang Awal*' : '🟢 *On Time*';
    }
    // --- AKHIR TAMBAHAN LOGIKA JAM KERJA ---

    $pesanWA = "📌 *ABSENSI MANUAL (ADMIN)*\n👤 Nama: " . $attendance->employee->nama_lengkap . "\n📅 Tipe: " . $attendance->type;
    
    // Menyisipkan status waktu ke pesan WA
    if ($statusWaktu != '') {
        $pesanWA .= "\n🚦 Status: " . $statusWaktu;
    }

    $this->sendWhatsappNotification($pesanWA);

    return redirect()->route('admin.absensi.index')->with('success', 'Data tersimpan & WA Terkirim!');
}

    public function userStore(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'type' => 'required',
            'foto_bukti' => 'required_unless:type,Leave Office|nullable|string', 
        ]);

        $sudahAbsen = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('created_at', Carbon::today())
            ->where('type', $request->type)
            ->exists();

        if ($sudahAbsen) {
            return back()->withErrors(['pesan' => 'AKSES DITOLAK: Anda sudah melakukan absensi "' . $request->type . '" hari ini.']);
        }

        $absenTerakhir = Attendance::where('employee_id', $request->employee_id)
                        ->whereDate('created_at', Carbon::today())
                        ->orderBy('created_at', 'desc')
                        ->first();

        if ($request->type === 'Pulang' && $absenTerakhir && $absenTerakhir->type === 'Leave Office') {
            return back()->withErrors(['pesan' => 'AKSES DITOLAK: Anda masih berstatus "Leave Office". Harap lapor "Kembali ke Kantor" terlebih dahulu.']);
        }

        $keterangan_alasan = null;
        if ($request->type === 'Leave Office') {
            $keterangan_alasan = ($request->alasan_ijin === 'Lainnya') ? $request->alasan_lainnya : $request->alasan_ijin;
        }

        $imageName = 'Tanpa Foto'; 
        if ($request->filled('foto_bukti') && str_contains($request->foto_bukti, 'base64')) {
            try {
                // Memecah teks base64 dari kamera
                $image_parts = explode(";base64,", $request->foto_bukti);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                
                // Buat nama file unik
                $imageName = time() . '_' . uniqid() . '.' . $image_type;
                $path = 'absensi/' . $imageName;
                
                // Simpan gambar ke Cloud (S3/Cloudflare R2)
                Storage::disk('s3')->put($path, $image_base64, 'public');

            } catch (\Exception $e) {
                return back()->withErrors(['pesan' => 'Gagal mengunggah foto ke Cloud: ' . $e->getMessage()]);
            }
        }
        $absenBaru = Attendance::create([
        'employee_id' => $request->employee_id,
        'type' => $request->type,
        'foto_bukti' => $imageName,
        'keterangan' => $keterangan_alasan 
    ]);

    $absenBaru->load('employee'); 

    // --- AWAL TAMBAHAN LOGIKA JAM KERJA ---
    $jamAbsen = now()->timezone('Asia/Jakarta')->format('H:i');
    $statusWaktu = '';
    
    if ($absenBaru->type == 'Masuk') {
        $statusWaktu = ($jamAbsen > '09:00') ? '🔴 *TELAT*' : '🟢 *On Time*';
    } elseif ($absenBaru->type == 'Pulang') {
        $statusWaktu = ($jamAbsen < '17:00') ? '🟡 *Pulang Awal*' : '🟢 *On Time*';
    } elseif ($absenBaru->type == 'Leave Office') {
        $statusWaktu = '🔵 *Ijin Keluar*';
    }
    // --- AKHIR TAMBAHAN LOGIKA JAM KERJA ---

    $pesanWA = "📌 *ABSENSI KARYAWAN*\n👤 Nama: " . $absenBaru->employee->nama_lengkap . "\n📅 Tipe: " . $absenBaru->type;
    
    // Menyisipkan status waktu ke pesan WA
    if ($statusWaktu != '') {
        $pesanWA .= "\n🚦 Status: " . $statusWaktu;
    }

    if ($keterangan_alasan) {
        $pesanWA .= "\n📝 Keterangan: " . $keterangan_alasan;
    }
    
    $this->sendWhatsappNotification($pesanWA);

        if ($request->type === 'Leave Office') {
            session(['kunci_ijin_keluar' => $absenBaru->id]);
            return redirect('/absen/terkunci');
        }

        return back()->with('success', 'Absensi berhasil disimpan!');
    }

    public function kembaliKeKantor(Request $request)
    {
        $idAbsenKeluar = session('kunci_ijin_keluar');
        $employeeId = $request->employee_id;
        $absenLama = null;

        if ($idAbsenKeluar) {
            $absenLama = Attendance::find($idAbsenKeluar);
        }

        if (!$absenLama && $employeeId) {
            $absenLama = Attendance::where('employee_id', $employeeId)
                            ->whereDate('created_at', Carbon::today())
                            ->where('type', 'Leave Office')
                            ->latest()
                            ->first();
        }

        if ($absenLama) {
            $absenKembali = Attendance::create([
                'employee_id' => $absenLama->employee_id,
                'type' => 'Kembali ke Kantor',
                'foto_bukti' => 'Tanpa Foto',
                'keterangan' => 'Selesai Ijin: ' . ($absenLama->keterangan ?? '-')
            ]);

            $absenKembali->load('employee');
            if ($absenKembali->employee) {
                $pesanWA = "📌 *UPDATE ABSENSI*\n👤 Nama: " . $absenKembali->employee->nama_lengkap . "\n📅 Tipe: " . $absenKembali->type;
                $this->sendWhatsappNotification($pesanWA);
            }
            session()->forget('kunci_ijin_keluar');
            return redirect('/')->with('success', 'Status diperbarui. Selamat bekerja kembali!');
        }

        return back()->withErrors(['pesan' => 'Gagal memperbarui status: Sesi absensi tidak ditemukan. Silakan hubungi HRD.']);
    }

    private function sendWhatsappNotification($message)
    {
        $token = env('FONNTE_TOKEN', 'KvH5jtTzc6yagwZsy6qa');
        $target = env('WA_HRD', '6281348467817'); // <-- INI NOMOR PENERIMA
        
        \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $target,
            'message' => $message . "\n⏰ Waktu: " . now()->format('d/m/Y H:i') . " WIB",
        ]);
    }

    public function userIndex()
    {
        if (session()->has('kunci_ijin_keluar')) {
            return redirect('/absen/terkunci');
        }
        $employees = Employee::orderBy('nama_lengkap', 'asc')->get();
        return view('absensi', compact('employees'));
    }

    public function destroy($id) {
        $attendance = Attendance::findOrFail($id);
        if ($attendance->foto_bukti && $attendance->foto_bukti != 'default.png' && $attendance->foto_bukti != 'Tanpa Foto' && file_exists(public_path('images/absensi/'.$attendance->foto_bukti))) {
            unlink(public_path('images/absensi/'.$attendance->foto_bukti));
        }
        $attendance->delete();
        return back()->with('success', 'Data dihapus!');
    }

    public function downloadPdf(Request $request) {
        // Beri kelonggaran waktu eksekusi dan memori server cloud
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $query = \App\Models\Attendance::with('employee');
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
        }
        
        $attendances = $query->orderBy('employee_id')->orderBy('created_at', 'asc')->get();

        // PENCEGAHAN: Jika data kosong, cegah error gateway cloud terputus
        if ($attendances->isEmpty()) {
            return back()->with('error', 'Data absensi kosong pada rentang tanggal tersebut, tidak dapat mencetak PDF.');
        }

        $reportData = [];

        // 1. Kelompokkan Data Berdasarkan Karyawan dan Tanggal
        foreach ($attendances as $absen) {
            $date = $absen->created_at->format('Y-m-d');
            $empId = $absen->employee_id;

            if (!isset($reportData[$empId])) {
                $reportData[$empId] = [
                    'nama' => $absen->employee->nama_lengkap ?? 'Karyawan Dihapus',
                    'jabatan' => $absen->employee->jabatan_posisi ?? '-',
                    'harian' => []
                ];
            }

            if (!isset($reportData[$empId]['harian'][$date])) {
                $reportData[$empId]['harian'][$date] = [
                    'tanggal_format' => $absen->created_at->format('d M Y'),
                    'masuk' => null,
                    'pulang' => null,
                    'leave' => [],
                    'kembali' => []
                ];
            }

            $time = $absen->created_at;
            $typeLokal = strtolower($absen->type);

            if (str_contains($typeLokal, 'masuk')) {
                $reportData[$empId]['harian'][$date]['masuk'] = $time;
            } elseif (str_contains($typeLokal, 'pulang')) {
                $reportData[$empId]['harian'][$date]['pulang'] = $time;
            } elseif (str_contains($typeLokal, 'ijin') || str_contains($typeLokal, 'leave')) {
                $reportData[$empId]['harian'][$date]['leave'][] = $time;
            } elseif (str_contains($typeLokal, 'kembali')) {
                $reportData[$empId]['harian'][$date]['kembali'][] = $time;
            }
        }

        // 2. Kalkulasi Jam Kerja & Total Periode
        foreach ($reportData as $empId => &$empData) {
            $totalMenitPeriode = 0;

            foreach ($empData['harian'] as $date => &$dayData) {
                if ($dayData['masuk'] && $dayData['pulang']) {
                    $masuk = \Carbon\Carbon::parse($dayData['masuk']);
                    $pulang = \Carbon\Carbon::parse($dayData['pulang']);
                    
                    $totalMinutes = $masuk->diffInMinutes($pulang);

                    $leaveMinutes = 0;
                    $leaveCount = min(count($dayData['leave']), count($dayData['kembali']));
                    for ($i = 0; $i < $leaveCount; $i++) {
                        $leaveStart = \Carbon\Carbon::parse($dayData['leave'][$i]);
                        $leaveEnd = \Carbon\Carbon::parse($dayData['kembali'][$i]);
                        $leaveMinutes += $leaveStart->diffInMinutes($leaveEnd);
                    }

                    $netMinutes = $totalMinutes - $leaveMinutes;
                    if ($netMinutes < 0) $netMinutes = 0; 

                    $totalMenitPeriode += $netMinutes;

                    $hours = floor($netMinutes / 60);
                    $minutes = $netMinutes % 60;
                    
                    $dayData['total_jam_text'] = $hours . ' Jam ' . $minutes . ' Menit';
                    
                    if ($leaveMinutes > 0) {
                        $leaveH = floor($leaveMinutes / 60);
                        $leaveM = $leaveMinutes % 60;
                        $dayData['durasi_ijin'] = $leaveH . ' Jam ' . $leaveM . ' Menit';
                    } else {
                        $dayData['durasi_ijin'] = '-';
                    }
                    
                } else {
                    $dayData['total_jam_text'] = 'Data Tidak Lengkap (Lupa Absen)';
                    $dayData['durasi_ijin'] = '-';
                }
            }

            $jamPeriode = floor($totalMenitPeriode / 60);
            $menitPeriode = $totalMenitPeriode % 60;
            $empData['total_jam_periode'] = $jamPeriode . ' Jam ' . $menitPeriode . ' Menit';
        }

        $pdf = Pdf::loadView('admin.attendance_pdf', [
            'reportData' => $reportData,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Absensi_DigiBAR.pdf');
    }
}