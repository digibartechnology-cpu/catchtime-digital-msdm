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
        Schema::table('attendances', function (Blueprint $table) {
            // KITA MATIKAN BARIS INI AGAR TIDAK BENTROK (DUPLICATE COLUMN) KARENA SUDAH ADA DI TABEL UTAMA
            // $table->string('keterangan')->nullable()->after('foto_bukti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Kita matikan juga fungsi hapusnya agar selaras dan aman
            // $table->dropColumn('keterangan');
        });
    }
};