# 🏷️ Kunci Jawaban Materi 2: Barcode (1D) & QR Code (2D)

---

## A. Peta File yang Berhubungan & Diubah di Project Ini
1. **Model Order (Accessor QR saat ini):** `app/Models/Order.php` (baris 147–152: `getQrCodeUrlAttribute()`)
2. **Migrasi Kolom QR:** `database/migrations/2026_10_19_083631_create_orders_table.php` (baris 45: `qr_code_path`)
3. **Halaman Kelola Orders Admin (Modal & Print QR):** `resources/views/admin/orders.blade.php` (baris 96–99, 633–665, 723–740)
4. **Halaman Tracking Publik:** `resources/views/pages/tracking.blade.php` (baris 204–215)
5. **Halaman Kurir (Tab Simulasi Scan QR):** `resources/views/courier/tasks.blade.php` (baris 441–454, 796–824)

---

## B. Kemungkinan Soal Kuis & Kunci Jawaban

### 1. Apa perbedaan Barcode 1D (kotak garis panjang) dan QR Code 2D?
**Kunci Jawaban:**
| Karakteristik | Barcode (1D) | QR Code (2D) |
| :--- | :--- | :--- |
| **Bentuk Visual** | Garis vertikal panjang (`\|\|\|\|\|\|`) | Kotak bujur sangkar |
| **Engine di Milon Barcode** | `DNS1D` | `DNS2D` |
| **Kode Format** | `'C39'` (Code 39) / `'C128'` (Code 128) | `'QRCODE'` |
| **Dimensi Ideal** | `(3, 100)` atau `(2, 50)` — lebar tipis, tinggi memanjang | `(5, 5)` — simetris bujur sangkar |
| **Dampak Data Panjang** | Melebar secara horizontal (bisa merusak lebar tabel!) | Pola semakin padat & kompleks |
| **Kelebihan Milon HTML** | `DNS1D::getBarcodeHTML()` menghasilkan `<div>` HTML murni, **tidak butuh `ext-gd`** | `DNS2D` / PNG butuh **`ext-gd`** aktif di `php.ini` |

---

### 2. Soal Praktik Paling Sering Keluar: Mengubah QR Code (Kotak) menjadi Barcode Panjang (1D), atau Sebaliknya!

#### Cara A — Pakai Package `milon/barcode` (Sesuai Materi Kuliah)
1. **Install package:**
   ```bash
   composer require milon/barcode
   # Jika ext-gd belum aktif saat install:
   composer require milon/barcode --ignore-platform-reqs
   ```
   *(ServiceProvider & alias `DNS1D`, `DNS2D` otomatis terdaftar di `bootstrap/cache/packages.php`).*

2. **File yang diubah #1: `app/Models/Order.php`**
   Tambahkan accessor kode & method `barcodeHtml()`:
   ```php
   // Accessor: format kode yang konsisten (misal tetap pakai tracking_number atau INV-00001)
   public function getKodeBarangAttribute(): string
   {
       return 'INV-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
   }

   // Method untuk Barcode 1D Panjang (Code 39 / Code 128)
   public function barcodeHtml(int $width = 3, int $height = 100): string
   {
       return \DNS1D::getBarcodeHTML($this->tracking_number, 'C39', $width, $height);
   }

   // Method jika diminta ubah ke QR Code (2D)
   public function qrHtml(int $width = 5, int $height = 5): string
   {
       return \DNS2D::getBarcodeHTML($this->tracking_number, 'QRCODE', $width, $height);
   }
   ```

3. **File yang diubah #2: Blade View (`resources/views/admin/orders.blade.php` atau `resources/views/pages/tracking.blade.php`)**
   Ganti tag `<img>` QR lama menjadi pemanggilan Blade unescaped:
   ```blade
   {{-- SALAH (akan tampil teks <div> mentah): --}}
   {{ $order->barcodeHtml(2, 50) }}

   {{-- BENAR (wajib pakai {!! !!} agar dirender sebagai HTML): --}}
   {!! $order->barcodeHtml(2, 50) !!}
   ```

#### Cara B — Tanpa Install Package Baru (Ubah URL API di `app/Models/Order.php`)
Jika kuis meminta cepat tanpa composer, cukup edit `app/Models/Order.php` baris 147–152:
```php
public function getQrCodeUrlAttribute(): string
{
    $data = urlencode($this->tracking_number);

    // Ganti dari qrserver (kotak) ke bwipjs-api (barcode garis panjang Code 128)
    return "https://bwipjs-api.metafloor.com/?bcid=code128&text={$data}&scale=2&height=10&includetext";
}
```
*(Dengan cara ini, semua view di `orders.blade.php` dan `tracking.blade.php` otomatis berubah jadi barcode panjang tanpa mengedit view).*

---

### 3. Soal Jebakan Blade: Mengapa muncul tulisan `<div style="font-size:0...">` di layar saat menampilkan Barcode?
**Kunci Jawaban:**
Karena memakai kurung kurawal ganda `{{ ... }}` yang otomatis melakukan **HTML escaping** (`htmlspecialchars`). Untuk merender output HTML murni dari `DNS1D::getBarcodeHTML()`, wajib menggunakan **unescaped syntax**:
```blade
{!! $order->barcodeHtml(3, 100) !!}
```

---

### 4. Soal Parameter & Layout:
1. **Jelaskan arti 4 parameter pada `DNS1D::getBarcodeHTML($kode, 'C39', 3, 100)`!**
   - Parameter 1 (`$kode`): Payload / data teks yang akan di-encode (contoh: `$order->tracking_number` atau `INV-00001`).
   - Parameter 2 (`'C39'`): Standar format encoding barcode (Code 39, hanya mendukung `A–Z`, `0–9`, dan beberapa simbol seperti `-`).
   - Parameter 3 (`3`): `width` — ketebalan tiap garis vertikal pembentuk pola.
   - Parameter 4 (`100`): `height` — panjang/tinggi garis vertikal dari atas ke bawah (dalam px).
2. **Kenapa barcode ukuran besar `(3, 100)` sebaiknya ditaruh di halaman Detail/Modal, bukan di tabel list?**
   - Karena lebarnya sekitar `528 px` sehingga bisa membuat kolom tabel melebar dan merusak layout. Di tabel list cukup pakai ukuran kecil seperti `(2, 40)` atau icon tombol modal.
3. **Kenapa menggunakan `str_pad((string) $this->id, 5, '0', STR_PAD_LEFT)`?**
   - Mengubah ID angka (`1`, `42`) menjadi panjang karakter tetap (`INV-00001`, `INV-00042`) tanpa menambah kolom/migrasi baru di database, sehingga lebar barcode konsisten.


---

## 🛠️ Soal Praktik Mengotak-atik Kodingan + Kunci Jawaban

### Latihan 1: Ganti Ukuran & Format Barcode Menjadi Code 128 (`C128`) yang Lebih Rapat
> **Soal:** *"Jika menggunakan `milon/barcode`, ubah format barcode dari `'C39'` menjadi `'C128'` dengan lebar garis `2` dan tinggi `60` agar muat di tabel!"*

**File yang diubah:** `app/Models/Order.php`
```php
public function barcodeHtml(int $width = 2, int $height = 60): string
{
    return \DNS1D::getBarcodeHTML($this->tracking_number, 'C128', $width, $height);
}
```
Dan di Blade (`resources/views/admin/orders.blade.php` atau `tracking.blade.php`):
```blade
{!! $order->barcodeHtml(2, 60) !!}
```

---

### Latihan 2: Ubah Payload QR Code dari Link URL Lengkap Menjadi Nomor Resi Saja
> **Soal:** *"Saat ini `Order::getQrCodeUrlAttribute()` menghasilkan QR berisi URL `http://.../?tracking=ORD-12345`. Ubah agar QR Code hanya menyimpan nomor resinya saja (`ORD-12345`) dan ukurannya diperbesar menjadi `300x300`!"*

**File yang diubah:** `app/Models/Order.php` (baris 147–152)
```php
// SEBELUM:
public function getQrCodeUrlAttribute(): string
{
    $data = urlencode(url('/?tracking='.$this->tracking_number));

    return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$data}";
}

// SESUDAH (Kunci Jawaban):
public function getQrCodeUrlAttribute(): string
{
    $data = urlencode($this->tracking_number);

    return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$data}";
}
```

---

### Latihan 3: Tukar QR Kotak Menjadi Barcode Panjang Hanya di Halaman Tracking Publik (`tracking.blade.php`)
> **Soal:** *"Biarkan QR Code di halaman Admin tetap kotak, tapi ubah tampilan label digital di halaman Tracking Publik (`resources/views/pages/tracking.blade.php`) menjadi Barcode Batang panjang menggunakan `JsBarcode`!"*

**File yang diubah:** `resources/views/pages/tracking.blade.php` (sekitar baris 204–215)
```blade
{{-- Ganti tag <img src="{{ $trackedOrder->qr_code_url }}"> menjadi: --}}
<svg id="trackingBarcode"></svg>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    JsBarcode("#trackingBarcode", "{{ $trackedOrder->tracking_number }}", {
        format: "CODE128",
        width: 1.5,
        height: 45,
        displayValue: true,
        fontSize: 12
    });
</script>
```
