# 🚀 Kunci Jawaban Materi 4: Hosting & Deployment Laravel ke **Hostinger**

---

## A. Peta File Project yang Berhubungan & Diubah Saat Hosting
1. **Environment:** `.env` (dibuat dari `.env.example`)
2. **Entry Point Web:** `public/index.php` *(diubah path-nya jika pakai Jalur A Upload Manual)*
3. **Rewrite Apache:** `public/.htaccess`
4. **Service Provider (untuk `public_html` atau paksa HTTPS):** `app/Providers/AppServiceProvider.php`
5. **Routes (untuk trik storage link jika tanpa SSH):** `routes/web.php`
6. **Build Frontend & Dependencies:** `composer.json`, `package.json`, `vite.config.js`, folder `public/build/`
7. **Storage Symlink:** `public/storage` → `storage/app/public`

---

## B. Persiapan Wajib di Komputer Lokal Sebelum Upload ke Hostinger
Jalankan perintah ini di terminal lokal (`c:\laragon\www\logistic_studycase`):
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```
- **Cek versi PHP Hostinger:** Laravel 11 / 12 / 13 wajib **PHP 8.2+** (project ini pakai PHP 8.3). Atur di **hPanel → Advanced → PHP Configuration**.

---

## C. Dua Jalur Deployment di Hostinger (hPanel)

### Jalur A: Upload Manual Lewat File Manager (Tanpa Git / Tanpa SSH)
1. **Pisahkan menjadi 2 file ZIP (Prinsip Keamanan: core Laravel tidak boleh di folder publik!):**
   - **`public_assets.zip`** → Isi folder `public/` saja (termasuk file tersembunyi `.htaccess` dan folder `build/`).
   - **`core_laravel.zip`** → Semua folder & file sisanya (`app/`, `bootstrap/`, `config/`, `database/`, `resources/`, `routes/`, `storage/`, `vendor/`, `.env`, `artisan`, `composer.json`), **TANPA** `public/`, `node_modules/`, dan `.git/`.
2. **Mapping Folder di Hostinger File Manager (`/home/u12345/domains/domainanda.com/`):**
   ```text
   /home/u12345/domains/domainanda.com/
   ├── logistic_app/     <-- Extract core_laravel.zip di sini (di luar public_html)
   └── public_html/      <-- Extract public_assets.zip di sini (aktif Show Hidden Files agar .htaccess terlihat!)
   ```
3. **File yang WAJIB diubah #1: `public_html/index.php`**
   Arahkan path `autoload.php` dan `bootstrap/app.php` ke folder `logistic_app`:
   ```php
   // Sebelum:
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';

   // Sesudah (sesuaikan nama folder core):
   require __DIR__.'/../logistic_app/vendor/autoload.php';
   $app = require_once __DIR__.'/../logistic_app/bootstrap/app.php';
   ```
4. **File yang WAJIB diubah #2: `logistic_app/app/Providers/AppServiceProvider.php`** (jika pakai storage link / upload ke `public_html`):
   ```php
   public function register(): void
   {
       $this->app->usePublicPath(base_path('../public_html'));
   }
   ```
5. **Migrasi Database MySQL:**
   - Lokal: Export database dari `localhost/phpmyadmin` menjadi file `.sql`.
   - Hostinger hPanel: Menu **Databases → Management** → Buat Database & User baru (ingat, Hostinger otomatis menambah prefix seperti `u12345_`).
   - Buka **phpMyAdmin** di Hostinger → pilih database baru → **Import** file `.sql`.
6. **File yang WAJIB diubah #3: `logistic_app/.env` untuk Production**
   ```env
   APP_NAME="Meridian Logistics"
   APP_ENV=production
   APP_KEY=base64:...(salin dari lokal / generate)
   APP_DEBUG=false
   APP_URL=https://domainanda.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u12345_meridian
   DB_USERNAME=u12345_admin
   DB_PASSWORD="password_kuat_anda"
   ```
7. **Storage Link Tanpa SSH (jika fungsi `symlink()` aktif):**
   Tambah route sementara di `routes/web.php`:
   ```php
   Route::get('/storagelink', function () {
       \Illuminate\Support\Facades\Artisan::call('storage:link');
       return 'Storage Linked Sukses!';
   });
   ```
   Akses `https://domainanda.com/storagelink`, lalu **WAJIB HAPUS** route tersebut demi keamanan.

---

### Jalur B: Deploy dari GitHub + SSH di Hostinger (Cara Terbaik & Paling Direkomendasikan)
*Hostinger hPanel memiliki menu **Advanced → SSH Access** dan **Advanced → Git**.*

1. **Persiapan Repo GitHub (di lokal):**
   Pastikan `.gitignore` memuat `.env`, `/vendor`, dan `/node_modules`.
   *(Catatan: `public/build` secara default ada di `.gitignore`. Jika server Hostinger tidak punya Node.js, hapus `public/build` dari `.gitignore` lalu commit hasil `npm run build`, atau jalankan `npm run build` di server jika Node tersedia).*
2. **Perintah di Terminal SSH Hostinger:**
   ```bash
   cd ~/domains/domainanda.com
   mv public_html public_html.bak
   git clone https://github.com/username/repo-anda.git app
   ln -s app/public public_html

   cd app
   composer2 install --no-dev --optimize-autoloader
   cp .env.example .env
   nano .env                  # isi APP_ENV=production, APP_DEBUG=false, APP_URL, DB_*
   php artisan key:generate
   php artisan migrate --force
   ln -s ../storage/app/public public/storage
   php artisan optimize
   ```
   > **Perhatian Khusus Hostinger:**
   > 1. Gunakan `composer2` (bukan `composer`, karena `composer` default sering masih versi 1).
   > 2. Fungsi PHP `symlink()` di shared hosting sering dinonaktifkan, jadi `php artisan storage:link` bisa gagal — gantikan langsung dengan perintah Linux `ln -s ../storage/app/public public/storage`.
   > 3. `php artisan migrate --force` wajib pakai `--force` karena `APP_ENV=production`.
3. **Update Kode Berikutnya (CI/CD manual via SSH):**
   ```bash
   php artisan down
   git pull origin main
   composer2 install --no-dev
   php artisan migrate --force
   php artisan optimize:clear
   php artisan up
   ```

---

## D. Kemungkinan Soal Kuis Hosting & Kunci Jawaban Singkat

1. **Kenapa `APP_DEBUG` wajib diubah ke `false` saat live?**
   - Karena jika `true`, setiap error akan membocorkan stack trace, struktur folder, query SQL, serta isi credential `.env` (password database, API key) ke browser pengguna.
2. **Kenapa file `.env` dan folder `app/`, `config/` tidak boleh ditaruh di dalam `public_html/`?**
   - Karena isi `public_html/` bisa diakses langsung lewat URL browser (`domain.com/.env`). Menaruh core Laravel satu level di luar `public_html/` melindungi seluruh kode sumber dan kredensial.
3. **Apa penyebab Error 500 setelah upload ke Hostinger dan cara ceknya?**
   - **4 Penyebab Utama:** (1) Versi PHP kurang dari 8.2, (2) File `.htaccess` hilang atau salah sintaks, (3) Kredensial database di `.env` salah/lupa prefix `u12345_`, (4) Permission folder salah (`0777` langsung diblokir server dan menyebabkan Error 500!).
   - **Golden Rule Permission:** Folder = **`0755`**, File = **`0644`**.
   - **Cara Cek:** Baca file `storage/logs/laravel.log`.
4. **Kenapa halaman `/` bisa dibuka, tapi `/login` atau `/admin/orders` muncul Error 404?**
   - File `public/.htaccess` belum ikut ter-upload (karena hidden file), `mod_rewrite` belum aktif, atau `public_html` tidak mengarah ke folder `public/`.
5. **Kenapa CSS/JS Vite gagal dimuat atau terkena Mixed Content setelah aktifkan HTTPS/SSL?**
   - Pastikan `APP_URL=https://...`, jalankan `npm run build`, dan jika perlu tambahkan `URL::forceScheme('https');` di `AppServiceProvider::boot()` saat `$this->app->environment('production')`.
6. **Apa fungsi Cronjob `* * * * * /usr/bin/php /home/user/app/artisan schedule:run >> /dev/null 2>&1` di hPanel?**
   - Menjalankan Laravel Scheduler setiap menit agar tugas terjadwal (atau queue/notifikasi otomatis) berjalan di server.


---

## 🛠️ Soal Praktik Mengotak-atik Kodingan + Kunci Jawaban (Khusus Hostinger)

### Latihan 1: Menyesuaikan `public_html/index.php` Saat Folder Core Bernama `meridian_core`
> **Soal:** *"Anda meng-upload core Laravel ke `/home/u998877/domains/meridian.id/meridian_core/` dan isi folder `public/` ke `public_html/`. Ubah isi `public_html/index.php` agar website tidak Error 500!"*

**File yang diubah:** `public_html/index.php`
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Cek maintenance mode
if (file_exists($maintenance = __DIR__.'/../meridian_core/storage/framework/maintenance.php')) {
    require $maintenance;
}

// 2. Autoloader Composer
require __DIR__.'/../meridian_core/vendor/autoload.php';

// 3. Bootstrap Laravel
/** @var Application $app */
$app = require_once __DIR__.'/../meridian_core/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

---

### Latihan 2: Mengatasi Gambar & CSS Mixed Content (`http://` vs `https://`) di Hostinger
> **Soal:** *"Setelah domain Hostinger memakai SSL (`https://`), aset CSS/JS dan URL gambar `asset('storage/...')` masih keluar sebagai `http://` sehingga diblokir browser. Ubah `AppServiceProvider.php` untuk memaksa HTTPS hanya di environment production!"*

**File yang diubah:** `app/Providers/AppServiceProvider.php`
```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
```

---

### Latihan 3: Mengubah `.env` Lokal Menjadi `.env` Production Hostinger
> **Soal:** *"Diberikan data Hostinger: Domain `https://meridianlog.my.id`, DB Name `u4512_db`, DB User `u4512_user`, Password `Rahasia#123`. Perbaiki `.env` lokal berikut agar aman untuk live!"*

**File yang diubah:** `.env`
```env
APP_NAME="Meridian Logistics"
APP_ENV=production
APP_KEY=base64:...(tetap terisi)
APP_DEBUG=false
APP_URL=https://meridianlog.my.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u4512_db
DB_USERNAME=u4512_user
DB_PASSWORD="Rahasia#123"
```
*(Catatan: Password diberi tanda kutip ganda `"..."` supaya karakter spesial seperti `#` tidak dianggap komentar di `.env`).*
