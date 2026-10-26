<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function customerDashboard()
    {
        // Nanti di sini kita akan ambil data Order dari database
        // $orders = Order::where('user_id', auth()->id())->get();
        
        // Memanggil file resources/views/pages/customer/dashboard.blade.php
        return view('customer.dashboard');
    }
}