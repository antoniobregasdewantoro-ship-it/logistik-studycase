# 🛡️ Kunci Jawaban Materi 5: Middleware & Export PDF (`DomPDF`)

---

## A. Peta File yang Berhubungan di Project Ini
1. **Registrasi Alias Middleware (Laravel 11/12/13):** `bootstrap/app.php`
2. **Custom Role Middleware:** `app/Http/Middleware/RoleMiddleware.php`
3. **Login Throttle Middleware:** `app/Http/Middleware/LoginRateLimitMiddleware.php`
4. **Definisi Route & Proteksi:** `routes/web.php`
5. **Controller PDF:** `app/Http/Controllers/LaporanController.php` & `app/Http/Controllers/AdminController.php`
6. **Template Blade PDF:** `resources/views/laporan/pdf/manifest.blade.php`, `transaksi.blade.php`, `barang.blade.php`, `resources/views/admin/report-pdf.blade.php`

---

## B. Kemungkinan Soal Kuis Middleware & Kunci Jawaban

### 1. Di mana mendaftarkan alias Middleware di Laravel 12/13 (pengganti `Kernel.php`)?
**Kunci Jawaban:** Di `bootstrap/app.php` di dalam `->withMiddleware(...)`:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'login.throttle' => \App\Http\Middleware\LoginRateLimitMiddleware::class,
    ]);
})
```

### 2. Bagaimana cara kerja `RoleMiddleware` di project ini?
**Kunci Jawaban** (lihat `app/Http/Middleware/RoleMiddleware.php`):
1. Cek `if (! Auth::check())` → jika belum login, redirect ke route `login`.
2. Ambil `$userRole = strtolower(Auth::user()->role ?? '')` (dan normalisasi `'kurir'` menjadi `'courier'`).
3. Cek `if (! in_array($userRole, $normalizedRoles, true))` → jika role tidak sesuai, hentikan dengan **`abort(403)`**.
4. Jika sesuai, teruskan request dengan **`return $next($request);`**.

### 3. Bagaimana membuktikan route Export/Import & PDF sudah terlindungi middleware?
**Kunci Jawaban:** Jalankan perintah Artisan dengan flag verbose:
```bash
php artisan route:list -v
```
Perintah ini menampilkan kolom **Middleware** (seperti `web`, `auth`, `role:admin`) pada setiap route.

### 4. Contoh mengamankan route Excel di `routes/web.php`:
```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders/export-excel', [ExcelController::class, 'exportOrders'])->name('orders.export-excel');
    Route::get('/orders/template-excel', [ExcelController::class, 'downloadTemplate'])->name('orders.template-excel');
    Route::post('/orders/import-excel', [ExcelController::class, 'importOrders'])->name('orders.import-excel');
});
```

---

## C. Kemungkinan Soal Kuis PDF (`barryvdh/laravel-dompdf`) & Kunci Jawaban

### 1. Bagaimana alur membuat PDF di `LaporanController.php`?
```php
$orders = Order::with(['customer', 'courier'])->latest()->get();

$pdf = Pdf::loadView('laporan.pdf.manifest', compact('orders'))
    ->setPaper('a4', 'landscape')
    ->setOption('isRemoteEnabled', true)
    ->setOption('defaultFont', 'DejaVu Sans');

return $pdf->download('laporan-manifest-' . date('Y-m-d') . '.pdf');
```
- **`with(['customer', 'courier'])`** → Mencegah **N+1 Query Problem**.
- **`setPaper('a4', 'landscape')`** → Mengatur ukuran A4 dan orientasi mendatar (bisa diganti `'portrait'`).
- **`isRemoteEnabled` => `true`** → Wajib aktif agar gambar eksternal (seperti QR Code dari API) bisa tampil di PDF.
- **`download()` vs `stream()`** → `download()` langsung mengunduh file `.pdf`; `stream()` membuka preview PDF di tab browser.


---

## 🛠️ Soal Praktik Mengotak-atik Kodingan + Kunci Jawaban

### Latihan 1: Tambah Role Baru (`supervisor`) ke Middleware Route di `routes/web.php`
> **Soal:** *"Ubah proteksi grup route laporan PDF di `routes/web.php` (baris 82) agar tidak hanya semua user `auth`, tetapi HANYA user dengan role `admin` atau `supervisor` yang bisa mengunduh PDF!"*

**File yang diubah:** `routes/web.php` (baris 82)
```php
// SEBELUM:
Route::middleware(['auth'])->group(function () {
    Route::get('/laporan/manifest/pdf', [LaporanController::class, 'exportPdfManifest'])->name('laporan.manifest.pdf');
    Route::get('/laporan/transaksi/pdf', [LaporanController::class, 'exportPdfTransaksi'])->name('laporan.transaksi.pdf');
    Route::get('/laporan/barang/pdf', [LaporanController::class, 'exportPdfBarang'])->name('laporan.barang.pdf');
});

// SESUDAH (Kunci Jawaban):
Route::middleware(['auth', 'role:admin,supervisor'])->group(function () {
    Route::get('/laporan/manifest/pdf', [LaporanController::class, 'exportPdfManifest'])->name('laporan.manifest.pdf');
    Route::get('/laporan/transaksi/pdf', [LaporanController::class, 'exportPdfTransaksi'])->name('laporan.transaksi.pdf');
    Route::get('/laporan/barang/pdf', [LaporanController::class, 'exportPdfBarang'])->name('laporan.barang.pdf');
});
```
Lalu buktikan di terminal dengan:
```bash
php artisan route:list --path=laporan -v
```

---

### Latihan 2: Mengubah Perilaku `RoleMiddleware` Jika Belum Login (Return JSON 401 untuk API)
> **Soal:** *"Modifikasi `RoleMiddleware.php` agar jika request berasal dari API/AJAX (`$request->expectsJson()`), jangan redirect ke halaman login melainkan kembalikan response JSON status `401`!"*

**File yang diubah:** `app/Http/Middleware/RoleMiddleware.php` (baris 20–22)
```php
// SEBELUM:
if (! Auth::check()) {
    return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
}

// SESUDAH (Kunci Jawaban):
if (! Auth::check()) {
    if ($request->expectsJson()) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
}
```

---

### Latihan 3: Ubah Orientasi Kertas PDF Menjadi `A4 Portrait` & Mode Default `stream()` (Preview)
> **Soal:** *"Ubah `LaporanController::exportPdfManifest()` agar kertasnya menjadi `A4 Portrait` dan langsung terbuka sebagai preview di tab browser (bukan auto-download), kecuali jika ada query `?action=download`!"*

**File yang diubah:** `app/Http/Controllers/LaporanController.php` (baris 25–36)
```php
$pdf = Pdf::loadView('laporan.pdf.manifest', compact('orders'))
    ->setPaper('a4', 'portrait') // diubah dari 'landscape' ke 'portrait'
    ->setOption('isRemoteEnabled', true)
    ->setOption('defaultFont', 'DejaVu Sans');

$filename = 'laporan-manifest-'.date('Y-m-d').'.pdf';

if ($request->query('action') === 'download') {
    return $pdf->download($filename);
}

return $pdf->stream($filename); // default preview di browser
```
