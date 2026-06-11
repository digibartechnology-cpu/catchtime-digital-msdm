<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // KITA MATIKAN SEMUA BARIS INI KARENA KEDUA TABEL INI SUDAH SUKSES DIBUAT 
        // OLEH FILE MIGRASI ANDA YANG SEBELUMNYA.
        
        /*
        // 1. MEMBUAT TABEL JADWAL LSP
        Schema::create('lsp_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->dateTime('waktu_buka');
            $table->dateTime('waktu_tutup');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. MEMBUAT TABEL ABSENSI PESERTA LSP
        Schema::create('lsp_attendances', function (Blueprint $table) {
            $table->id();
            // Menghubungkan peserta dengan jadwal LSP-nya
            $table->foreignId('lsp_session_id')->constrained('lsp_sessions')->onDelete('cascade');
            $table->string('nama_peserta');
            $table->string('instansi_asal');
            $table->string('foto_bukti');
            $table->timestamps();
        });
        */
    }

    public function down(): void
    {
        // Matikan juga fungsi hapusnya
        /*
        Schema::dropIfExists('lsp_attendances');
        Schema::dropIfExists('lsp_sessions');
        */
    }
};