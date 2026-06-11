<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Cari user dengan username 'admin'
        $user = User::where('username', 'admin')->first();

        // 2. Jika belum ada, kita buatkan secara manual untuk menghindari error NOT NULL
        if (!$user) {
            $user = new User();
            $user->username = 'admin';  // Ini yang diminta oleh error barusan!
            $user->name = 'Admin DigiBAR';
            $user->password = \Illuminate\Support\Facades\Hash::make('admin');
            $user->save();
        }

        // 3. Langsung paksa masuk
        Auth::login($user);

        // 4. Terobos ke dashboard
        return redirect('/admin/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}