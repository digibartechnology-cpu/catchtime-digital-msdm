<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('lsp_sessions', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kegiatan');
        $table->dateTime('waktu_buka');
        $table->dateTime('waktu_tutup');
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('lsp_sessions');
    }
};
