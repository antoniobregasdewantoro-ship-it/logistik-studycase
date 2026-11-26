# 📚 Peta File Materi Kuis Workshop Web

Dokumen ini merangkum seluruh file di project **Meridian Logistics (`logistic_studycase`)** yang berkaitan dengan materi kuis.  
Untuk **kunci jawaban & bank soal per materi**, buka file berikut di folder `docs/`:

- 📊 **[01_EXCEL_EXPORT_IMPORT.md](./01_EXCEL_EXPORT_IMPORT.md)** — Export & Import Excel (`maatwebsite/excel`), concern, template, validasi, & jebakan `ext-zip`.
- 🏷️ **[02_BARCODE_QRCODE.md](./02_BARCODE_QRCODE.md)** — Mengubah QR Code (2D) ↔ Barcode 1D (`milon/barcode` `DNS1D`/`DNS2D` & API), `{!! !!}` vs `{{ }}`.
- 📈 **[03_CHART_DAN_GRAFIK.md](./03_CHART_DAN_GRAFIK.md)** — Arsitektur `Chart.js` + `Service` (`DB::raw`, `groupBy`, `@json()`, `renderChart()`, filter `in_array()`).
- 🚀 **[04_HOSTING_HOSTINGER.md](./04_HOSTING_HOSTINGER.md)** — Panduan deploy Laravel ke **Hostinger hPanel** (Jalur A Upload Manual & Jalur B GitHub/SSH, `.env`, `index.php`, `ln -s`, Error 500).
- 🛡️ **[05_MIDDLEWARE_DAN_PDF.md](./05_MIDDLEWARE_DAN_PDF.md)** — Custom Middleware (`bootstrap/app.php`, `RoleMiddleware`, `route:list -v`) & Export PDF (`DomPDF`).

---

## 1. Materi Excel (Export & Import — `maatwebsite/excel`)

| Komponen | Lokasi File | Fungsi Utama |
| :--- | :--- | :--- |
| **Controller Excel** | `app/Http/Controllers/ExcelController.php` | Menangani `exportOrders()`, `downloadTemplate()`, dan `importOrders()` (validasi `.xlsx/.xls/.csv`, tangkap `ValidationException`, tampilkan ringkasan berhasil & dilewati). |
| **Export Data** | `app/Exports/OrdersExport.php` | Export seluruh data `Order` menggunakan `FromCollection`, `WithHeadings`, `WithMapping`, `WithColumnWidths`, `WithStyles`, `WithTitle`. |
| **Export Template** | `app/Exports/OrdersTemplateExport.php` | Membuat template Excel kosong berisi petunjuk di baris 1, header di baris 2, dan 2 baris contoh data (`FromArray`, `WithHeadings`, `WithColumnWidths`, `WithStyles`). |
| **Import Data** | `app/Imports/OrdersImport.php` | Membaca file Excel dengan `ToCollection`, `WithHeadingRow` (`headingRow()` = 2), `WithValidation`, `SkipsEmptyRows`, generate nomor resi, hitung tarif, dan mencatat `$importedCount` & `$skippedCount`. |
| **Dependency** | `composer.json` | Terdaftar `"maatwebsite/excel": "^4.0"`. |

---

## 2. Materi PDF (`barryvdh/laravel-dompdf`)

| Komponen | Lokasi File | Fungsi Utama |
| :--- | :--- | :--- |
| **Controller Laporan** | `app/Http/Controllers/LaporanController.php` | `exportPdfManifest()`, `exportPdfBarang()`, `exportPdfTransaksi()` memakai `Pdf::loadView()->setPaper('a4', 'landscape')` dengan mode `stream()` atau `download()`, serta `uploadPdfReport()`. |
| **Controller Admin** | `app/Http/Controllers/AdminController.php` | Method `exportPdf()` (baris 658–682) untuk download laporan PDF ringkasan armada & pesanan. |
| **View PDF Manifest** | `resources/views/laporan/pdf/manifest.blade.php` | Template HTML/CSS khusus cetak PDF daftar manifest pengiriman. |
| **View PDF Transaksi** | `resources/views/laporan/pdf/transaksi.blade.php` | Template HTML/CSS khusus cetak PDF rekap transaksi & armada. |
| **View PDF Barang** | `resources/views/laporan/pdf/barang.blade.php` | Meng-include `laporan.pdf.manifest`. |
| **View PDF Admin** | `resources/views/admin/report-pdf.blade.php` | Template HTML/CSS untuk `AdminController::exportPdf()`. |
| **Halaman Laporan** | `resources/views/admin/laporan.blade.php` | UI halaman laporan dengan tombol Export PDF. |
| **Dependency** | `composer.json` | Terdaftar `"barryvdh/laravel-dompdf": "^3.1"`. |

---

## 3. Materi Barcode / QR Code

| Komponen | Lokasi File | Fungsi Utama |
| :--- | :--- | :--- |
| **Accessor QR di Model** | `app/Models/Order.php` | Method `getQrCodeUrlAttribute()` (baris 147–152) menghasilkan URL QR Code via `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=...`. |
| **Migrasi Database** | `database/migrations/2026_10_19_083631_create_orders_table.php` | Kolom `qr_code_path` (baris 45) pada tabel `orders`. |
| **UI & Print QR Admin** | `resources/views/admin/orders.blade.php` | Thumbnail QR di tabel (baris 96–99), Modal QR Label (baris 633–665), serta fungsi JS `showQrModal()` dan `printQrLabel()` (baris 723–740). |
| **UI QR Tracking Publik** | `resources/views/pages/tracking.blade.php` | Menampilkan gambar QR Code resi di halaman lacak publik (baris 204–215). |
| **Simulasi Scan QR Kurir** | `resources/views/courier/tasks.blade.php` | Tab scan QR (baris 441–454) dan fungsi JS `switchProofMode()` / `simulateQrScanned()` (baris 796–824). |

---

## 4. Materi Middleware & Keamanan Route

| Komponen | Lokasi File | Fungsi Utama |
| :--- | :--- | :--- |
| **Registrasi Middleware** | `bootstrap/app.php` | Mendaftarkan alias middleware `'role' => RoleMiddleware::class` dan `'login.throttle' => LoginRateLimitMiddleware::class`. |
| **Custom Role Middleware** | `app/Http/Middleware/RoleMiddleware.php` | Mengecek `Auth::check()`, menormalisasi role (`kurir` → `courier`), dan melakukan `abort(403)` jika role tidak sesuai. |
| **Rate Limit Login** | `app/Http/Middleware/LoginRateLimitMiddleware.php` | Proteksi brute force pada route login. |
| **Definisi Route** | `routes/web.php` | Pengelompokan route publik, `auth` (PDF), `['auth', 'role:admin']` (Admin), `['auth', 'role:courier,admin']` (Kurir), dan `['auth', 'role:customer,admin']` (Customer). |

---

## 5. Materi Hosting & Deployment

| Komponen | Lokasi File | Fungsi Utama |
| :--- | :--- | :--- |
| **Environment** | `.env` & `.env.example` | Konfigurasi `APP_ENV`, `APP_DEBUG`, `APP_KEY`, `APP_URL`, `DB_CONNECTION`, `FILESYSTEM_DISK`. |
| **Front Controller** | `public/index.php` | Titik masuk utama seluruh request HTTP ke Laravel (document root saat hosting diarahkan ke folder `public/`). |
| **Apache Rewrite** | `public/.htaccess` | Aturan `mod_rewrite` untuk clean URL (tanpa `/index.php`) serta meneruskan header `Authorization` & `X-XSRF-Token`. |
| **Storage Symlink** | `public/storage` | Symlink ke `storage/app/public` (dibuat dengan `php artisan storage:link`) agar file upload (foto bukti, PDF) bisa diakses dari browser. |
| **Config Utama** | `config/app.php`, `config/database.php`, `config/filesystems.php`, `config/services.php` | Pengaturan aplikasi, koneksi database, disk penyimpanan, dan OAuth/layanan pihak ketiga. |
| **Build Assets & Setup** | `composer.json`, `package.json`, `vite.config.js` | Script `setup` (`composer install`, `key:generate`, `migrate --force`, `npm run build`). |
