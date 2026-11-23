@extends('layouts.admin')

@section('title', 'Kelola Pesanan & Manifest')
@section('header_title', 'Manajemen Manifest & Resi')

@section('content')
<div class="space-y-6">

    <!-- TOP BAR ACTIONS & SEARCH -->
    <div
        class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-[#0A1128]">Daftar Manifest Pengiriman</h2>
            <p class="text-xs text-slate-500">Kelola siklus pengiriman, update checkpoint kurir, dan cetak barcode QR
                resi</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 border border-orange-200 text-[#FF6B00] text-xs font-bold">
                <i class="fa-solid fa-user-check"></i>
                <span>Pesanan Diinput oleh Pelanggan</span>
            </span>
            <button type="button" onclick="openImportModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                <i class="fa-solid fa-file-import text-sm"></i>
                <span>Import Excel</span>
            </button>
            <a href="{{ route('laporan.excel') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                <i class="fa-solid fa-file-excel text-sm"></i>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('admin.export-pdf') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                <i class="fa-solid fa-file-pdf text-rose-500 text-sm"></i>
                <span>Download PDF</span>
            </a>
        </div>
    </div>

    <!-- FILTER & SEARCH FORM -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.orders') }}"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari Resi, Pengirim, Kota..."
                    class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#FF6B00]">
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" onchange="this.form.submit()"
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#FF6B00] bg-white">
                    <option value="">-- Semua Status --</option>
                    <option value="Verifikasi" {{ request('status') === 'Verifikasi' ? 'selected' : '' }}>Verifikasi
                        Manifest</option>
                    <option value="Dalam Perjalanan" {{ request('status') === 'Dalam Perjalanan' ? 'selected' : '' }}>
                        Dalam Perjalanan</option>
                    <option value="Terkirim" {{ request('status') === 'Terkirim' ? 'selected' : '' }}>Terkirim</option>
                </select>
            </div>

            <!-- Delay Risk Filter -->
            <div>
                <select name="risk" onchange="this.form.submit()"
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#FF6B00] bg-white">
                    <option value="">-- Semua Risiko --</option>
                    <option value="Low" {{ request('risk') === 'Low' ? 'selected' : '' }}>Risiko Rendah (Aman)</option>
                    <option value="Medium" {{ request('risk') === 'Medium' ? 'selected' : '' }}>Risiko Sedang</option>
                    <option value="High" {{ request('risk') === 'High' ? 'selected' : '' }}>Risiko Tinggi (Delay)
                    </option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-[#0A1128] hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'risk']))
                <a href="{{ route('admin.orders') }}"
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- MAIN ORDERS TABLE -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead
                    class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Resi & Tanggal</th>
                        <th class="px-6 py-4">Pengirim & Penerima</th>
                        <th class="px-6 py-4">Rute Asal → Tujuan</th>
                        <th class="px-6 py-4">Kargo & Tarif</th>
                        <th class="px-6 py-4">Kurir Ditugaskan</th>
                        <th class="px-6 py-4">Status & Checkpoint</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/70 transition">
                        <!-- Resi & Tanggal -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <button type="button"
                                    onclick="showBarcodeModal('{{ $order->tracking_number }}', '{{ $order->barcode_url }}', '{{ addslashes($order->sender_name) }}', '{{ addslashes($order->destination) }}', '{{ $order->id }}', '{{ $order->kode_barang }}')"
                                    class="w-9 h-8 rounded-lg bg-white border border-slate-200 p-0.5 flex items-center justify-center hover:border-[#FF6B00] transition shadow-xs shrink-0"
                                    title="Lihat Barcode Resi">
                                    <img src="{{ $order->barcode_url }}" alt="Barcode"
                                        class="w-full h-full object-contain">
                                </button>
                                <div>
                                    <span
                                        class="font-extrabold text-[#0A1128] font-mono text-sm tracking-wide">{{ $order->tracking_number }}</span>
                                    <span
                                        class="block text-[11px] text-slate-400">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Pengirim & Penerima -->
                        <td class="px-6 py-4">
                            <div class="text-xs space-y-0.5">
                                <p class="font-bold text-slate-900"><i class="fa-solid fa-user text-slate-400 mr-1"></i>
                                    {{ $order->sender_name ?? 'Pelanggan' }}</p>
                                <p class="text-slate-500"><i class="fa-solid fa-location-dot text-[#FF6B00] mr-1"></i>
                                    {{ $order->recipient_name ?? '-' }} ({{ $order->recipient_phone ?? '-' }})</p>
                                @if($order->full_address)
                                <p class="text-[10px] text-slate-400 mt-1 flex items-start gap-1 max-w-[220px]"
                                    title="{{ $order->full_address }}">
                                    <i class="fa-solid fa-map-pin text-[#FF6B00] text-[9px] mt-0.5 shrink-0"></i>
                                    <span class="truncate">{{ $order->full_address }}</span>
                                </p>
                                @endif
                            </div>
                        </td>

                        <!-- Rute -->
                        <td class="px-6 py-4">
                            <div class="text-xs">
                                <span class="font-bold text-[#0A1128]">{{ $order->origin }} →
                                    {{ $order->destination }}</span>
                                <span class="block text-[11px] text-slate-400 mt-0.5">Layanan:
                                    <b>{{ $order->service_type }}</b></span>
                                <div class="mt-1">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold {{ $order->vehicle_type === 'pickup' ? 'bg-orange-50 text-[#FF6B00] border border-orange-200' : 'bg-blue-50 text-blue-600 border border-blue-200' }}">
                                        <i class="{{ $order->vehicle_icon }}"></i>
                                        <span>{{ $order->scope_label }}</span>
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Kargo & Tarif -->
                        <td class="px-6 py-4">
                            <div class="text-xs">
                                <span class="font-extrabold text-[#0A1128] text-sm">Rp
                                    {{ number_format($order->price, 0, ',', '.') }}</span>
                                <span class="block text-[10px] text-slate-500 font-semibold">{{ $order->weight }} Kg •
                                    {{ $order->payment_method }}</span>
                                @if($order->package_detail)
                                <p class="text-[11px] text-slate-700 font-semibold mt-1 max-w-[200px] truncate"
                                    title="Detail: {{ $order->package_detail }}">
                                    <i class="fa-solid fa-box text-[#FF6B00] mr-1"></i>{{ $order->package_detail }}
                                </p>
                                @endif
                                @if($order->initial_photo)
                                <button type="button"
                                    onclick="openPhotoLightbox('{{ $order->initial_photo_url }}', '{{ $order->tracking_number }} (Foto Sebelum Dikirim)', 'Detail: {{ addslashes($order->package_detail ?? 'Kondisi awal di cabang') }}')"
                                    class="inline-flex items-center gap-1 text-[10px] text-blue-600 font-bold hover:underline mt-1 block"
                                    title="Lihat foto paket sebelum dikirim">
                                    <i class="fa-solid fa-box-archive text-blue-500"></i>
                                    <span>Foto Sebelum Dikirim</span>
                                </button>
                                @endif
                            </div>
                        </td>

                        <!-- Kurir -->
                        <td class="px-6 py-4">
                            <div class="text-xs">
                                @if($order->courier)
                                <div class="flex items-center gap-1.5 font-bold text-slate-800">
                                    <i class="fa-solid fa-motorcycle text-[#FF6B00]"></i>
                                    <span>{{ $order->courier->name }}</span>
                                </div>
                                <span
                                    class="text-[10px] text-slate-400 font-mono">{{ $order->courier->phone ?? '-' }}</span>
                                @else
                                <span class="text-xs text-amber-600 font-semibold italic">Belum ditentukan</span>
                                @endif
                            </div>
                        </td>

                        <!-- Status & Checkpoint + Dokumen PDF -->
                        <td class="px-6 py-4">
                            <div>
                                @if($order->status === 'Terkirim')
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                    <span>Terkirim</span>
                                </span>
                                @elseif($order->status === 'Dalam Perjalanan')
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="fa-solid fa-truck-fast text-blue-600"></i>
                                    <span>Dalam Perjalanan</span>
                                </span>
                                @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    <i class="fa-solid fa-clipboard-check text-amber-600"></i>
                                    <span>Verifikasi Manifest</span>
                                </span>
                                @endif

                                <p class="text-[11px] text-slate-500 mt-1 max-w-[200px] truncate"
                                    title="{{ $order->checkpoint }}">
                                    {{ $order->checkpoint ?? 'Drop point' }}
                                </p>

                                <!-- Foto Bukti Pengiriman -->
                                @if($order->proof_image)
                                <button type="button"
                                    onclick="openPhotoLightbox('{{ $order->proof_image_url }}', '{{ $order->tracking_number }}', '{{ addslashes($order->notes ?? 'Paket telah terkirim.') }}')"
                                    class="inline-flex items-center gap-1 text-[10px] text-emerald-700 font-bold hover:underline mt-0.5 block">
                                    <i class="fa-solid fa-camera"></i>
                                    <span>Lihat Bukti Foto</span>
                                </button>
                                @endif

                                <!-- Dokumen Laporan PDF Terlampir (Requirement #4) -->
                                @if($order->pdf_report_path)
                                <div class="mt-1.5">
                                    <a href="{{ $order->pdf_report_url }}" target="_self"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition shadow-sm"
                                        title="Buka Dokumen PDF">
                                        <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                        <span>Laporan PDF Terlampir</span>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </td>

                        <!-- Aksi (Cetak Resi, Lihat Barcode, Detail Barang, dan Hapus) -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- 1. Cetak Resi Pengiriman Fisik -->
                                <a href="{{ route('resi.cetak', $order->tracking_number) }}" target="_blank"
                                    title="Cetak Resi"
                                    class="p-2 rounded-xl bg-orange-50 hover:bg-[#FF6B00] text-[#FF6B00] hover:text-white text-xs transition shadow-xs">
                                    <i class="fa-solid fa-print text-sm"></i>
                                </a>

                                <!-- 2. Print / Lihat Label Barcode Resi (1D Code 39) -->
                                <button type="button"
                                    onclick="showBarcodeModal('{{ $order->tracking_number }}', '{{ $order->barcode_url }}', '{{ addslashes($order->sender_name) }}', '{{ addslashes($order->destination) }}', '{{ $order->id }}', '{{ $order->kode_barang }}')"
                                    title="Lihat Barcode Resi"
                                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition">
                                    <i class="fa-solid fa-barcode text-sm"></i>
                                </button>

                                <!-- 3. Halaman Detail Barang & Barcode ID -->
                                <a href="{{ route('barang.show', $order->id) }}" target="_blank"
                                    title="Detail Barang & Barcode ID"
                                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>

                                <!-- 4. Hapus Pesanan (Buka Modal Konfirmasi Modern) -->
                                <button type="button"
                                    onclick="openDeleteOrderModal('{{ $order->id }}', '{{ $order->tracking_number }}')"
                                    title="Hapus Pesanan"
                                    class="p-2 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-600 text-xs transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-boxes-packing text-4xl mb-3 text-slate-300"></i>
                            <p class="text-sm font-semibold">Tidak ada data pesanan yang sesuai filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>

</div>

<!-- ============================================================== -->
<!-- MODAL: TAMBAH PESANAN (CREATE) + KALKULATOR TARIF OTOMATIS     -->
<!-- ============================================================== -->
<div id="modalCreateOrder"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div
        class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-[#0A1128] text-[#FF6B00] flex items-center justify-center font-bold">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-[#0A1128]">Terbitkan Pesanan / Manifest Baru</h3>
                    <p class="text-xs text-slate-500">Nomor resi acak dan QR code otomatis di-generate</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()"
                class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('admin.orders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Resi Custom (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Resi (Kosongkan untuk otomatis
                    MRD-XXXXX)</label>
                <input type="text" name="tracking_number" placeholder="Otomatis (cth: MRD-89123)"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00] font-mono">
            </div>

            <!-- Detail Isi Paket & Foto Sebelum Dikirim -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Detail / Isi Paket *</label>
                    <input type="text" name="package_detail" placeholder="Contoh: Sparepart, Baju, Dokumen..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Foto Sebelum Dikirim (Maks 2MB)</label>
                    <input type="file" name="initial_photo" accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#FF6B00] file:text-white file:cursor-pointer border border-slate-200 rounded-xl p-1 bg-white">
                </div>
            </div>

            <!-- Pengirim & Penerima -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pengirim *</label>
                    <input type="text" name="sender_name" required placeholder="Nama pengirim"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penerima *</label>
                    <input type="text" name="recipient_name" required placeholder="Nama penerima"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00]">
                </div>
            </div>

            <!-- Kontak Penerima -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP Penerima</label>
                <input type="text" name="recipient_phone" placeholder="08xxxxxxxxxx"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00]">
            </div>

            <!-- Asal & Tujuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kota Asal (Origin) *</label>
                    <input type="text" name="origin" value="Sidoarjo" required placeholder="Kota asal"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kota Tujuan (Destination) *</label>
                    <input type="text" name="destination" required placeholder="Kota tujuan"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00]">
                </div>
            </div>

            <!-- Detail Alamat Pengiriman Lengkap (RT/RW, Desa, Kecamatan, Kodepos) -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 block">
                    <i class="fa-solid fa-house-chimney text-[#FF6B00] mr-1"></i> Detail Alamat Lengkap & Rumah
                </span>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Alamat Jalan & Nomor Rumah</label>
                    <input type="text" name="street_address" placeholder="Contoh: Jl. Diponegoro No. 12, RT 02 / RW 04"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#FF6B00] bg-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">RT / RW</label>
                        <input type="text" name="rt_rw" placeholder="RT 02 / RW 04"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#FF6B00] bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Kelurahan / Desa</label>
                        <input type="text" name="village" placeholder="Desa / Kelurahan"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#FF6B00] bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Kecamatan</label>
                        <input type="text" name="district" placeholder="Kecamatan"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#FF6B00] bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1">Kode Pos</label>
                        <input type="text" name="postal_code" placeholder="61214"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-[#FF6B00] bg-white">
                    </div>
                </div>
            </div>

            <!-- Berat & Layanan (Kalkulator Tarif Dinamis) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Berat Paket (Kg) *</label>
                    <input type="number" id="createWeight" name="weight" step="0.1" min="0.1" value="3.0" required
                        oninput="calcTariffCreate()"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Layanan Pengiriman *</label>
                    <select id="createService" name="service_type" required onchange="calcTariffCreate()"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00] bg-white">
                        <option value="Reguler">Reguler (Rp 9.000 / Kg)</option>
                        <option value="Express">Express (Rp 15.000 / Kg)</option>
                        <option value="Kargo">Kargo (Rp 6.000 / Kg)</option>
                        <option value="Same Day">Same Day (Rp 20.000 / Kg)</option>
                    </select>
                </div>
            </div>

            <!-- Dynamic Tariff Box -->
            <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-700 block">Kalkulasi Ongkir Otomatis:</span>
                    <span class="text-[11px] text-slate-400">(Berat Paket x Harga Layanan Dasar)</span>
                </div>
                <div class="text-right">
                    <span id="createDisplayTariff" class="text-xl font-black text-[#FF6B00]">Rp 27.000</span>
                </div>
            </div>

            <!-- Penugasan Kurir -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tugaskan Kurir / Armada</label>
                <select name="courier_id"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00] bg-white">
                    <option value="">-- Otomatis Pilih Kurir Tersedia --</option>
                    @foreach($couriers as $courier)
                    <option value="{{ $courier->id }}">{{ $courier->name }} ({{ $courier->phone ?? 'Kurir' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Catatan opsional..."
                    class="w-full px-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#FF6B00]"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Batal</button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-[#0A1128] text-white text-xs font-bold hover:bg-slate-800 transition shadow-md">
                    <i class="fa-solid fa-check text-[#FF6B00] mr-1"></i> Terbitkan Manifest
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: KONFIRMASI HAPUS PESANAN MODERN                         -->
<!-- ============================================================== -->
<div id="modalConfirmDeleteOrder"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div
        class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 text-center transform transition-all">
        <!-- Warning Icon -->
        <div
            class="w-16 h-16 rounded-3xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl font-bold mx-auto mb-4 border border-rose-200 shadow-inner">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <h3 class="text-lg font-black text-[#0A1128]">
            Konfirmasi Hapus Pesanan
        </h3>

        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
            Apakah Anda yakin ingin menghapus data pesanan <span id="deleteOrderTrackingNumber"
                class="font-mono font-black text-[#FF6B00]">MRD-XXXXX</span>? Tindakan ini bersifat permanen dan data
            pengiriman tidak dapat dipulihkan.
        </p>

        <!-- Aksi Tombol -->
        <div class="mt-6 flex items-center justify-center gap-3">
            <button type="button" onclick="closeDeleteOrderModal()"
                class="flex-1 py-3 px-4 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                Batal
            </button>
            <form id="deleteOrderForm" action="" method="POST" class="flex-1 m-0">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black uppercase tracking-wider transition shadow-lg shadow-rose-600/30 flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Ya, Hapus</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: LABEL BARCODE RESI (1D CODE 128)                        -->
<!-- ============================================================== -->
<div id="modalBarcodeLabel"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-2 text-left">
                <div
                    class="w-8 h-8 rounded-lg bg-orange-50 text-[#FF6B00] flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-barcode"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-[#0A1128]">Label Barcode Resi</h3>
                    <p class="text-[10px] text-slate-400">Barcode 1D (Code 128) Khusus Resi Paket</p>
                </div>
            </div>
            <button type="button" onclick="closeBarcodeModal()"
                class="text-slate-400 hover:text-slate-700 text-lg font-bold">&times;</button>
        </div>

        <div id="printableBarcodeLabel" class="p-4 bg-white border border-slate-200 rounded-2xl shadow-sm text-center">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Meridian Express Logistics
            </div>
            <div class="text-[9px] font-bold text-[#FF6B00] mb-2 uppercase tracking-wider">Thermal Shipping Label (Resi
                Paket)</div>

            <div class="my-3 flex justify-center bg-white p-2 border border-slate-100 rounded-xl">
                <img id="barcodeModalImage" src="" alt="Barcode Resi" class="w-full max-h-20 object-contain">
            </div>

            <h4 id="barcodeModalResi" class="text-lg font-black font-mono text-[#0A1128] tracking-wider mb-3">MRD-48207
            </h4>

            <div class="text-left text-xs bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-1">
                <p><span class="text-slate-400 font-semibold">Kode ID Barang:</span> <span id="barcodeModalKodeBarang"
                        class="font-bold font-mono text-[#0A1128]">INV-00001</span></p>
                <p><span class="text-slate-400 font-semibold">Pengirim:</span> <span id="barcodeModalSender"
                        class="font-bold text-slate-800">Ahmad Fauzi</span></p>
                <p><span class="text-slate-400 font-semibold">Tujuan:</span> <span id="barcodeModalDestination"
                        class="font-bold text-[#FF6B00]">Surabaya</span></p>
            </div>

            <div class="mt-3 flex items-center justify-center">
                <a id="barcodeModalDetailLink" href="#" target="_blank"
                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-indigo-600 hover:text-indigo-800">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Lihat Halaman Detail Barang</span>
                </a>
            </div>
        </div>

        <div class="mt-5 flex items-center justify-center gap-2">
            <!-- Tombol Cetak Resi (Utama) -->
            <a id="barcodeModalCetakShopeeLink" href="#" target="_blank"
                class="px-5 py-2.5 rounded-xl bg-[#0A1128] hover:bg-[#FF6B00] text-white text-xs font-extrabold shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-print text-[#FF6B00]"></i>
                <span>Cetak Resi</span>
            </a>
            <button type="button" onclick="closeBarcodeModal()"
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: IMPORT EXCEL MANIFEST                                   -->
<!-- ============================================================== -->
<div id="modalImportExcel"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div
        class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-100 transform transition-all">
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg font-bold border border-teal-200">
                    <i class="fa-solid fa-file-excel"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#0A1128]">Import Data Manifest Excel</h3>
                    <p class="text-xs text-slate-400">Unggah berkas spreadsheet untuk input pesanan massal</p>
                </div>
            </div>
            <button type="button" onclick="closeImportModal()"
                class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
        </div>

        <!-- Form Import -->
        <form action="{{ route('admin.import.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Input File Excel -->
            <div>
                <label for="file_excel" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Pilih File Excel / CSV <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="file_excel" id="file_excel" accept=".xlsx,.xls,.csv" required
                    class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 border border-slate-200 rounded-xl cursor-pointer p-1.5 focus:outline-none focus:border-teal-500">
                <p class="text-[11px] text-slate-400 mt-1">Format didukung: .xlsx, .xls, .csv (Maksimal 5MB)</p>
            </div>

            <!-- Teks Petunjuk Kolom Header Manifest -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs text-slate-600 space-y-1.5">
                <div class="flex items-center gap-2 font-bold text-slate-800">
                    <i class="fa-solid fa-circle-info text-teal-600"></i>
                    <span>Petunjuk Format Kolom Excel:</span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Pastikan baris pertama Excel Anda memiliki judul kolom:
                </p>
                <div
                    class="p-2 rounded-xl bg-white border border-slate-200 font-mono text-[11px] text-teal-700 font-semibold break-words">
                    nomor_resi, nama_pengirim, tujuan, berat, layanan, tarif
                </div>
            </div>

            <!-- Tombol Aksi Modal -->
            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeImportModal()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
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

@endsection

@push('scripts')
<script>
function openImportModal() {
    document.getElementById('modalImportExcel').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('modalImportExcel').classList.add('hidden');
}

function openCreateModal() {
    document.getElementById('modalCreateOrder').classList.remove('hidden');
    calcTariffCreate();
}

function closeCreateModal() {
    document.getElementById('modalCreateOrder').classList.add('hidden');
}

function calcTariffCreate() {
    const weight = parseFloat(document.getElementById('createWeight').value) || 0;
    const service = document.getElementById('createService').value;
    const rates = {
        'Reguler': 9000,
        'Express': 15000,
        'Kargo': 6000,
        'Same Day': 20000
    };
    const ratePerKg = rates[service] || 9000;
    const total = weight * ratePerKg;
    document.getElementById('createDisplayTariff').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// Modal Konfirmasi Hapus Pesanan Modern
function openDeleteOrderModal(orderId, trackingNumber) {
    document.getElementById('deleteOrderTrackingNumber').textContent = trackingNumber;
    document.getElementById('deleteOrderForm').action = '/admin/orders/' + orderId + '/delete';
    document.getElementById('modalConfirmDeleteOrder').classList.remove('hidden');
}

function closeDeleteOrderModal() {
    document.getElementById('modalConfirmDeleteOrder').classList.add('hidden');
}

function showBarcodeModal(resi, barcodeUrl, sender, destination, orderId, kodeBarang) {
    document.getElementById('barcodeModalResi').textContent = resi;
    document.getElementById('barcodeModalImage').src = barcodeUrl;
    document.getElementById('barcodeModalSender').textContent = sender || 'Pelanggan';
    document.getElementById('barcodeModalDestination').textContent = destination || '-';
    if (document.getElementById('barcodeModalKodeBarang')) {
        document.getElementById('barcodeModalKodeBarang').textContent = kodeBarang || ('INV-' + String(orderId || '1')
            .padStart(5, '0'));
    }
    if (document.getElementById('barcodeModalDetailLink')) {
        if (orderId) {
            document.getElementById('barcodeModalDetailLink').href = '/barang/' + orderId;
            document.getElementById('barcodeModalDetailLink').classList.remove('hidden');
        } else {
            document.getElementById('barcodeModalDetailLink').classList.add('hidden');
        }
    }
    if (document.getElementById('barcodeModalCetakShopeeLink')) {
        document.getElementById('barcodeModalCetakShopeeLink').href = '/resi/' + resi + '/cetak';
    }
    document.getElementById('modalBarcodeLabel').classList.remove('hidden');
}

function closeBarcodeModal() {
    document.getElementById('modalBarcodeLabel').classList.add('hidden');
}

// Alias untuk kompatibilitas fungsi pemanggilan lama
function showQrModal(resi, barcodeUrl, sender, destination) {
    showBarcodeModal(resi, barcodeUrl, sender, destination);
}

function printBarcodeLabel() {
    const printContents = document.getElementById('printableBarcodeLabel').innerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = '<div style="max-width:350px;margin:20px auto;text-align:center;">' + printContents +
        '</div>';
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}

function printQrLabel() {
    printBarcodeLabel();
}
</script>
@endpush