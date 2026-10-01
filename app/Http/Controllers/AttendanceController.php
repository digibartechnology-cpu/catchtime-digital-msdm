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
        
        if ($request->hasFile('foto_bukti')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->foto_bukti->extension();
            $request->file('foto_bukti')->storeAs('absensi', $imageName, 's3', 'public');
        }

        $attendance = Attendance::create([
            'employee_id' => $request->employee_id,
            'type' => $request->type,
            'foto_bukti' => $imageName,
        ]);

        $jamAbsen = now()->timezone('Asia/Jakarta')->format('H:i');
        $statusWaktu = '';
        
        if ($attendance->type == 'Masuk') {
            $statusWaktu = ($jamAbsen > '09:00') ? '🔴 *TELAT*' : '🟢 *On Time*';
        } elseif ($attendance->type == 'Pulang') {
            $statusWaktu = ($jamAbsen < '17:00') ? '🟡 *Pulang Awal*' : '🟢 *On Time*';
        }

        $pesanWA = "📌 *ABSENSI MANUAL (ADMIN)*\n👤 Nama: " . $attendance->employee->nama_lengkap . "\n📅 Tipe: " . $attendance->type;
        
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
            'foto_bukti' => 'nullable|string', 
        ]);

        $tipeAbsen = $request->type;
        $isIjin = in_array($tipeAbsen, ['Leave Office', 'Ijin Keluar']);

        $absenTerakhir = Attendance::where('employee_id', $request->employee_id)
                            ->orderBy('id', 'desc')
                            ->first();

        if ($tipeAbsen === 'Masuk') {
            if ($absenTerakhir && $absenTerakhir->type === 'Masuk' && Carbon::parse($absenTerakhir->created_at)->diffInHours(now()) < 16) {
                 return back()->withErrors(['pesan' => 'AKSES DITOLAK: Anda masih berada dalam shift "Masuk". Silakan tap "Pulang" jika shift sudah selesai.']);
            }
        } elseif ($tipeAbsen === 'Pulang') {
            $cekMasukTerakhir = Attendance::where('employee_id', $request->employee_id)
                                ->where('type', 'Masuk')
                                ->orderBy('id', 'desc')
                                ->first();

            if (!$cekMasukTerakhir) {
                return back()->withErrors(['pesan' => 'AKSES DITOLAK: Tidak ditemukan data "Masuk" untuk dipasangkan dengan absen "Pulang" ini.']);
            }

            if ($absenTerakhir && in_array($absenTerakhir->type, ['Leave Office', 'Ijin Keluar'])) {
                return back()->withErrors(['pesan' => 'AKSES DITOLAK: Anda masih berstatus "Ijin Keluar". Harap lapor "Kembali" terlebih dahulu.']);
            }

            if ($absenTerakhir && $absenTerakhir->type === 'Pulang' && Carbon::parse($absenTerakhir->created_at)->diffInHours(now()) < 12) {
                 return back()->withErrors(['pesan' => 'AKSES DITOLAK: Anda baru saja melakukan absensi "Pulang".']);
            }
        }

        $keterangan_alasan = null;
        if ($isIjin) {
            $keterangan_alasan = ($request->alasan_ijin === 'Lainnya') ? $request->alasan_lainnya : $request->alasan_ijin;
        }

        $imageName = 'Tanpa Foto'; 
        if ($request->filled('foto_bukti') && str_contains($request->foto_bukti, 'base64')) {
            try {
                $image_parts = explode(";base64,", $request->foto_bukti);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                
                $imageName = time() . '_' . uniqid() . '.' . $image_type;
                $path = 'absensi/' . $imageName;
                
                Storage::disk('s3')->put($path, $image_base64, 'public');
            } catch (\Exception $e) {
                return back()->withErrors(['pesan' => 'Gagal mengunggah foto ke Cloud: ' . $e->getMessage()]);
            }
        }
        
        $absenBaru = Attendance::create([
            'employee_id' => $request->employee_id,
            'type' => $tipeAbsen,
            'foto_bukti' => $imageName,
            'keterangan' => $keterangan_alasan 
        ]);

        $absenBaru->load('employee'); 

        $jamAbsen = now()->timezone('Asia/Jakarta')->format('H:i');
        $statusWaktu = '';
        
        if ($absenBaru->type == 'Masuk') {
            $statusWaktu = ($jamAbsen > '09:00') ? '🔴 *TELAT*' : '🟢 *On Time*';
        } elseif ($absenBaru->type == 'Pulang') {
            $statusWaktu = ($jamAbsen < '17:00') ? '🟡 *Pulang Awal*' : '🟢 *On Time*';
        } elseif ($isIjin) {
            $statusWaktu = '🔵 *Ijin Keluar*';
        }

        $pesanWA = "📌 *ABSENSI KARYAWAN*\n👤 Nama: " . $absenBaru->employee->nama_lengkap . "\n📅 Tipe: " . $absenBaru->type;
        
        if ($statusWaktu != '') {
            $pesanWA .= "\n🚦 Status: " . $statusWaktu;
        }

        if ($keterangan_alasan) {
            $pesanWA .= "\n📝 Keterangan: " . $keterangan_alasan;
        }

        if ($request->filled('latitude') && $request->filled('longitude')) {
            $lat = $request->latitude;
            $lng = $request->longitude;
            $pesanWA .= "\n📍 Lokasi: https://www.google.com/maps?q={$lat},{$lng}";
        }
        
        $this->sendWhatsappNotification($pesanWA);

        if ($isIjin) {
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
                            ->whereIn('type', ['Leave Office', 'Ijin Keluar'])
                            ->orderBy('id', 'desc')
                            ->first();
        }

        $request->session()->forget('kunci_ijin_keluar');
        $request->session()->save(); 

        if ($absenLama) {
            $absenKembali = Attendance::create([
                'employee_id' => $absenLama->employee_id,
                'type' => 'Kembali Ijin',
                'foto_bukti' => 'Tanpa Foto',
                'keterangan' => 'Selesai Ijin: ' . ($absenLama->keterangan ?? '-')
            ]);

            $absenKembali->load('employee');
            if ($absenKembali->employee) {
                $pesanWA = "📌 *UPDATE ABSENSI*\n👤 Nama: " . $absenKembali->employee->nama_lengkap . "\n📅 Tipe: " . $absenKembali->type;
                
                if ($request->filled('latitude') && $request->filled('longitude')) {
                    $lat = $request->latitude;
                    $lng = $request->longitude;
                    $pesanWA .= "\n📍 Lokasi: https://www.google.com/maps?q={$lat},{$lng}";
                }

                $this->sendWhatsappNotification($pesanWA);
            }
            
            return redirect('/')->with('success', 'Status diperbarui. Selamat bekerja kembali!');
        }

        return redirect('/')->with('success', 'Gembok berhasil direset.');
    }

    private function sendWhatsappNotification($message)
    {
        $token = env('FONNTE_TOKEN', 'KvH5jtTzc6yagwZsy6qa');
        $target = env('WA_HRD', '628984715214');
        
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

    public function destroy($id) 
    {
        $attendance = Attendance::findOrFail($id);
        if ($attendance->foto_bukti && $attendance->foto_bukti != 'default.png' && $attendance->foto_bukti != 'Tanpa Foto' && file_exists(public_path('images/absensi/'.$attendance->foto_bukti))) {
            unlink(public_path('images/absensi/'.$attendance->foto_bukti));
        }
        $attendance->delete();
        return back()->with('success', 'Data dihapus!');
    }

    public function downloadPdf(Request $request) 
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $query = \App\Models\Attendance::with('employee');
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
        }
        
        $attendances = $query->orderBy('employee_id')->orderBy('created_at', 'asc')->get();

        if ($attendances->isEmpty()) {
            return back()->with('error', 'Data absensi kosong pada rentang tanggal tersebut, tidak dapat mencetak PDF.');
        }

        $reportData = [];
        $shifts = [];
        
        foreach ($attendances as $absen) {
            $empId = $absen->employee_id;
            $typeLokal = strtolower($absen->type);
            
            if (!isset($reportData[$empId])) {
                $reportData[$empId] = [
                    'nama' => $absen->employee->nama_lengkap ?? 'Karyawan Dihapus',
                    'jabatan' => $absen->employee->jabatan_posisi ?? '-',
                    'harian' => []
                ];
            }

            if (str_contains($typeLokal, 'masuk')) {
                $shiftId = 'shift_' . $absen->id; 
                $reportData[$empId]['harian'][$shiftId] = [
                    'tanggal_format' => $absen->created_at->format('d M Y (H:i)'),
                    'masuk' => $absen->created_at,
                    'pulang' => null,
                    'leave' => [],
                    'kembali' => []
                ];
                $shifts[$empId] = $shiftId; 
            } elseif (isset($shifts[$empId])) {
                $activeShift = $shifts[$empId];
                
                if (str_contains($typeLokal, 'pulang')) {
                    $reportData[$empId]['harian'][$activeShift]['pulang'] = $absen->created_at;
                    unset($shifts[$empId]); 
                } elseif (str_contains($typeLokal, 'ijin') || str_contains($typeLokal, 'leave')) {
                    $reportData[$empId]['harian'][$activeShift]['leave'][] = $absen->created_at;
                } elseif (str_contains($typeLokal, 'kembali')) {
                    $reportData[$empId]['harian'][$activeShift]['kembali'][] = $absen->created_at;
                }
            }
        }

        foreach ($reportData as $empId => &$empData) {
            $totalMenitPeriode = 0;

            foreach ($empData['harian'] as $shiftId => &$shiftData) {
                if ($shiftData['masuk'] && $shiftData['pulang']) {
                    
                    $masuk = \Carbon\Carbon::parse($shiftData['masuk'])->startOfMinute();
                    $pulang = \Carbon\Carbon::parse($shiftData['pulang'])->startOfMinute();
                    
                    $totalMinutes = $masuk->diffInMinutes($pulang);

                    $leaveMinutes = 0;
                    $leaveCount = count($shiftData['leave']);
                    
                    for ($i = 0; $i < $leaveCount; $i++) {
                        $leaveStart = \Carbon\Carbon::parse($shiftData['leave'][$i])->startOfMinute();
                        
                        if (isset($shiftData['kembali'][$i])) {
                            $leaveEnd = \Carbon\Carbon::parse($shiftData['kembali'][$i])->startOfMinute();
                        } else {
                            $leaveEnd = \Carbon\Carbon::parse($shiftData['pulang'])->startOfMinute();
                        }
                        
                        $leaveMinutes += $leaveStart->diffInMinutes($leaveEnd);
                    }

                    $netMinutes = $totalMinutes - $leaveMinutes;
                    if ($netMinutes < 0) $netMinutes = 0; 

                    $totalMenitPeriode += $netMinutes;

                    $hours = floor($netMinutes / 60);
                    $minutes = $netMinutes % 60;
                    
                    $shiftData['total_jam_text'] = $hours . ' Jam ' . $minutes . ' Menit';
                    
                    if ($leaveMinutes > 0) {
                        $leaveH = floor($leaveMinutes / 60);
                        $leaveM = $leaveMinutes % 60;
                        $shiftData['durasi_ijin'] = $leaveH . ' Jam ' . $leaveM . ' Menit';
                    } else {
                        $shiftData['durasi_ijin'] = '-';
                    }
                    
                } else {
                    $shiftData['total_jam_text'] = 'Sedang Bekerja / Belum Pulang';
                    $shiftData['durasi_ijin'] = '-';
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