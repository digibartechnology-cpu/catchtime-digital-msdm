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
    Schema::create('lsp_attendances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lsp_session_id')->constrained('lsp_sessions')->onDelete('cascade');
        $table->string('nama_peserta');
        $table->string('instansi_asal');
        $table->string('foto_bukti');
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('lsp_attendances');
    }
};
