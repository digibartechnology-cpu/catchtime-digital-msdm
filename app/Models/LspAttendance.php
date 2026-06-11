<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LspAttendance extends Model
{
    // INI WAJIB ADA AGAR LARAVEL MENGIZINKAN DATA MASUK KE DATABASE
    protected $fillable = ['lsp_session_id', 'nama_peserta', 'instansi_asal', 'foto_bukti'];

    public function session() {
        return $this->belongsTo(LspSession::class, 'lsp_session_id');
    }
}