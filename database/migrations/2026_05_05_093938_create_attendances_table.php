<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            
            // Menghubungkan absensi dengan nama karyawan
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            
            // --- TAMBAHKAN DUA KOLOM INI ---
            $table->string('type'); // Untuk menyimpan status Masuk/Pulang/Leave Office
            $table->string('foto_bukti');
            $table->string('keterangan')->nullable(); // Untuk menyimpan alasan ijin (nullable = boleh kosong)
            
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
