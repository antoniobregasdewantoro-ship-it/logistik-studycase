# 🔑 Kunci Jawaban & Bank Soal Kemungkinan Kuis Workshop Web

Dokumen ini berisi kumpulan soal praktik & teori yang paling sering muncul pada kuis **Barcode/QR Code**, **Excel**, **PDF**, **Middleware**, dan **Hosting**, lengkap dengan **kunci jawaban** dan **contoh kode siap pakai** sesuai struktur project ini.

---

## BAGIAN A: BARCODE & QR CODE

### Soal A1: Ubah QR Code (kotak/2D) menjadi Barcode Batang / Panjang (1D Code 128)
> *"Ubah tampilan kode resi dari QR Code kotak menjadi Barcode garis/panjang (1D)!"*

#### Opsi 1 — Menggunakan API Online (Paling Cepat, mirip cara `Order.php` saat ini)
Buka `app/Models/Order.php` dan ubah/tambah accessor `getBarcodeUrlAttribute()`:
```php
public function getBarcodeUrlAttribute(): string
{
    $data = urlencode($this->tracking_number);

    // Menghasilkan gambar Barcode 1D Code 128 (garis panjang)
    return "https://bwipjs-api.metafloor.com/?bcid=code128&text={$data}&scale=2&height=10&includetext";
}
```
Lalu di file Blade (`resources/views/admin/orders.blade.php` atau `resources/views/pages/tracking.blade.php`), ganti:
```html
<!-- Sebelumnya (QR Kotak) -->
<img src="{{ $order->qr_code_url }}" alt="QR">

<!-- Menjadi (Barcode Panjang) -->
<img src="{{ $order->barcode_url }}" alt="Barcode" class="h-12 w-auto object-contain">
```

#### Opsi 2 — Menggunakan Library JavaScript `JsBarcode` (Client-side, tanpa API gambar eksternal)
1. Tambahkan CDN di layout (`resources/views/layouts/admin.blade.php` atau di bagian `@push('scripts')`):
```html
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
```
2. Di HTML Blade ganti `<img>` dengan `<svg>`:
```html
<svg class="barcode"
     jsbarcode-format="CODE128"
     jsbarcode-value="{{ $order->tracking_number }}"
     jsbarcode-textmargin="2"
     jsbarcode-height="40"
     jsbarcode-width="1.5">
</svg>

<script>
    JsBarcode(".barcode").init();
</script>
```

#### Opsi 3 — Menggunakan Package PHP `picqer/php-barcode-generator` (Server-side / cocok untuk PDF)
1. Install package:
```bash
composer require picqer/php-barcode-generator
```
2. Pakai di Controller atau Blade:
```php
use Picqer\Barcode\BarcodeGeneratorHTML;
// atau BarcodeGeneratorPNG untuk hasil gambar base64 (aman di DomPDF)

$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
$barcodeBase64 = base64_encode(
    $generator->getBarcode($order->tracking_number, $generator::TYPE_CODE_128)
);
```
Tampilkan di Blade / PDF:
```html
<img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="Barcode">
```

---

### Soal A2: Mengapa QR Code / Barcode tidak muncul saat di-export ke PDF?
**Kunci Jawaban:**
1. DomPDF memblokir URL gambar eksternal secara default. Pastikan opsi `isRemoteEnabled` diaktifkan:
   ```php
   Pdf::loadView('...', compact('orders'))
       ->setOption('isRemoteEnabled', true);
   ```
   *(Sudah diterapkan di `LaporanController.php` baris 27 & 60).*
2. Jika masih tidak muncul (misal server offline/SSL error), ubah gambar menjadi **Base64** (`data:image/png;base64,...`) menggunakan `file_get_contents()` atau package lokal seperti `picqer/php-barcode-generator` / `simplesoftwareio/simple-qrcode`.

---

### Soal A3: Apa perbedaan QR Code (2D) dan Barcode Batang (1D)?
**Kunci Jawaban:**
- **Barcode 1D (Code 128, EAN-13):** Menyimpan data secara horizontal saja, kapasitas kecil (biasanya angka/kode pendek seperti nomor resi `ORD-12345`), dibaca scanner laser/kamera.
- **QR Code 2D:** Menyimpan data secara horizontal & vertikal, kapasitas jauh lebih besar (bisa menyimpan URL lengkap seperti `https://domain.com/?tracking=ORD-12345`), dan punya *error correction* (tetap terbaca meski sedikit rusak).

---

## BAGIAN B: EXCEL (`maatwebsite/excel`)

### Soal B1: Sebutkan minimal 4 Concern/Interface penting pada Export Excel dan fungsinya!
**Kunci Jawaban** (lihat `app/Exports/OrdersExport.php`):
1. `FromCollection` / `FromArray` / `FromView` → Sumber data yang akan di-export.
2. `WithHeadings` → Menentukan baris judul kolom (`headings(): array`).
3. `WithMapping` → Memformat/memilih kolom per baris data (`map($row): array`), misalnya format tanggal atau format Rupiah.
4. `WithColumnWidths` → Mengatur lebar masing-masing kolom (`['A' => 5, 'B' => 16, ...]`).
5. `WithStyles` → Memberi styling (bold header, warna background, alignment, border) memakai `PhpOffice\PhpSpreadsheet\Worksheet\Worksheet`.
6. `WithTitle` → Memberi nama tab/sheet di Excel.

---

### Soal B2: Bagaimana cara menambah kolom baru pada Export Excel?
**Kunci Jawaban** (di `app/Exports/OrdersExport.php`):
1. Tambahkan nama header baru di method `headings()`.
2. Tambahkan nilai datanya di urutan yang sama pada method `map($order)`.
3. Tambahkan lebar kolomnya di `columnWidths()` (misal `'R' => 20`).
4. Perbarui `$lastCol = 'R'` di method `styles()` agar border dan styling mencakup kolom baru tersebut.

---

### Soal B3: Bagaimana cara kerja Import Excel dengan Template dan menampilkan ringkasan baris?
**Kunci Jawaban** (lihat `app/Imports/OrdersImport.php` & `app/Http/Controllers/ExcelController.php`):
- `WithHeadingRow` + `headingRow(): int` → Menentukan baris mana yang dianggap sebagai nama kolom (di project ini baris `2`, karena baris `1` berisi teks petunjuk).
- `SkipsEmptyRows` → Otomatis melewati baris kosong agar tidak error.
- `WithValidation` + `rules()` → Memvalidasi tiap baris (jika gagal, melempar `\Maatwebsite\Excel\Validators\ValidationException`).
- `ToCollection` → Mengolah data baris per baris di `collection(Collection $rows)`, menghitung `$this->importedCount++` untuk yang sukses dan `$this->skippedCount++` untuk yang dilewati, lalu ditampilkan di `ExcelController::importOrders()` melalui flash session `->with('success', $summary)`.

---

### Soal B4: Error umum saat install/menjalankan `maatwebsite/excel` di Windows/Laragon
**Kunci Jawaban:**
- **`ext-zip` is missing** → Buka `php.ini`, hapus tanda `;` pada `;extension=zip` menjadi `extension=zip`, lalu restart Apache/Nginx/terminal.
- **`ext-gd` is missing** → Aktifkan `extension=gd` di `php.ini` jika export/import memakai gambar atau styling tertentu.

---

## BAGIAN C: PDF (`barryvdh/laravel-dompdf`)

### Soal C1: Bagaimana cara mengubah orientasi kertas dan membedakan Preview vs Download PDF?
**Kunci Jawaban** (lihat `app/Http/Controllers/LaporanController.php`):
- Mengubah ukuran & orientasi kertas:
  ```php
  $pdf = Pdf::loadView('laporan.pdf.manifest', compact('orders'))
      ->setPaper('a4', 'portrait'); // ganti 'landscape' menjadi 'portrait'
  ```
- **Download langsung:** `return $pdf->download('nama-file.pdf');`
- **Tampil / Preview di tab browser:** `return $pdf->stream('nama-file.pdf');`

---

### Soal C2: Mengapa menggunakan `Order::with(['customer', 'courier'])->get()` sebelum membuat PDF?
**Kunci Jawaban:**
Untuk mencegah **N+1 Query Problem**. Jika tidak memakai *eager loading* (`with()`), maka setiap kali loop di Blade memanggil `$order->courier->name`, Laravel akan menjalankan query SQL baru berulang-ulang sebanyak jumlah baris order, yang membuat proses generate PDF sangat lambat atau timeout.

---

## BAGIAN D: MIDDLEWARE & KEAMANAN ROUTE

### Soal D1: Bagaimana cara membuat dan mendaftarkan Custom Middleware di Laravel versi baru (Laravel 11/12/13)?
**Kunci Jawaban:**
Di Laravel 11/12/13 tidak ada `app/Http/Kernel.php`. Registrasi alias middleware dilakukan di `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'login.throttle' => \App\Http\Middleware\LoginRateLimitMiddleware::class,
    ]);
})
```
Membuat file middleware baru via Artisan:
```bash
php artisan make:middleware RoleMiddleware
```
Isi logika pengecekan di `handle(Request $request, Closure $next, ...$roles)` (lihat `app/Http/Middleware/RoleMiddleware.php`):
- Jika belum login (`! Auth::check()`), redirect ke `login`.
- Jika role user tidak ada di `$roles`, kembalikan `abort(403)`.
- Jika lolos, lanjutkan request dengan `return $next($request);`.

---

### Soal D2: Bagaimana cara mengamankan route Export & Import Excel agar hanya Admin yang bisa mengakses?
**Kunci Jawaban:**
Pasang route di dalam grup middleware `['auth', 'role:admin']` pada `routes/web.php`:
```php
use App\Http\Controllers\ExcelController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders/export-excel', [ExcelController::class, 'exportOrders'])->name('orders.export-excel');
    Route::get('/orders/template-excel', [ExcelController::class, 'downloadTemplate'])->name('orders.template-excel');
    Route::post('/orders/import-excel', [ExcelController::class, 'importOrders'])->name('orders.import-excel');
});
```
> **Pembuktian di terminal (sering diminta di soal):**
> ```bash
> php artisan route:list -v
> ```
> Perintah `-v` (*verbose*) akan menampilkan kolom **Middleware** (`web`, `auth`, `role:admin`) di samping setiap route.

---

### Soal D3: Apa perbedaan middleware `auth` dengan `role:admin`?
**Kunci Jawaban:**
- `auth` hanya mengecek apakah pengguna **sudah login** (siapa pun, baik customer, kurir, maupun admin).
- `role:admin` mengecek apakah pengguna yang sudah login **memiliki hak akses (role) `admin`**. Jika customer/kurir mencoba mengakses route `role:admin`, sistem menolak dengan HTTP **403 Forbidden**.

---

## BAGIAN E: HOSTING & DEPLOYMENT LARAVEL

### Soal E1: Perubahan wajib pada file `.env` saat upload ke Hosting (Production)!
**Kunci Jawaban** (bandingkan dengan `.env.example`):
```env
APP_NAME="Meridian Logistics"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_db_hosting
DB_USERNAME=user_db_hosting
DB_PASSWORD=password_kuat
```
- **Mengapa `APP_DEBUG=false` wajib?** Agar jika terjadi error, detail stack trace, query database, dan credential tidak bocor ke publik.
- **Kapan pakai `php artisan key:generate`?** Jika `APP_KEY` masih kosong saat deploy pertama kali.

---

### Soal E2: Kenapa gambar/file upload (foto bukti kurir, PDF terlampir) tidak muncul setelah di-hosting (404 Not Found)?
**Kunci Jawaban:**
Karena file upload disimpan di `storage/app/public`, sedangkan web server hanya melayani folder `public/`. Harus dibuat **symbolic link** dari `public/storage` ke `storage/app/public`:
```bash
php artisan storage:link
```
*(Catatan di shared hosting tanpa akses SSH: bisa dibuat lewat route sementara yang memanggil `Artisan::call('storage:link')` atau `symlink()`, lalu hapus route tersebut setelah berhasil).*

---

### Soal E3: Urutan perintah terminal yang dijalankan saat deploy ke server/hosting
**Kunci Jawaban:**
```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env        # lalu edit DB & APP_URL
php artisan key:generate
php artisan migrate --force # --force wajib jika APP_ENV=production
php artisan storage:link
npm install && npm run build
php artisan optimize        # menjalankan config:cache, route:cache, view:cache
```

---

### Soal E4: Fungsi file `public/.htaccess` dan cara mengatasi Error 404 di semua route selain `/` saat hosting Apache
**Kunci Jawaban:**
- `public/.htaccess` mengaktifkan `RewriteEngine On` dan mengarahkan semua URL (yang bukan file/folder fisik) ke `index.php` (Front Controller Laravel).
- Jika di shared hosting URL `/login` atau `/admin/orders` menghasilkan **404 Not Found**, penyebab umumnya:
  1. Document Root domain masih mengarah ke root project, bukan ke folder `public/` (atau tidak ada `.htaccess` di root yang meneruskan ke `public/`).
  2. Modul `mod_rewrite` Apache belum aktif.

---

### Soal E5: Cara mengatasi CSS/JS tidak termuat atau Mixed Content (HTTP vs HTTPS) saat sudah pakai SSL/HTTPS
**Kunci Jawaban:**
1. Pastikan `APP_URL` di `.env` menggunakan `https://...` dan jalankan `npm run build` sebelum upload (atau di server) agar folder `public/build` ter-generate.
2. Jika aplikasi berada di balik reverse proxy / Cloudflare / PaaS (Railway, Render, Heroku), paksa skema HTTPS di `app/Providers/AppServiceProvider.php`:
   ```php
   use Illuminate\Support\Facades\URL;

   public function boot(): void
   {
       if ($this->app->environment('production')) {
           URL::forceScheme('https');
       }
   }
   ```
