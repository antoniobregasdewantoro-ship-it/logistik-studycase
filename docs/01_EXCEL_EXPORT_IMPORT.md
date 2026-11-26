# 📊 Kunci Jawaban Materi 1: Export & Import Excel (`maatwebsite/excel`)

---

## A. Peta File yang Berhubungan & Diubah di Project Ini
1. **Package:** `composer.json` (`"maatwebsite/excel": "^4.0"`)
2. **Export Data Utama:** `app/Exports/OrdersExport.php`
3. **Export Template Kosong:** `app/Exports/OrdersTemplateExport.php`
4. **Import Data:** `app/Imports/OrdersImport.php`
5. **Controller Excel:** `app/Http/Controllers/ExcelController.php`
6. **Route & Middleware:** `routes/web.php` *(catatan: pastikan route `ExcelController` sudah didaftarkan di dalam grup middleware `['auth', 'role:admin']`)*
7. **Tampilan Tombol & Modal Upload:** `resources/views/admin/orders.blade.php`

---

## B. Kemungkinan Soal Kuis & Kunci Jawaban

### 1. Kenapa butuh Export Excel padahal sudah ada tabel HTML dan Export PDF?
**Kunci Jawaban:**
- **Tabel HTML:** Hanya menampilkan data per halaman (*pagination*), tidak bisa dihitung ulang (`SUM`), difilter bebas, atau dibuat PivotTable, dan hilang saat tab ditutup.
- **PDF:** Cocok untuk dicetak dan diarsipkan (tampilan paten/statis), tidak bisa diolah datanya.
- **Excel (`.xlsx`):** Membuka seluruh data tanpa batas halaman, bisa difilter, di-sort, dihitung rumusnya (`SUM`, PivotTable, grafik), dan disimpan/dikirim ke staf.
- **Mekanisme `Excel::download()`:** Tidak menyimpan file `.xlsx` secara permanen di server; file langsung ditulis ke output buffer PHP dan dikirim sebagai stream respons ke browser.

---

### 2. Sebutkan Concern (Interface) pada `maatwebsite/excel` dan fungsinya!
**Kunci Jawaban:**
1. `FromCollection` → Mengambil seluruh data dari `collection()` (`Model::with(...)->get()`).
2. `FromQuery` → Mengambil data lewat query builder (lebih hemat RAM, bisa dikombinasikan dengan `WithChunkReading`).
3. `FromArray` → Data berasal dari array PHP statis (dipakai di `OrdersTemplateExport.php` untuk baris contoh).
4. `WithHeadings` → Menentukan baris judul kolom (`headings(): array`). Urutannya wajib sama dengan `map()`.
5. `WithMapping` → Memetakan 1 model menjadi 1 baris (`map($row): array`), membuka relasi, format tanggal, dan null-safety (`?? '-'`).
6. `WithColumnWidths` → Mengatur lebar kolom (`columnWidths(): array`).
7. `WithStyles` → Mengatur style sel (tebal/bold judul, warna fill, border, alignment) memakai `Worksheet $sheet`.
8. `ShouldQueue` → Menjalankan export di background (antrean `jobs`) agar request tidak timeout pada data sangat besar.
9. `WithTitle` → Memberi nama sheet/tab di Excel.

---

### 3. Soal Praktik: Mengubah/Menambah Kolom & Formatting pada Export
> *"Tambahkan kolom baru pada Export Excel, atur lebar kolomnya, dan tebalkan baris judul (header)!"*

**File yang diubah:** `app/Exports/OrdersExport.php`
1. Pastikan class meng-implements `WithColumnWidths` dan `WithStyles`.
2. Tambah judul kolom di `headings()`:
   ```php
   public function headings(): array
   {
       return ['No', 'No. Resi', ..., 'Kolom Baru'];
   }
   ```
3. Tambah datanya di urutan yang sama pada `map($order)`:
   ```php
   public function map($order): array
   {
       return [$no, $order->tracking_number, ..., $order->nilai_baru ?? '-'];
   }
   ```
4. Atur lebar kolom di `columnWidths()`:
   ```php
   public function columnWidths(): array
   {
       return ['A' => 5, 'B' => 16, ..., 'R' => 22];
   }
   ```
5. Tebalkan & beri warna header di `styles(Worksheet $sheet)`:
   ```php
   public function styles(Worksheet $sheet): array
   {
       return [
           1 => [
               'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
               'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0A1128']],
           ],
       ];
   }
   ```

---

### 4. Soal Teori: Bagaimana mengatasi Export Excel jika datanya ratusan ribu/juta baris (RAM habis)?
**Kunci Jawaban:**
`FromCollection` memanggil `->get()` sehingga seluruh data dimuat ke RAM sekaligus. Solusinya:
1. Ganti `FromCollection` menjadi `FromQuery` (mengembalikan query builder tanpa `->get()`).
2. Tambahkan `use WithChunkReading;` (membaca per batch, misal 500 baris lalu membebaskan memori).
3. Tambahkan `ShouldQueue` agar proses berjalan di background queue.

---

### 5. Soal Import Excel: Jelaskan fungsi `WithHeadingRow`, `ToModel`/`ToCollection`, `SkipsOnError`/`SkipsEmptyRows`, dan normalisasi data!
**Kunci Jawaban** (lihat `app/Imports/OrdersImport.php` & `app/Http/Controllers/ExcelController.php`):
- `WithHeadingRow` → Membaca baris judul sebagai key array asosiatif (misal `$row['nama_pengirim']`). Jika judul ada di baris ke-2, tambahkan `public function headingRow(): int { return 2; }`.
- `ToCollection` / `ToModel` → `ToModel` mengembalikan object Model baru per baris (`return null` untuk melewati baris); `ToCollection` memproses seluruh baris dalam loop `foreach ($rows as $row)`.
- `SkipsEmptyRows` / `SkipsOnError` → Melewati baris kosong atau baris yang error agar tidak menghentikan keseluruhan proses import, lalu menghitung `$importedCount` dan `$skippedCount` untuk ditampilkan sebagai ringkasan flash message.
- **Validasi Upload di Controller (`ExcelController::importOrders`):**
  ```php
  $request->validate([
      'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
  ]);
  ```
- **Normalisasi Data:** Nilai kosong/asing dinormalisasi sebelum masuk DB (contoh: `firstOrCreate` untuk relasi, fallback `'Reguler'` atau `'baik'`, `max(0, (int) $qty)`).

---

### 6. Soal Jebakan Route & Environment PHP untuk Excel
1. **Kenapa route `/barang/import` atau `/barang/template` harus ditulis SEBELUM `Route::resource('barang', ...)`?**
   - **Jawaban:** Agar tidak tertimpa wildcard `/barang/{barang}` milik `show()`.
2. **Bagaimana mengamankan route Export & Import?**
   - **Jawaban:** Masukkan ke dalam grup middleware `['auth', 'role:admin']` di `routes/web.php`, lalu buktikan dengan perintah:
     ```bash
     php artisan route:list -v
     ```
     (Flag `-v` menampilkan kolom **Middleware**).
3. **Error `Class "ZipArchive" not found` saat Export/Import Excel di Windows/Laragon/XAMPP:**
   - **Jawaban:** Aktifkan ekstensi zip di `php.ini` (`;extension=zip` → `extension=zip`), lalu restart Apache/Laragon. Cek lewat `php -m | findstr zip`.


---

## 🛠️ Soal Praktik Mengotak-atik Kodingan + Kunci Jawaban

### Latihan 1: Ubah Nama File Download & Urutan Data Export (Terlama Dulu)
> **Soal:** *"Ubah `OrdersExport` agar data yang di-export diurutkan dari yang paling **terlama** (`oldest()`), dan ubah format nama file download menjadi `Export_Order_DD-MM-YYYY.xlsx`!"*

**File yang diubah:**
1. `app/Exports/OrdersExport.php` (baris `collection()`):
   ```php
   // SEBELUM:
   return Order::with(['customer', 'courier'])->latest()->get();

   // SESUDAH (Kunci Jawaban):
   return Order::with(['customer', 'courier'])->oldest()->get();
   ```
2. `app/Http/Controllers/ExcelController.php` (method `exportOrders()`):
   ```php
   // SEBELUM:
   $fileName = 'Meridian_Manifest_Pesanan_'.now()->format('Ymd_His').'.xlsx';

   // SESUDAH (Kunci Jawaban):
   $fileName = 'Export_Order_'.now()->format('d-m-Y').'.xlsx';
   ```

---

### Latihan 2: Ubah Warna Header Excel & Tambah Kolom `Kode Pos`
> **Soal:** *"Tambahkan kolom `Kode Pos` setelah kolom `Tujuan` pada `OrdersExport.php`, sesuaikan lebar kolomnya, dan ubah warna background header dari biru tua menjadi hijau tua (`#166534`)!"*

**File yang diubah:** `app/Exports/OrdersExport.php`
1. Tambah `'Kode Pos'` di `headings()` setelah `'Tujuan'`.
2. Tambah `$order->postal_code ?? '-'` di `map($order)` pada posisi yang sama (setelah `$order->destination`).
3. Geser huruf kolom di `columnWidths()` (tambahkan `'I' => 12` untuk Kode Pos, kolom berikutnya geser ke `'J'`..`'R'`).
4. Di `styles(Worksheet $sheet)`:
   - Ubah `$lastCol = 'Q';` menjadi `$lastCol = 'R';`
   - Ubah `'startColor' => ['argb' => 'FF0A1128']` menjadi `'startColor' => ['argb' => 'FF166534']`.

---

### Latihan 3: Ubah `OrdersImport` Agar Berat Kosong/Negatif Otomatis Jadi `1.0 Kg` (Bukan Error)
> **Soal:** *"Modifikasi `OrdersImport.php` agar jika kolom `berat_kg` di Excel kosong atau kurang dari `0.1`, baris tidak gagal validasi melainkan otomatis dinormalisasi menjadi `1.0` Kg!"*

**File yang diubah:** `app/Imports/OrdersImport.php`
1. Di `rules()`, ubah validasi `'berat_kg'`:
   ```php
   'berat_kg' => 'nullable|numeric',
   ```
2. Di `collection()`, normalisasi nilai `$weight`:
   ```php
   $rawWeight = (float) ($row['berat_kg'] ?? 0);
   $weight = $rawWeight >= 0.1 ? $rawWeight : 1.0;
   ```
