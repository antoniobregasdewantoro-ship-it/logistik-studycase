<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;

// --- RUTE PUBLIK ---
Route::get('/', [LandingController::class, 'index']);

// --- RUTE AUTENTIKASI ---
Route::get('/login', function () {
    return view('pages.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTE CUSTOMER ---
Route::get('/customer/dashboard', function () {
    return view('customer.dashboard');
});
Route::get('/customer/book', function () {
    return view('customer.book');
});
Route::get('/customer/rating', function () {
    return view('customer.rating');
});

// --- RUTE ADMIN ---
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});
Route::get('/admin/orders', function () {
    return view('admin.orders');
});

// --- RUTE KURIR ---
Route::get('/courier/tasks', function () {
    return view('courier.tasks');
});