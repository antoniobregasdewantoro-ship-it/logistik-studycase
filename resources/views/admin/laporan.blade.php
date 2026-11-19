@extends('layouts.admin')

@section('title', 'Laporan Pengiriman & Rekapitulasi PDF')
@section('header_title', 'Laporan Pengiriman')

@section('content')
<div class="space-y-6">

    <!-- HEADER & EXPORT ACTION BUTTONS -->
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-6 border border-slate-200/80 dark:border-white/5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[11px] font-black tracking-widest uppercase text-[#FF6B00]">DOKUMEN OPERASIONAL & KEUANGAN</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-[11px] font-bold text-emerald-500">Data Real-Time</span>
            </div>
            <h1 class="text-2xl font-black text-[#0A1128] dark:text-white tracking-tight">
                Laporan Pengiriman & Rekapitulasi Ekspedisi
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Rekapitulasi lengkap data manifest operasional, rincian omzet, performa armada kurir, serta cetak dokumen resmi berformat PDF A4.
            </p>
        </div>

        <!-- Tombol Aksi Import & Export Laporan -->
        <div class="flex items-center gap-3">
            <button type="button" onclick="openImportModalLaporan()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black shadow-lg shadow-teal-600/25 transition">
                <i class="fa-solid fa-file-import text-sm"></i>
                <span>Import Excel</span>
            </button>

            <div class="relative" id="exportDropdownContainer">
            <button type="button" onclick="toggleExportDropdown(event)" id="exportDropdownBtn"
                    class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white text-xs font-black shadow-lg shadow-[#FF6B00]/25 transition">
                <i class="fa-solid fa-file-export text-sm"></i>
                <span>Export Laporan</span>
                <i class="fa-solid fa-chevron-down text-[10px] ml-1 transition-transform duration-200" id="exportDropdownArrow"></i>
            </button>

            <!-- Dropdown Menu Pilihan Format Dokumen -->
            <div id="exportDropdownMenu" class="absolute right-0 mt-2 w-64 bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl border border-slate-200 dark:border-white/10 p-2 z-50 hidden transition-all duration-150">
                <div class="px-3 py-2 border-b border-slate-100 dark:border-white/5 mb-1">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">PILIH FORMAT DOKUMEN</span>
                </div>

                <!-- 1. Export Excel Barang / Manifest -->
                <a href="{{ route('laporan.barang.excel') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-600 transition group">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-file-excel text-sm"></i>
                    </div>
                    <div>
                        <span class="block">Barang Excel (.xlsx)</span>
                        <span class="text-[10px] font-normal text-slate-400">Data Barang & Manifest Resmi</span>
                    </div>
                </a>

                <!-- 2. Export Excel Transaksi -->
                <a href="{{ route('laporan.transaksi.excel') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-600 transition group">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-file-excel text-sm"></i>
                    </div>
                    <div>
                        <span class="block">Transaksi Excel (.xlsx)</span>
                        <span class="text-[10px] font-normal text-slate-400">Rekap Omzet & Keuangan</span>
                    </div>
                </a>

                <!-- 2. Export Manifest PDF -->
                <a href="{{ route('laporan.manifest.pdf') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-600 transition group">
                    <div class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-file-pdf text-sm"></i>
                    </div>
                    <div>
                        <span class="block">Export Manifest PDF</span>
                        <span class="text-[10px] font-normal text-slate-400">Dokumen Berita Acara A4</span>
                    </div>
                </a>

                <!-- 3. Export Transaksi PDF -->
                <a href="{{ route('laporan.transaksi.pdf') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition group">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-[#FF6B00] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                    </div>
                    <div>
                        <span class="block">Export Transaksi PDF</span>
                        <span class="text-[10px] font-normal text-slate-400">Rekapitulasi Omzet & Keuangan</span>
                    </div>
                </a>
            </div>
        </div>
        </div>
    </div>

    <!-- 4 BENTO SUMMARY STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Pendapatan -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Omzet Transaksi</p>
                <h3 class="text-xl font-black text-[#0A1128] dark:text-white mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mt-1 inline-block">
                    <i class="fa-solid fa-chart-line mr-0.5"></i> Penerimaan Resmi
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>

        <!-- 2. Total Manifest / Pesanan -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Manifest Terbit</p>
                <h3 class="text-xl font-black text-[#0A1128] dark:text-white mt-1">{{ number_format($totalOrders, 0, ',', '.') }} Manifest</h3>
                <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 mt-1 inline-block">
                    <i class="fa-solid fa-boxes-stacked mr-0.5"></i> Keseluruhan Siklus
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
        </div>

        <!-- 3. Paket Terkirim -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Pengiriman Sukses</p>
                <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($deliveredOrders, 0, ',', '.') }} Terkirim</h3>
                <span class="text-[10px] font-bold text-slate-400 mt-1 inline-block">
                    <i class="fa-solid fa-circle-check mr-0.5 text-emerald-500"></i> Bukti Serah Terima
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
        </div>

        <!-- 4. Total Berat Kargo -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Muatan Logistik</p>
                <h3 class="text-xl font-black text-[#FF6B00] mt-1">{{ number_format($totalWeight, 1, ',', '.') }} Kg</h3>
                <span class="text-[10px] font-bold text-slate-400 mt-1 inline-block">
                    <i class="fa-solid fa-weight-hanging mr-0.5 text-[#FF6B00]"></i> Tonase Armada
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-[#FF6B00] flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH FORM -->
    <div class="bg-white dark:bg-[#0D1527] rounded-2xl p-4 border border-slate-200/80 dark:border-white/5 shadow-sm">
        <form method="GET" action="{{ route('admin.laporan') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <!-- Search Keyword -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Resi, Pengirim, Kota..."
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:outline-none focus:border-[#FF6B00]">
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-white/10 dark:bg-[#0D1527] dark:text-white focus:outline-none focus:border-[#FF6B00]">
                    <option value="">-- Semua Status --</option>
                    <option value="Verifikasi" {{ request('status') === 'Verifikasi' ? 'selected' : '' }}>Verifikasi Manifest</option>
                    <option value="Dalam Perjalanan" {{ request('status') === 'Dalam Perjalanan' ? 'selected' : '' }}>Dalam Perjalanan</option>
                    <option value="Terkirim" {{ request('status') === 'Terkirim' ? 'selected' : '' }}>Terkirim (Selesai)</option>
                </select>
            </div>

            <!-- Service Filter -->
            <div>
                <select name="service" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-white/10 dark:bg-[#0D1527] dark:text-white focus:outline-none focus:border-[#FF6B00]">
                    <option value="">-- Semua Layanan --</option>
                    <option value="Reguler" {{ request('service') === 'Reguler' ? 'selected' : '' }}>Reguler (Rp 9.000/kg)</option>
                    <option value="Express" {{ request('service') === 'Express' ? 'selected' : '' }}>Express (Rp 15.000/kg)</option>
                    <option value="Kargo" {{ request('service') === 'Kargo' ? 'selected' : '' }}>Kargo (Rp 6.000/kg)</option>
                    <option value="Same Day" {{ request('service') === 'Same Day' ? 'selected' : '' }}>Same Day (Rp 20.000/kg)</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-[#0A1128] dark:bg-white/10 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                    Filter Laporan
                </button>
                @if(request()->hasAny(['search', 'status', 'service']))
                    <a href="{{ route('admin.laporan') }}" class="px-3 py-2 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- MAIN DATA TABLE FOR REPORT & EXPORT -->
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl border border-slate-200/80 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-extrabold text-[#0A1128] dark:text-white">Rincian Data yang Tercantum dalam Dokumen PDF</h3>
                <p class="text-xs text-slate-400 mt-0.5">Tabel berikut menampilkan seluruh item manifest pengiriman yang akan diekspor.</p>
            </div>
            <div class="text-xs text-slate-500 font-bold">
                Total: <span class="text-[#FF6B00]">{{ $orders->total() }}</span> Data Pengiriman
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-white/5 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-white/5">
                    <tr>
                        <th class="px-6 py-4">No & Resi</th>
                        <th class="px-6 py-4">Pengirim & Penerima</th>
                        <th class="px-6 py-4">Rute & Layanan</th>
                        <th class="px-6 py-4">Berat & Tarif</th>
                        <th class="px-6 py-4">Kurir Ditugaskan</th>
                        <th class="px-6 py-4">Status & Checkpoint</th>
                        <th class="px-6 py-4 text-center">Dokumen / Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                    @forelse($orders as $index => $order)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition">
                            <!-- No & Resi -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-mono font-bold text-slate-400 w-5">
                                        {{ $orders->firstItem() + $index }}.
                                    </span>
                                    <div class="w-14 h-8 rounded-lg bg-white border border-slate-200 dark:border-white/10 p-0.5 flex items-center justify-center shrink-0 shadow-xs" title="Barcode Resi">
                                        <img src="{{ $order->barcode_url }}" alt="Barcode" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-[#0A1128] dark:text-white font-mono text-xs tracking-wide block">
                                            {{ $order->tracking_number }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Pengirim & Penerima -->
                            <td class="px-6 py-4">
                                <div class="text-xs space-y-0.5">
                                    <p class="font-bold text-slate-900 dark:text-white">
                                        <i class="fa-solid fa-user text-slate-400 mr-1"></i> {{ $order->sender_name ?? 'Pelanggan' }}
                                    </p>
                                    <p class="text-slate-500 dark:text-slate-400">
                                        <i class="fa-solid fa-location-dot text-[#FF6B00] mr-1"></i> {{ $order->recipient_name ?? '-' }} ({{ $order->recipient_phone ?? '-' }})
                                    </p>
                                    @if($order->full_address)
                                        <p class="text-[10px] text-slate-400 mt-1 flex items-start gap-1 max-w-[220px]" title="{{ $order->full_address }}">
                                            <i class="fa-solid fa-map-pin text-[#FF6B00] text-[9px] mt-0.5 shrink-0"></i>
                                            <span class="truncate">{{ $order->full_address }}</span>
                                        </p>
                                    @endif
                                </div>
                            </td>

                            <!-- Rute & Layanan -->
                            <td class="px-6 py-4">
                                <div class="text-xs">
                                    <span class="font-bold text-[#0A1128] dark:text-white">{{ $order->origin }} → {{ $order->destination }}</span>
                                    <span class="block text-[11px] text-slate-400 mt-0.5">
                                        Layanan: <b class="text-[#FF6B00]">{{ $order->service_type }}</b>
                                    </span>
                                </div>
                            </td>

                            <!-- Berat & Tarif -->
                            <td class="px-6 py-4">
                                <div class="text-xs">
                                    <span class="font-extrabold text-[#0A1128] dark:text-white text-sm">
                                        Rp {{ number_format($order->price, 0, ',', '.') }}
                                    </span>
                                    <span class="block text-[10px] text-slate-400 font-semibold">
                                        {{ $order->weight }} Kg • {{ $order->payment_method ?? 'Cash/QRIS' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Kurir Ditugaskan -->
                            <td class="px-6 py-4">
                                <div class="text-xs">
                                    @if($order->courier)
                                        <div class="flex items-center gap-1.5 font-bold text-slate-800 dark:text-slate-200">
                                            <i class="fa-solid fa-motorcycle text-[#FF6B00]"></i>
                                            <span>{{ $order->courier->name }}</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $order->courier->phone ?? '-' }}</span>
                                    @else
                                        <span class="text-xs text-amber-600 font-semibold italic">Belum ditentukan</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status & Checkpoint -->
                            <td class="px-6 py-4">
                                <div>
                                    @if($order->status === 'Terkirim')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                                            <span>Terkirim</span>
                                        </span>
                                    @elseif($order->status === 'Dalam Perjalanan')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            <i class="fa-solid fa-truck-fast text-blue-600 dark:text-blue-400"></i>
                                            <span>Dalam Perjalanan</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            <i class="fa-solid fa-clipboard-check text-amber-600 dark:text-amber-400"></i>
                                            <span>Verifikasi Manifest</span>
                                        </span>
                                    @endif

                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 max-w-[200px] truncate" title="{{ $order->checkpoint }}">
                                        {{ $order->checkpoint ?? 'Drop point' }}
                                    </p>
                                </div>
                            </td>

                            <!-- Dokumen / Bukti -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($order->proof_image)
                                        <button type="button"
                                                onclick="openPhotoLightbox('{{ $order->proof_image_url }}', '{{ $order->tracking_number }}', '{{ addslashes($order->notes ?? 'Paket telah terkirim.') }}')"
                                                class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold hover:bg-emerald-100 transition shadow-sm"
                                                title="Lihat Bukti Foto">
                                            <i class="fa-solid fa-camera"></i>
                                        </button>
                                    @endif

                                    @if($order->pdf_report_path)
                                        <a href="{{ $order->pdf_report_url }}" target="_self"
                                           class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs font-bold hover:bg-rose-100 transition shadow-sm"
                                           title="Unduh Dokumen PDF">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </a>
                                    @endif

                                    @if(!$order->proof_image && !$order->pdf_report_path)
                                        <span class="text-[11px] text-slate-300 dark:text-slate-600 italic">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 mx-auto flex items-center justify-center mb-3">
                                    <i class="fa-solid fa-folder-open text-slate-400 text-lg"></i>
                                </div>
                                <p class="text-sm font-bold">Tidak ada data manifest yang sesuai filter.</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan pencarian atau reset filter untuk melihat seluruh laporan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ============================================================== -->
<!-- MODAL: IMPORT EXCEL MANIFEST                                   -->
<!-- ============================================================== -->
<div id="modalImportExcelLaporan" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 dark:border-white/10 transform transition-all">
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center text-lg font-bold border border-teal-200 dark:border-teal-800">
                    <i class="fa-solid fa-file-excel"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#0A1128] dark:text-white">Import Data Manifest Excel</h3>
                    <p class="text-xs text-slate-400">Unggah berkas spreadsheet untuk input pesanan massal</p>
                </div>
            </div>
            <button type="button" onclick="closeImportModalLaporan()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white text-xl font-bold">&times;</button>
        </div>

        <!-- Form Import -->
        <form action="{{ route('admin.import.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Input File Excel -->
            <div>
                <label for="file_excel_laporan" class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                    Pilih File Excel / CSV <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="file_excel" id="file_excel_laporan" accept=".xlsx,.xls,.csv" required
                       class="w-full text-xs text-slate-600 dark:text-slate-300 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 dark:file:bg-teal-950/60 file:text-teal-700 dark:file:text-teal-300 hover:file:bg-teal-100 border border-slate-200 dark:border-white/10 rounded-xl cursor-pointer p-1.5 focus:outline-none focus:border-teal-500">
                <p class="text-[11px] text-slate-400 mt-1">Format didukung: .xlsx, .xls, .csv (Maksimal 5MB)</p>
            </div>

            <!-- Teks Petunjuk Kolom Header Manifest -->
            <div class="p-3.5 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-200/80 dark:border-white/10 text-xs text-slate-600 dark:text-slate-300 space-y-1.5">
                <div class="flex items-center gap-2 font-bold text-slate-800 dark:text-white">
                    <i class="fa-solid fa-circle-info text-teal-600"></i>
                    <span>Petunjuk Format Kolom Excel:</span>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Pastikan baris pertama Excel Anda memiliki judul kolom:
                </p>
                <div class="p-2 rounded-xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-white/10 font-mono text-[11px] text-teal-600 dark:text-teal-400 font-semibold break-words">
                    nomor_resi, nama_pengirim, tujuan, berat, layanan, tarif
                </div>
            </div>

            <!-- Tombol Aksi Modal -->
            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeImportModalLaporan()"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition">
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Upload & Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openImportModalLaporan() {
        document.getElementById('modalImportExcelLaporan').classList.remove('hidden');
    }
    function closeImportModalLaporan() {
        document.getElementById('modalImportExcelLaporan').classList.add('hidden');
    }

    function toggleExportDropdown(event) {
        event.stopPropagation();
        const menu = document.getElementById('exportDropdownMenu');
        const arrow = document.getElementById('exportDropdownArrow');
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) {
                arrow.classList.toggle('rotate-180');
            }
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('exportDropdownContainer');
        const menu = document.getElementById('exportDropdownMenu');
        const arrow = document.getElementById('exportDropdownArrow');
        if (container && !container.contains(e.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    });
</script>
@endsection
