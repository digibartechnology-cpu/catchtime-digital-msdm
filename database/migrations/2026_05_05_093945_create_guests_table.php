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
    Schema::create('guests', function (Blueprint $table) {
        $table->id();
        // Pastikan nama kolom-kolom ini ada dan sama persis
        $table->string('nama_tamu');
        $table->string('instansi_asal')->nullable();
        $table->string('no_hp');
        $table->text('tujuan_keperluan');
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
