<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch; // Jangan lupa import model Branch

class LandingController extends Controller
{
    public function index()
    {
        // 1. Mengambil jumlah kota dinamis (menghitung cabang yang unik berdasarkan kota)
        $jumlahKota = Branch::distinct('city')->count();
        
        // 2. Mengambil total kurir (Bisa di-hardcode dulu atau ambil dari tabel User jika ada)
        $totalKurir = 2400; // Nanti bisa diganti: User::where('role', 'courier')->count();
        
        // 3. Mengambil semua data cabang untuk ditampilkan titiknya di peta
        $branches = Branch::whereNotNull('position_x')
                          ->whereNotNull('position_y')
                          ->get();

        // Mengirim data ke resources/views/pages/landing.blade.php[cite: 27]
        return view('pages.landing', compact('jumlahKota', 'totalKurir', 'branches'));
    }
}