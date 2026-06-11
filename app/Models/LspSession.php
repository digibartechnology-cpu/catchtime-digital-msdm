<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LspSession extends Model
{
    protected $fillable = ['nama_kegiatan', 'waktu_buka', 'waktu_tutup', 'is_active'];

    // INSTRUKSI TAMBAHAN AGAR LARAVEL PAHAM INI ADALAH FORMAT WAKTU
    protected $casts = [
        'waktu_buka' => 'datetime',
        'waktu_tutup' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function attendances() {
        return $this->hasMany(LspAttendance::class);
    }
}
