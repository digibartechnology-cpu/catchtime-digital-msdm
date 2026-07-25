<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Karyawan</title>
    <style>
        /* CSS murni agar tampilan PDF stabil */
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        
        /* Gaya Kop Surat */
        .kop-surat { 
            width: 100%; 
            border-bottom: 3px solid #000; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
            border-collapse: collapse;
        }
        .kop-surat td {
            border: none !important; 
            vertical-align: middle;
        }
        .logo-container { 
            width: 15%; 
            text-align: center; 
        }
        .identitas { 
            width: 85%; 
            text-align: center; 
        }
        .identitas h2 { 
            margin: 0 0 5px 0; 
            font-size: 20px; 
            text-transform: uppercase;
            font-weight: bold;
        }
        .identitas p { 
            margin: 2px 0; 
            font-size: 11px; 
        }

        /* Judul Laporan */
        .judul-laporan {
            text-align: center;
            margin-bottom: 20px;
        }
        .judul-laporan h3 {
            font-size: 13px;
            text-decoration: underline;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .judul-laporan p {
            margin: 0;
            font-size: 11px;
        }

        /* Gaya Tabel Data */
        .tabel-data { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
            table-layout: fixed; 
        }
        .tabel-data th, .tabel-data td { 
            border: 1px solid #000; 
        }
        .tabel-data th { 
            background-color: #f2f2f2; 
            padding: 8px; 
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .tabel-data td { 
            padding: 6px; 
            vertical-align: middle;
            word-wrap: break-word;
        }
        .text-center {
            text-align: center;
        }

        /* Status Styling */
        .status-masuk { color: #059669; font-weight: bold; }
        .status-pulang { color: #dc2626; font-weight: bold; }
        .bg-warning { background-color: #fef08a; color: #854d0e; font-weight: bold; }

        /* Tanda Tangan */
        .footer-section {
            margin-top: 30px;
            width: 100%;
            page-break-inside: avoid;
        }
        .ttd-box {
            float: right;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="kop-surat">
        <tr>
            <td class="logo-container">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Logo-digibar.png'))) }}" alt="Logo" width="150">
            </td>
            <td class="identitas">
                <h2>DigiBAR Group</h2>
                <p>Jl. Danau Sentarum Komp. Zal Khatulistiwa No A6</p>
                <p>Kota Pontianak, Kalimantan Barat</p>
                <p>Email: digibartechnology@gmail.com | No. Telp: 0855-9000-857</p>
            </td>
        </tr>
    </table>

    <div class="judul-laporan">
        <h3>LAPORAN REKAPITULASI JAM KERJA KARYAWAN LSP CITRA INSAN dan PT ANANTA JAYA UTAMA ABADI</h3>
        <p>Periode: 
            @if($startDate && $endDate)
                <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>
            @else
                <strong>Keseluruhan Data</strong>
            @endif
        </p>
    </div>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="23%">Nama Karyawan</th>
                <th width="15%">Tanggal</th>
                <th width="18%">Waktu (In - Out)</th>
                <th width="15%">Potongan Ijin</th>
                <th width="24%">Total Jam Kerja Bersih</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($reportData as $empId => $empData)
                
                <!-- Looping Data Harian -->
                @foreach($empData['harian'] as $date => $dayData)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        <strong>{{ $empData['nama'] }}</strong><br>
                        <span style="font-size: 9px; color: #666;">{{ $empData['jabatan'] }}</span>
                    </td>
                    <td class="text-center font-bold">{{ $dayData['tanggal_format'] }}</td>
                    <td class="text-center">
                        <span class="status-masuk">{{ $dayData['masuk'] ? $dayData['masuk']->format('H:i') : '-' }}</span> s/d 
                        <span class="status-pulang">{{ $dayData['pulang'] ? $dayData['pulang']->format('H:i') : '-' }}</span>
                    </td>
                    <td class="text-center {{ $dayData['durasi_ijin'] !== '-' ? 'bg-warning' : '' }}">
                        {{ $dayData['durasi_ijin'] }}
                    </td>
                    <td class="text-center" style="font-weight: bold; font-size: 11px; {{ $dayData['total_jam_text'] == 'Data Tidak Lengkap (Lupa Absen)' ? 'color: #dc2626; font-size: 9px;' : 'color: #059669;' }}">
                        {{ $dayData['total_jam_text'] }}
                    </td>
                </tr>
                @endforeach

                <!-- BARIS BARU: REKAP TOTAL PER KARYAWAN -->
                <tr style="background-color: #e5e7eb;">
                    <td colspan="5" style="text-align: right; padding-right: 15px; font-weight: bold; text-transform: uppercase; font-size: 10px;">
                        Total Jam Kerja Bersih {{ $empData['nama'] }} Periode Ini:
                    </td>
                    <td class="text-center" style="font-weight: bold; font-size: 12px; color: #15803d;">
                        {{ $empData['total_jam_periode'] ?? '0 Jam 0 Menit' }}
                    </td>
                </tr>
                <!-- Akhir Baris Rekap -->

            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data absensi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-section">
        <div class="ttd-box">
            <p>Pontianak, {{ date('d F Y') }}</p>
            <p>Mengetahui,</p>
            <div style="height: 60px;"></div>
            <p>DigiBAR DIGITAL MSDM</p>
        </div>
    </div>

    <div style="position: fixed; bottom: 0; left: 0; font-size: 8px; color: #999;">
        Dicetak secara otomatis melalui Sistem DigiBAR Digital SDM pada {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>