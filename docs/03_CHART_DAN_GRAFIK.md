# 📈 Kunci Jawaban Materi 3: Chart & Grafik (`Chart.js` + `Service`)

---

## A. Peta File yang Berhubungan & Diubah di Project Ini
1. **Service Agregasi:** `app/Services/StatistikService.php`
   - `transaksiPerBulan(int $bulan = 6)` → data grafik garis 3/6/12 bulan terakhir.
   - `statusTransaksi()` → data grafik doughnut komposisi status (`Terkirim`, `Dalam Perjalanan`, `Verifikasi`).
   - `jumlahBarangPerKategori(int $limit = 6)` → agregasi per layanan kargo.
   - `topBarangDipinjam(int $limit = 5)` → top rute pengiriman.
2. **Controller (Method Injection & Whitelist Filter):** `app/Http/Controllers/AdminController.php` (method `dashboard(Request $request, StatistikService $statistikService)`, baris 92–135).
3. **View Blade (`<canvas>`, Filter 3/6/12 Bulan, Payload JSON & Chart.js):** `resources/views/admin/dashboard.blade.php` (baris 31–125, 409–410, 481–658).

---

## B. Kemungkinan Soal Kuis & Kunci Jawaban

### 1. Prinsip Arsitektur: Mengapa agregasi grafik (`SUM`, `COUNT`, `groupBy`) dilakukan di Server (Service/MySQL), bukan di JavaScript browser?
**Kunci Jawaban:**
1. Jika ribuan baris mentah dikirim ke browser untuk dihitung di JS, ukuran payload membengkak dan halaman menjadi lambat.
2. MySQL jauh lebih cepat melakukan `DB::raw('COUNT(*) as total')`, `DB::raw('SUM(...)')`, dan `groupBy()`.
3. Memisahkan data (`StatistikService`) dari tampilan (`Chart.js`): mengganti tipe grafik (`line` → `bar`) tidak perlu menyentuh query, dan mengubah query tidak perlu menyentuh JS.

---

### 2. Jelaskan 5 langkah/lapisan alur data dari Database sampai Grafik tampil!
**Kunci Jawaban:**
1. **Database** (tabel `orders`, `users`, dll.)
2. **Service (`StatistikService`)** → Query agregasi `DB::raw()` + `groupBy()` + `pluck()` mengembalikan array `['labels' => ..., 'data' => ..., 'colors' => ...]`.
3. **Controller** → Memanggil service via *method injection* (`public function index(StatistikService $statistik)`) lalu mengirim ke view via `compact()`.
4. **Blade (`@json()`)** → Mengubah array PHP menjadi JSON aman untuk JavaScript (`const tren = @json($tren);`).
5. **Chart.js (`renderChart()`)** → Menggambar grafik ke elemen `<canvas id="...">`.

---

### 3. Kenapa di `charts.js` wajib memanggil `chart.destroy()` sebelum membuat `new Chart(...)` baru?
**Kunci Jawaban:**
Menggunakan `const instances = new Map();` dan mengecek `if (instances.has(id)) { instances.get(id).destroy(); }` bertujuan mencegah error **"Canvas is already in use"** saat grafik di-render ulang / halaman di-filter.

---

### 4. Bagaimana cara memfilter periode grafik (misal 3 / 6 / 12 bulan) dengan aman?
**Kunci Jawaban:**
Gunakan query string (`?bulan=6`) dan wajib lakukan **whitelist validation** dengan `in_array()` di Controller agar parameter tidak diisi angka sembarangan:
```php
$jumlahBulan = (int) request('bulan', 6);
$jumlahBulan = in_array($jumlahBulan, [3, 6, 12], true) ? $jumlahBulan : 6;
```

---

### 5. Perbedaan tipe grafik Chart.js yang sering ditanyakan:
- **`line`** → Cocok untuk **tren waktu** (misal pesanan/transaksi 6 bulan terakhir), bisa pakai `tension: 0.4` dan `fill: true` dengan `chartGradient()`.
- **`bar`** → Cocok untuk **perbandingan antar kategori** (vertikal, atau horizontal dengan `indexAxis: 'y'`).
- **`doughnut` / `pie`** → Cocok untuk **komposisi/proporsi** (misal status `Verifikasi`, `Dalam Perjalanan`, `Terkirim`). Catatan: **`doughnut` dan `polarArea` tidak memiliki sumbu `scales` (x/y)**, sehingga di `chartOptions()` properti `scales` harus dihapus jika tidak dipakai.
- **`polarArea`** → Cocok untuk membandingkan kondisi/kategori dengan area radial.

---

### 6. Contoh Kode Siap Pakai Jika Disuruh Menambah Grafik Status Order di `AdminController`
1. **Query di Controller / Service:**
   ```php
   $statusData = \App\Models\Order::select('status', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
       ->groupBy('status')
       ->pluck('total', 'status');

   $chartStatus = [
       'labels' => ['Verifikasi', 'Dalam Perjalanan', 'Terkirim'],
       'data'   => [
           (int) $statusData->get('Verifikasi', 0),
           (int) $statusData->get('Dalam Perjalanan', 0),
           (int) $statusData->get('Terkirim', 0),
       ],
       'colors' => ['#f59e0b', '#3b82f6', '#10b981'],
   ];
   ```
2. **Tampilkan di Blade:**
   ```html
   <canvas id="chartStatusOrder"></canvas>
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script>
       const statusChart = @json($chartStatus);
       new Chart(document.getElementById('chartStatusOrder'), {
           type: 'doughnut',
           data: {
               labels: statusChart.labels,
               datasets: [{ data: statusChart.data, backgroundColor: statusChart.colors, borderWidth: 0 }]
           }
       });
   </script>
   ```


---

## 🛠️ Soal Praktik Mengotak-atik Kodingan + Kunci Jawaban

### Latihan 1: Tambah Pilihan Filter `24 Bulan` pada Grafik Tren Pengiriman
> **Soal:** *"Saat ini filter periode hanya menerima `3`, `6`, dan `12` bulan. Tambahkan opsi `24` bulan pada validasi backend dan tombol UI-nya!"*

**File yang diubah:**
1. `app/Http/Controllers/AdminController.php` (baris 113–114):
   ```php
   // SEBELUM:
   $jumlahBulan = in_array($jumlahBulan, [3, 6, 12], true) ? $jumlahBulan : 6;

   // SESUDAH (Kunci Jawaban):
   $jumlahBulan = in_array($jumlahBulan, [3, 6, 12, 24], true) ? $jumlahBulan : 6;
   ```
2. `resources/views/admin/dashboard.blade.php` (sekitar baris 52–63, di sebelah tombol `12 Bulan`):
   ```blade
   <a href="{{ route('admin.dashboard', array_merge(request()->query(), ['bulan' => 24])) }}"
      class="px-3 py-1.5 rounded-lg text-xs font-bold {{ $jumlahBulan === 24 ? 'bg-[#FF6B00] text-white' : 'text-slate-600' }}">
       24 Bulan
   </a>
   ```

---

### Latihan 2: Ubah Warna & Tipe Grafik `Status Pengiriman` dari `doughnut` Menjadi `bar` Horizontal
> **Soal:** *"Ubah grafik Status Pengiriman di `dashboard.blade.php` dari tipe `doughnut` menjadi `bar` horizontal, dan ubah warna status `Verifikasi` di `StatistikService.php` menjadi merah (`#ef4444`)!"*

**File yang diubah:**
1. `app/Services/StatistikService.php` (di dalam `statusTransaksi()`):
   Ubah warna untuk `Verifikasi` pada array `'colors'` menjadi `'#ef4444'`.
2. `resources/views/admin/dashboard.blade.php` (di konfigurasi `new Chart(ctxStatus, { ... })`):
   ```javascript
   type: 'bar', // sebelumnya 'doughnut'
   options: {
       indexAxis: 'y', // membuat bar menjadi horizontal
       responsive: true,
       maintainAspectRatio: false,
       // hapus cutout: '...' karena khusus doughnut
   }
   ```

---

### Latihan 3: Tambah Dataset Baru atau Ubah Limit Top Rute dari `5` ke `10`
> **Soal:** *"Ubah jumlah top rute yang diambil di `AdminController::dashboard()` dari `5` menjadi `10` dan jumlah kategori layanan dari `4` menjadi `6`!"*

**File yang diubah:** `app/Http/Controllers/AdminController.php` (baris 118–119):
```php
// SEBELUM:
$layananKargo = $statistikService->jumlahBarangPerKategori(4);
$topRute = $statistikService->topBarangDipinjam(5);

// SESUDAH (Kunci Jawaban):
$layananKargo = $statistikService->jumlahBarangPerKategori(6);
$topRute = $statistikService->topBarangDipinjam(10);
```
