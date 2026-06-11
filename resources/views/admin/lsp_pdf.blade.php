<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran LSP</title>
    <link rel="icon" href="{{ asset('images/Logo DigiBAR PNG.png') }}" type="image/png">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #cc0000; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; color: #cc0000; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; font-weight: bold; }
        
        .info-kegiatan { margin-bottom: 20px; }
        .info-kegiatan table { width: 50%; border: none; }
        .info-kegiatan td { padding: 3px 0; border: none; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-data th, .table-data td { border: 1px solid #999; padding: 10px; text-align: left; }
        .table-data th { background-color: #f5f5f5; font-weight: bold; text-align: center; }
        .table-data td.center { text-align: center; }
        
        .footer { margin-top: 40px; text-align: right; font-size: 12px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>DigiBAR Digital MSDM</h1>
        <p>LAPORAN REKAPITULASI KEHADIRAN PESERTA</p>
    </div>

    <div class="info-kegiatan">
        <table>
            <tr>
                <td width="30%"><strong>Nama Kegiatan</strong></td>
                <td width="5%">:</td>
                <td>{{ $sesi->nama_kegiatan }}</td>
            </tr>
            <tr>
                <td><strong>Waktu Mulai</strong></td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($sesi->waktu_buka)->format('d F Y - H:i') }} WIB</td>
            </tr>
            <tr>
                <td><strong>Total Hadir</strong></td>
                <td>:</td>
                <td>{{ $sesi->attendances->count() }} Orang</td>
            </tr>
        </table>
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Waktu Absen</th>
                <th width="35%">Nama Peserta</th>
                <th width="30%">Instansi Asal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sesi->attendances as $index => $absen)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ $absen->created_at->format('d/m/Y H:i:s') }}</td>
                <td><strong>{{ $absen->nama_peserta }}</strong></td>
                <td>{{ $absen->instansi_asal }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="center">Belum ada peserta yang melakukan absensi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d F Y H:i:s') }}</p>
    </div>

</body>
</html>