<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OrderController;
use App\Models\Barang;
use App\Models\Branch;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Meridian Logistics Platform
|--------------------------------------------------------------------------
| Seluruh rute aplikasi didefinisikan di sini dengan proteksi middleware RBAC
| (Role-Based Access Control) untuk Admin, Kurir, dan Pelanggan.
*/

// =========================================================================
// 1. RUTE PUBLIK (DAPAT DIAKSES OLEH SIAPA SAJA TANPA LOGIN)
// =========================================================================

// Kode ini untuk menampilkan landing page utama publik (Hero, Cek Tarif, Keunggulan & Showcase Armada)
Route::get('/', [LandingController::class, 'index'])->name('home');

// Kode ini untuk menampilkan halaman pelacakan resi real-time & peta GPS checkpoint pengiriman
Route::get('/lacak', [LandingController::class, 'trackingPage'])->name('tracking');
Route::get('/tracking', [LandingController::class, 'trackingPage']); // Kode ini untuk alias rute URL tracking

// Kode ini untuk mencetak label resi pengiriman model Shopee (Thermal 100x150 mm / A6)
Route::get('/resi/{tracking_number}/cetak', [OrderController::class, 'printResi'])->name('resi.cetak');
Route::get('/resi/{tracking_number}/pdf', [OrderController::class, 'downloadResiPdf'])->name('resi.pdf');
Route::get('/orders/{id}/cetak-resi', [OrderController::class, 'printResi'])->name('orders.print-resi');

// Kode ini untuk menampilkan Halaman Detail Barang & Implementasi Milon Barcode Code 39
Route::get('/barang/{id}', function ($id) {
    $barang = Barang::findOrFail($id);
    $size = request('size', '3x100');
    $parts = explode('x', $size);
    $w = isset($parts[0]) && is_numeric($parts[0]) ? (int) $parts[0] : 3;
    $h = isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : 100;

    return view('pages.barang.show', compact('barang', 'w', 'h', 'size'));
})->name('barang.show');

// Kode ini untuk API kalkulasi estimasi tarif ongkir otomatis berdasarkan berat dan jenis layanan
Route::post('/api/calculate-tariff', [LandingController::class, 'calculateTariffApi'])->name('api.tariff');

// Kode ini untuk API pengecekan detail status dan posisi paket berdasarkan nomor resi
Route::get('/api/track/{tracking_number}', [LandingController::class, 'trackApi'])->name('api.track');

// =========================================================================
// 2. RUTE AUTENTIKASI, REGISTRASI & GOOGLE OAUTH
// =========================================================================

// Kode ini untuk menampilkan formulir login atau mengarahkan pengguna yang sudah login ke dashboard sesuai role-nya
Route::get('/login', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (in_array($role, ['courier', 'kurir'])) {
            return redirect()->route('courier.tasks');
        }

        return redirect()->route('customer.dashboard');
    }

    return view('pages.login');
})->name('login');

// Kode ini untuk memproses submit autentikasi login (dilengkapi middleware 'login.throttle' untuk proteksi brute force)
Route::post('/login', [AuthController::class, 'login'])
    ->middleware(['login.throttle'])
    ->name('login.post');

// Kode ini untuk menampilkan halaman formulir pendaftaran akun baru (Pelanggan / Kurir)
Route::get('/register', [AuthController::class, 'registerView'])->name('register');

// Kode ini untuk memproses penyimpanan pendaftaran akun baru ke dalam database
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Kode ini untuk mengarahkan pengguna ke halaman login Single Sign-On (SSO) Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');

// Kode ini untuk menerima callback hasil otentikasi akun dari Google OAuth
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Kode ini untuk memproses logout dan menghapus sesi autentikasi pengguna
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =========================================================================
// 3. RUTE EKSTRAK LAPORAN PDF RESMI
// =========================================================================

// Kode ini untuk middleware autentikasi (wajib login) dalam mengekstrak dan mengunduh laporan PDF resmi
Route::middleware(['auth'])->group(function () {
    // Kode ini untuk mengekstrak laporan manifest pengiriman resmi dalam format file PDF A4 landscape
    Route::get('/laporan/manifest/pdf', [LaporanController::class, 'exportPdfManifest'])->name('laporan.manifest.pdf');

    // Kode ini untuk mengekstrak laporan rekapitulasi transaksi & keuangan dalam format file PDF A4 landscape
    Route::get('/laporan/transaksi/pdf', [LaporanController::class, 'exportPdfTransaksi'])->name('laporan.transaksi.pdf');

    // Kode ini untuk mengekstrak laporan data kargo & barang pengiriman dalam format file PDF A4 landscape
    Route::get('/laporan/barang/pdf', [LaporanController::class, 'exportPdfBarang'])->name('laporan.barang.pdf');

    // Kode ini untuk mengekstrak data barang / manifest pengiriman ke file Microsoft Excel (.xlsx)
    Route::get('/laporan/barang/excel', [LaporanController::class, 'exportExcelBarang'])->name('laporan.barang.excel');

    // Kode ini untuk mengekstrak data transaksi & omzet keuangan ke file Microsoft Excel (.xlsx)
    Route::get('/laporan/transaksi/excel', [LaporanController::class, 'exportExcelTransaksi'])->name('laporan.transaksi.excel');

    // Kode ini untuk export laporan excel umum (alias ke exportExcelBarang)
    Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
});

// =========================================================================
// 4. RUTE KHUSUS ADMINISTRATOR (RBAC: role:admin)
// =========================================================================

// Kode ini untuk middleware proteksi hak akses admin ('auth' & 'role:admin') dengan prefix URL '/admin'
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Kode ini untuk menampilkan dashboard admin (Ringkasan Armada, 4 Bento metric card, dan tabel pesanan aktif)
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Kode ini untuk menampilkan formulir pesan pengiriman baru lengkap dengan kalkulator real-time & pilihan cabang hub
    Route::get('/new-order', [AdminController::class, 'newOrderView'])->name('new-order');

    // Kode ini untuk menampilkan daftar tabel kelola manifest pesanan beserta fitur pencarian dan filter
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');

    // Kode ini untuk menyimpan pesanan pengiriman baru ke database lengkap dengan detail kargo dan foto kondisi awal
    Route::post('/orders/store', [AdminController::class, 'storeOrder'])->name('orders.store');

    // Kode ini untuk mengimpor data manifest pesanan secara massal dari file Excel (.xlsx, .xls, .csv)
    Route::post('/orders/import-excel', [AdminController::class, 'importExcel'])->name('import.excel');

    // Kode ini untuk rute tambahan export manifest, download template, dan import pesanan via ExcelController
    Route::get('/excel/export', [ExcelController::class, 'exportOrders'])->name('excel.export');
    Route::get('/excel/template', [ExcelController::class, 'downloadTemplate'])->name('excel.template');
    Route::post('/excel/import', [ExcelController::class, 'importOrders'])->name('excel.import');

    // Kode ini untuk memperbarui data manifest, alamat tujuan lengkap, checkpoint, bobot kargo, dan koordinat GPS
    Route::put('/orders/{id}/update', [AdminController::class, 'updateOrder'])->name('orders.update');

    // Kode ini untuk memverifikasi manifest pesanan oleh admin dan mengubah status menjadi 'Dalam Perjalanan'
    Route::post('/orders/{id}/verify', [AdminController::class, 'verifyOrder'])->name('orders.verify');

    // Kode ini untuk menghapus data pesanan / manifest pengiriman dari database
    Route::delete('/orders/{id}/delete', [AdminController::class, 'destroyOrder'])->name('orders.destroy');

    // Kode ini untuk mengunggah foto bukti serah terima paket pengiriman (POD)
    Route::post('/orders/{id}/upload-proof', [AdminController::class, 'uploadProof'])->name('orders.upload-proof');

    // Kode ini untuk mengunggah berkas dokumen PDF berita acara resmi manifest pengiriman
    Route::post('/orders/{id}/upload-pdf', [LaporanController::class, 'uploadPdfReport'])->name('orders.upload-pdf');

    // Kode ini untuk menampilkan halaman manajemen kelola kurir & akun pengguna sistem
    Route::get('/users', [AdminController::class, 'usersView'])->name('users');

    // Kode ini untuk menambahkan akun kurir armada baru atau pengguna baru oleh admin
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');

    // Kode ini untuk memperbarui profil, kata sandi, hak akses (role), atau status dinas kurir
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');

    // Kode ini untuk menghapus akun pengguna dari sistem (dilengkapi proteksi anti self-delete)
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Kode ini untuk menampilkan riwayat login 3 role (Admin, Kurir, Pelanggan)
    Route::get('/login-history', [AdminController::class, 'loginHistory'])->name('login-history');

    // Kode ini untuk menampilkan halaman laporan pengiriman terintegrasi dengan ringkasan finansial & tombol cetak PDF
    Route::get('/laporan', [AdminController::class, 'laporanView'])->name('laporan');

    // Kode ini untuk menampilkan halaman CMS pengeditan konten landing page publik
    Route::get('/landing-content', [AdminController::class, 'landingContentView'])->name('landing-content');

    // Kode ini untuk menyimpan perubahan teks konten landing page dan mengunggah gambar armada lokal (maks 2MB)
    Route::post('/landing-content', [AdminController::class, 'updateLandingContent'])->name('landing-content.update');

    // Kode ini untuk mengekstrak dan mengunduh laporan PDF manifest A4
    Route::get('/export-pdf', [LaporanController::class, 'exportPdfManifest'])->name('export-pdf');
});

// =========================================================================
// 5. RUTE KHUSUS KURIR ARMADA LAPANGAN (RBAC: role:courier,admin)
// =========================================================================

// Kode ini untuk middleware proteksi hak akses kurir ('auth' & 'role:courier,admin') dengan prefix URL '/courier'
Route::middleware(['auth', 'role:courier,admin'])->prefix('courier')->name('courier.')->group(function () {

    // Kode ini untuk menampilkan halaman portal tugas kurir, manifes pengantaran, dan navigasi peta rute
    Route::get('/tasks', [CourierController::class, 'tasks'])->name('tasks');

    // Kode ini untuk kurir memperbarui status pesanan (Verifikasi -> Dalam Perjalanan -> Terkirim)
    Route::post('/tasks/{id}/status', [CourierController::class, 'updateStatus'])->name('tasks.status');

    // Kode ini untuk kurir mengunggah foto bukti serah terima paket asli di lokasi penerima (maks 2MB)
    Route::post('/tasks/{id}/upload-proof', [CourierController::class, 'uploadProof'])->name('tasks.upload-proof');

    // Kode ini untuk kurir mengubah status kesiapan bertugas (Sedang Bertugas / Standby)
    Route::post('/toggle-status', [CourierController::class, 'toggleStatus'])->name('toggle-status');
});

// =========================================================================
// 6. RUTE KHUSUS PELANGGAN (RBAC: role:customer,admin)
// =========================================================================

// Kode ini untuk middleware proteksi hak akses pelanggan ('auth' & 'role:customer') dengan prefix URL '/customer'
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {

    // Kode ini untuk menampilkan dashboard pelanggan dengan riwayat pengiriman aktif dan riwayat paket terkirim khusus milik pelanggan yang login
    Route::get('/dashboard', function () {
        $orders = Order::where('customer_id', auth()->id())
            ->orWhere(function ($q) {
                $q->where('sender_name', auth()->user()->name)
                    ->whereNotNull('sender_name');
            })
            ->with(['courier', 'review', 'reviews'])
            ->latest()
            ->get();

        $activeOrders = $orders->whereIn('status', ['Verifikasi', 'Dalam Perjalanan']);
        $deliveredOrders = $orders->where('status', 'Terkirim');
        $branches = Branch::all();

        return view('customer.dashboard', compact('orders', 'activeOrders', 'deliveredOrders', 'branches'));
    })->name('dashboard');

    // Kode ini untuk membuka form pemesanan pengiriman paket mandiri oleh pelanggan
    Route::get('/book', function () {
        $branches = Branch::all();
        $couriers = User::whereIn('role', ['courier', 'kurir'])->get();

        return view('customer.new-order', compact('branches', 'couriers'));
    })->name('book');

    // Kode ini untuk pelanggan membuka formulir order kirim paket baru (Kurir jemput di lokasi -> taruh gudang -> kirim ke tujuan)
    Route::get('/new-order', [AdminController::class, 'newOrderView'])->name('new-order');
    Route::post('/orders/store', [AdminController::class, 'storeOrder'])->name('orders.store');

    // Kode ini untuk pelanggan memberikan penilaian (rating 1-5 bintang & ulasan) kepada kurir pengantar paket
    Route::post('/orders/{id}/rate', [AdminController::class, 'rateCourier'])->name('orders.rate');

    // Kode ini untuk menampilkan formulir penilaian dan rating kepuasan pelanggan terhadap kurir
    Route::get('/rating', function () {
        return view('customer.rating');
    })->name('rating');
});
