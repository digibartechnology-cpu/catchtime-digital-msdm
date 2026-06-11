<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // Baris ini WAJIB ada untuk memberikan izin form menyimpan data ke kolom ini
    protected $fillable = [
        'employee_id', 
        'type', 
        'foto_bukti', 
        'keterangan'
    ];
    // Relasi: 1 Absensi ini milik 1 Karyawan (Agar nama karyawan bisa dipanggil di tabel)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}