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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->text('alamat_tempat_tinggal');
            $table->string('no_whatsapp');
            $table->string('jabatan_posisi');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
