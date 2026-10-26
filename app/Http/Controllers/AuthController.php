<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Memproses data login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek kecocokan email dan password
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Ambil role user yang sedang login
            $role = Auth::user()->role;

            // Redirect ke halaman masing-masing sesuai role
            if ($role === 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($role === 'courier') {
                return redirect('/courier/tasks');
            } else {
                return redirect('/customer/dashboard');
            }
        }

        // Jika gagal login, kembali ke halaman sebelumnya dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // 2. Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Kembali ke halaman landing
    }
}