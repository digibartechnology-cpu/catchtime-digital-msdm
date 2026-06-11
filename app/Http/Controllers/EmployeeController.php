<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    // Menampilkan halaman form sekaligus tabel data
    public function index()
    {
        // Mengambil semua data karyawan, diurutkan dari yang paling baru
        $employees = Employee::latest()->get(); 
        
        // Mengirim data $employees ke tampilan blade
        return view('admin.employee_create', compact('employees'));
    }

    // Menyimpan data ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat_tempat_tinggal' => 'required',
            'no_whatsapp' => 'required|string|max:20',
            'jabatan_posisi' => 'required|string|max:100',
        ]);

        Employee::create($request->all());

        return back()->with('success', 'Data Karyawan berhasil ditambahkan!');
    }
    public function destroy($id)
{
    $employee = Employee::findOrFail($id);
    
    // Hapus karyawan (jika database diset cascade, absensi akan ikut terhapus)
    $employee->delete();

    return back()->with('success', 'Data karyawan ' . $employee->nama_lengkap . ' berhasil dihapus!');
}
}