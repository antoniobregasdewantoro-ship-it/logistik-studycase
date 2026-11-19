@extends('layouts.app')

@section('title', 'Detail Barang - ' . ($barang->nama ?? 'INV-' . str_pad($barang->id, 5, '0', STR_PAD_LEFT)))

@section('content')
<div class="min-h-screen bg-slate-50/60 dark:bg-[#070C1D] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Top Header & Breadcrumb -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-white/10">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.orders') }}"
                   class="w-10 h-10 rounded-xl bg-white dark:bg-[#141E3D] border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-slate-100 transition shadow-xs">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-black text-[#0A1128] dark:text-white tracking-tight">Detail Barang</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Informasi lengkap data barang & barcode identitas barang inventaris (Code 39)</p>
                </div>
            </div>

            <!-- Badge Link ke Resi Pengiriman -->
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-orange-500/10 text-[#FF6B00] border border-[#FF6B00]/20">
                    <i class="fa-solid fa-box"></i>
                    <span>Tabel ID: #{{ $barang->id }}</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                    <i class="fa-solid fa-shield-check"></i>
                    <span>Status: {{ $barang->status ?? 'Baik' }}</span>
                </span>
            </div>
        </div>

        <!-- 2 Kolom Grid: Detail Barang & Barcode/QRIS -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Kolom Kiri: Informasi Barang (Slide 12) -->
            <div class="lg:col-span-5 bg-white dark:bg-[#0D1527] p-6 rounded-3xl border border-slate-200 dark:border-white/10 shadow-xs space-y-5">
                <!-- Foto / Placeholder Barang -->
                <div class="w-full h-48 rounded-2xl bg-slate-100 dark:bg-white/5 border border-dashed border-slate-200 dark:border-white/10 flex flex-col items-center justify-center text-slate-400">
                    @if(!empty($barang->initial_photo_url))
                        <img src="{{ $barang->initial_photo_url }}" alt="{{ $barang->nama }}" class="w-full h-full object-cover rounded-2xl">
                    @else
                        <i class="fa-regular fa-image text-4xl mb-2"></i>
                        <span class="text-xs font-bold">Tidak ada gambar</span>
                    @endif
                </div>

                <!-- Judul & Badge Kategori -->
                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-extrabold text-[#0A1128] dark:text-white">{{ $barang->nama }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-red-500/10 text-red-500 border border-red-500/20 uppercase">
                            {{ $barang->status ?? 'Normal' }}
                        </span>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400 mt-1">
                        <i class="fa-solid fa-tag text-[#FF6B00]"></i>
                        <span>{{ $barang->service_type ?? 'Kargo & Logistik' }}</span>
                    </span>
                </div>

                <!-- Jumlah / Berat Tersedia -->
                <div class="p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/20 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-cubes-stacked"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jumlah / Berat</span>
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $barang->weight ?? 1 }} <span class="text-xs font-normal text-slate-500">Kg / Pcs</span></span>
                    </div>
                </div>

                <!-- Deskripsi & Rincian -->
                <div class="space-y-2 text-xs border-t border-slate-100 dark:border-white/10 pt-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Deskripsi</span>
                        <p class="text-slate-600 dark:text-slate-300 mt-0.5">{{ $barang->notes ?? 'Paket pengiriman reguler dengan proteksi keamanan standar logistik.' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dibuat</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300">{{ $barang->created_at ? $barang->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Diupdate</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300">{{ $barang->updated_at ? $barang->updated_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Card Barcode 1D & QR Code 2D (Sesuai Slide Dosen 10, 11, 13) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 1. CARD BARCODE 1D (Code 39 - DNS1D) -->
                <div class="bg-white dark:bg-[#0D1527] p-6 rounded-3xl border border-slate-200 dark:border-white/10 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10 gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg font-bold">
                                <i class="fa-solid fa-barcode"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-[#0A1128] dark:text-white">Barcode Barang</h3>
                                <p class="text-xs text-slate-400">Kode ID {{ $barang->id }} dengan format Code 39 (C39) • Engine: DNS1D</p>
                            </div>
                        </div>

                        <!-- Tombol Cetak Barcode (Slide 11 & 12) -->
                        <button type="button" onclick="window.print()"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-md shadow-indigo-600/20">
                            <i class="fa-solid fa-print"></i>
                            <span>Cetak Barcode</span>
                        </button>
                    </div>

                    <!-- Pilihan Ganti Size Barcode (Tugas 2: Coba ganti size dari Barcode) -->
                    <div class="flex items-center justify-between bg-slate-50 dark:bg-white/5 p-3 rounded-2xl border border-slate-200 dark:border-white/10">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                            <i class="fa-solid fa-sliders text-[#FF6B00] mr-1"></i> Pilih Ukuran Barcode:
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('barang.show', ['id' => $barang->id, 'size' => '2x50']) }}"
                               class="px-2.5 py-1 text-[11px] font-bold rounded-lg border {{ $size === '2x50' ? 'bg-[#FF6B00] text-white border-[#FF6B00]' : 'bg-white dark:bg-[#141E3D] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-white/10 hover:border-[#FF6B00]' }}">
                                2 x 50 (Tabel)
                            </a>
                            <a href="{{ route('barang.show', ['id' => $barang->id, 'size' => '3x100']) }}"
                               class="px-2.5 py-1 text-[11px] font-bold rounded-lg border {{ $size === '3x100' ? 'bg-[#FF6B00] text-white border-[#FF6B00]' : 'bg-white dark:bg-[#141E3D] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-white/10 hover:border-[#FF6B00]' }}">
                                3 x 100 (Detail)
                            </a>
                            <a href="{{ route('barang.show', ['id' => $barang->id, 'size' => '4x120']) }}"
                               class="px-2.5 py-1 text-[11px] font-bold rounded-lg border {{ $size === '4x120' ? 'bg-[#FF6B00] text-white border-[#FF6B00]' : 'bg-white dark:bg-[#141E3D] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-white/10 hover:border-[#FF6B00]' }}">
                                4 x 120 (Besar)
                            </a>
                        </div>
                    </div>

                    <!-- AREA RENDER RENDER MILON BARCODE HTML (SLIDE 11) -->
                    <div id="printableBarcodeArea" class="card-body p-6 rounded-2xl bg-white border border-slate-200 shadow-inner"
                         style="display:flex;align-items:center;justify-content:center;overflow-x:auto;">
                        <div style="text-align:center">
                            <!-- 1. Nama Barang di Atas Barcode -->
                            <div class="text-xs uppercase font-extrabold tracking-widest text-slate-500 mb-3">
                                {{ strtoupper($barang->nama) }}
                            </div>

                            <!-- 2. Barcode Code 39 di Tengah (Raw Unescaped HTML sesuai Slide 8) -->
                            <div class="flex justify-center my-2 p-2 bg-white rounded-lg">
                                {!! $barang->barcodeHtml($w, $h) !!}
                            </div>

                            <!-- 3. Kode INV-00001 di Bawah Barcode -->
                            <div class="text-base font-black font-mono tracking-widest text-[#0A1128] mt-3">
                                {{ $barang->kode_barang }}
                            </div>
                        </div>
                    </div>

                    <div class="text-[11px] text-slate-400 dark:text-slate-500 text-center">
                        <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i>
                        Dibangkitkan secara client-side via <code>\DNS1D::getBarcodeHTML('{{ $barang->kode_barang }}', 'C39', {{ $w }}, {{ $h }})</code> tanpa file fisik PNG.
                    </div>
                </div>

                <!-- 2. CARD RINCIAN EKSPEDISI & RUTE PENGIRIMAN -->
                <div class="bg-white dark:bg-[#0D1527] p-6 rounded-3xl border border-slate-200 dark:border-white/10 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-orange-50 dark:bg-orange-500/10 text-[#FF6B00] flex items-center justify-center text-lg font-bold">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-extrabold text-[#0A1128] dark:text-white">Rincian Ekspedisi & Rute</h4>
                                <p class="text-[10px] text-slate-400">Informasi nomor resi dan distribusi paket</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-black text-[#0A1128] dark:text-white bg-slate-100 dark:bg-white/10 px-2.5 py-1 rounded-lg">
                                {{ $barang->tracking_number ?? 'MRD-00000' }}
                            </span>
                            @if(!empty($barang->tracking_number))
                                <a href="{{ route('resi.cetak', $barang->tracking_number) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-extrabold bg-[#0A1128] text-white hover:bg-[#FF6B00] shadow-xs transition">
                                    <i class="fa-solid fa-print text-[#FF6B00]"></i>
                                    <span>Cetak Resi</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-1">
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pengirim</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-sm block">{{ $barang->sender_name ?? 'Pelanggan' }}</span>
                            <span class="text-[11px] text-slate-500 block"><i class="fa-solid fa-location-dot text-[#FF6B00] mr-1"></i> {{ $barang->origin ?? '-' }}</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Penerima</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-sm block">{{ $barang->recipient_name ?? '-' }}</span>
                            <span class="text-[11px] text-slate-500 block"><i class="fa-solid fa-map-pin text-emerald-500 mr-1"></i> {{ $barang->destination ?? '-' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printableBarcodeArea, #printableBarcodeArea * {
        visibility: visible;
    }
    #printableBarcodeArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
