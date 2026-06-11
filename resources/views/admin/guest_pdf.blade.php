<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Tamu</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10px; 
            color: #333; 
        }
        
        /* Gaya Kop Surat yang Sama Persis (Stabil Rata Tengah) */
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

        /* Gaya Tabel Data */
        .tabel-data { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .tabel-data th, .tabel-data td { 
            border: 1px solid #000; 
        }
        .tabel-data th { 
            background-color: #f2f2f2; 
            padding: 8px; 
            text-transform: uppercase; 
        }
        .tabel-data td { 
            padding: 6px; 
        }
        .text-center { 
            text-align: center; 
        }
    </style>
</head>
<body>
    
    <table class="kop-surat">
        <tr>
            <td class="logo-container">
                <img src="{{ public_path('images/icon Logo DigiBAR.png') }}" style="width: 90px; height: auto;">
            </td>
            <td class="identitas">
                <h2>DigiBAR Group</h2>
                <p>Jl. Danau Sentarum Komp. Zal Khatulistiwa No A6</p>
                <p>Kota Pontianak, Kalimantan Barat</p>
                <p>Email: digibartechnology@gmail.com | No. Telp: 0855-9000-857</p>
            </td>
        </tr>
    </table>

    <div class="text-center" style="margin-bottom: 20px;">
        <h3 style="margin-bottom: 5px; text-decoration: underline;">LAPORAN BUKU TAMU</h3>
        <p style="margin: 0;">Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong></p>
    </div>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Waktu Kedatangan</th>
                <th width="20%">Nama Tamu</th>
                <th width="20%">Instansi</th>
                <th width="20%">Tujuan / Keperluan</th>
                <th width="15%">No. HP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guests as $index => $guest)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $guest->created_at->format('d/m/Y H:i') }} WIB</td>
                <td>{{ $guest->nama_tamu }}</td>
                <td class="text-center">{{ $guest->instansi_asal }}</td>
                <td>{{ $guest->tujuan_keperluan }}</td>
                <td class="text-center">{{ $guest->no_hp }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data tamu pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="position: fixed; bottom: 0; left: 0; font-size: 8px; color: #999;">
        Dicetak secara otomatis melalui Sistem DigiBAR Digital SDM pada {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>