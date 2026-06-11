<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan penyimpanan data
    protected $fillable = [
        'nama_tamu', 
        'instansi_asal', 
        'no_hp', 
        'tujuan_keperluan'
    ];
}