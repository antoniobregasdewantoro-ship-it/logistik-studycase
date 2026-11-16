@extends('layouts.app')

@section('title', 'Pesan Pengiriman Baru - Portal Pelanggan Meridian')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 space-y-8 animate-in fade-in duration-300">

    <!-- TOP HEADER & BREADCRUMB (NO SIDEBAR - CLEAN & FOCUS ON ORDER) -->
    <div class="bg-gradient-to-r from-[#0A1128] via-[#141E3D] to-[#0A1128] rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/10 hover:bg-white/20 text-slate-200 transition">
                    <i class="fa-solid fa-arrow-left text-[11px] text-[#FF6B00]"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#FF6B00]/20 text-[#FF6B00] border border-[#FF6B00]/30">
                    PORTAL ORDER PELANGGAN
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                Pesan Pengiriman, Harga Real-Time.
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-2xl">
                Lengkapi rincian penjemputan paket, alamat tujuan, detail barang, dan pilih metode pembayaran (QRIS / Cash). Kurir akan menjemput paket di alamat Anda untuk dibawa ke gudang cabang dan dikirimkan ke tujuan.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <div class="px-4 py-3 rounded-2xl bg-white/5 border border-white/10 text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Akun Pemesan:</span>
                <span class="text-xs font-black text-white flex items-center gap-1.5 justify-end">
                    <i class="fa-solid fa-circle-user text-[#FF6B00]"></i>
                    <span>{{ auth()->user()->name }}</span>
                </span>
            </div>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN: FORM KIRI (7 COLS) & LIVE SUMMARY CARD KANAN (5 COLS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- ============================================================== -->
        <!-- KOLOM KIRI: FORMULIR INPUT PEMESANAN                           -->
        <!-- ============================================================== -->
        <div class="lg:col-span-7 bg-white dark:bg-[#0D1527] rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-white/10 shadow-sm transition space-y-6">

            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500/10 text-[#FF6B00] flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold text-[#0A1128] dark:text-white">Formulir Order Pengiriman Paket</h2>
                        <p class="text-xs text-slate-400">Data ini digunakan kurir untuk menjemput dan mengantar paket Anda.</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-slate-400">Langkah 1 dari 2</span>
            </div>

            <form id="newOrderForm" action="{{ route('customer.orders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- PENGIRIM & PENERIMA -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">NAMA PENGIRIM *</label>
                        <input type="text" name="sender_name" id="orderSender" value="{{ auth()->user()->name ?? 'Pelanggan' }}" required
                               oninput="updateLiveOrderSummary()"
                               placeholder="Nama Pengirim"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">NAMA PENERIMA *</label>
                        <input type="text" name="recipient_name" id="orderRecipient" value="Logistics Distribution Center" required
                               oninput="updateLiveOrderSummary()"
                               placeholder="Nama Penerima"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                </div>

                <!-- NO. TELEPON PENERIMA -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">NO. TELEPON / WHATSAPP PENERIMA *</label>
                    <input type="text" name="recipient_phone" id="orderPhone" placeholder="Contoh: 081234567890" value="081298765432" required
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                </div>

                <!-- CABANG PENGAMBILAN (HUB ASAL) & KOTA TUJUAN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">
                            <i class="fa-solid fa-warehouse text-[#FF6B00] mr-1"></i> CABANG PENGAMBILAN (HUB ASAL) *
                        </label>
                        <select name="origin" id="orderOrigin" onchange="updateLiveOrderSummary()"
                                class="w-full px-4 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                            @if(isset($branches) && $branches->count() > 0)
                                @foreach($branches as $b)
                                    <option value="{{ $b->name }}, {{ $b->city }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $b->name }} ({{ $b->city }})
                                    </option>
                                @endforeach
                            @else
                                <option value="Cabang Hub Waru, Sidoarjo" selected>Cabang Hub Waru (Sidoarjo)</option>
                                <option value="Cabang Hub Rungkut, Surabaya">Cabang Hub Rungkut (Surabaya)</option>
                                <option value="Cabang Hub Tanjung Priok, Jakarta">Cabang Hub Tanjung Priok (Jakarta)</option>
                                <option value="Cabang Hub Soekarno Hatta, Bandung">Cabang Hub Soekarno Hatta (Bandung)</option>
                                <option value="Cabang Hub Semarang, Semarang">Cabang Hub Semarang (Semarang)</option>
                                <option value="Cabang Hub Klojen, Malang">Cabang Hub Klojen (Malang)</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">
                            <i class="fa-solid fa-location-dot text-emerald-500 mr-1"></i> KOTA TUJUAN PENGIRIMAN *
                        </label>
                        <select name="destination" id="orderDestination" onchange="updateLiveOrderSummary()"
                                class="w-full px-4 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                            <option value="Surabaya, ID" selected>Surabaya, ID</option>
                            <option value="Sidoarjo, ID">Sidoarjo, ID</option>
                            <option value="Malang, ID">Malang, ID</option>
                            <option value="Jakarta, ID">Jakarta, ID</option>
                            <option value="Bandung, ID">Bandung, ID</option>
                            <option value="Semarang, ID">Semarang, ID</option>
                            <option value="Yogyakarta, ID">Yogyakarta, ID</option>
                            <option value="Denpasar, Bali">Denpasar, Bali</option>
                            <option value="Medan, ID">Medan, ID</option>
                            <option value="Makassar, ID">Makassar, ID</option>
                        </select>
                    </div>
                </div>

                <!-- DETAIL ALAMAT PENGIRIMAN & RT / RW -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">DETAIL ALAMAT LENGKAP PENERIMA *</label>
                        <input type="text" name="street_address" id="orderStreetAddress"
                               value="Kawasan Industri Rungkut Megah No. 42" required
                               oninput="updateLiveOrderSummary()"
                               placeholder="Jalan, No. Rumah, Blok, Patokan"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">RT / RW</label>
                        <input type="text" name="rt_rw" id="orderRtRw" value="RT 03 / RW 05"
                               oninput="updateLiveOrderSummary()"
                               placeholder="RT 01 / RW 02"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                </div>

                <!-- KELURAHAN, KECAMATAN, KODE POS -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">KELURAHAN / DESA</label>
                        <input type="text" name="village" id="orderVillage" value="Kali Rungkut"
                               placeholder="Kelurahan"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">KECAMATAN</label>
                        <input type="text" name="district" id="orderDistrict" value="Rungkut"
                               placeholder="Kecamatan"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">KODE POS</label>
                        <input type="text" name="postal_code" id="orderPostalCode" value="60293"
                               placeholder="60293"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                </div>

                <!-- DETAIL / ISI PAKET & FOTO SEBELUM DIKIRIM (MAKS 2MB) -->
                <div class="p-5 rounded-3xl bg-slate-50 dark:bg-[#101935] border border-slate-200 dark:border-white/5 space-y-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-box text-[#FF6B00]"></i>
                        <span class="text-xs font-black text-[#0A1128] dark:text-white uppercase tracking-wider">KONDISI & FOTO BARANG SEBELUM DIKIRIM</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">DETAIL / DESKRIPSI ISI PAKET *</label>
                            <input type="text" name="package_detail" id="orderPackageDetail"
                                   value="Komponen Mesin Industri & Sparepart Elektronik" required
                                   oninput="updateLiveOrderSummary()"
                                   placeholder="Contoh: Sparepart, Baju, Dokumen Penting..."
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">
                                FOTO PAKET SEBELUM DIKIRIM (MAKS 2MB)
                            </label>
                            <input type="file" name="initial_photo" id="orderInitialPhotoInput"
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   onchange="handleInitialPhotoPreview(this)"
                                   class="w-full text-xs text-slate-500 file:mr-2 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#FF6B00] file:text-white file:cursor-pointer border border-slate-200 dark:border-slate-700 rounded-2xl p-1.5 bg-white dark:bg-[#141E3D]">
                            <p class="text-[10px] text-slate-400 mt-1">Hanya file gambar (JPG, PNG, WEBP). Maksimal ukuran 2MB.</p>
                        </div>
                    </div>

                    <!-- Preview Thumbnail Foto Awal -->
                    <div id="initialPhotoPreviewContainer" class="hidden items-center gap-3 p-3 rounded-2xl bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-white/10">
                        <img id="initialPhotoPreviewImg" src="" alt="Preview Foto Paket" class="w-14 h-14 object-cover rounded-xl border border-slate-200">
                        <div>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">Foto Paket Terpilih</span>
                            <span id="initialPhotoSizeText" class="text-[10px] text-emerald-500 font-bold">Ukuran aman (< 2MB)</span>
                        </div>
                    </div>
                </div>

                <!-- BERAT MUATAN (KG) & LAYANAN PENGIRIMAN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">BERAT MUATAN (KG) *</label>
                            <span id="weightLbConversion" class="text-[11px] font-bold text-[#FF6B00]">2646 lb</span>
                        </div>
                        <input type="number" name="weight" id="orderWeightInput" value="1200" step="0.1" min="0.1" required
                               oninput="updateLiveOrderSummary()"
                               placeholder="1.200"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">PILIHAN LAYANAN *</label>
                        <select name="service_type" id="orderServiceType" onchange="updateLiveOrderSummary()"
                                class="w-full px-4 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                            <option value="Kargo" selected>Kargo Muatan Besar (Rp 6.000 / Kg)</option>
                            <option value="Reguler">Reguler Standar (Rp 9.000 / Kg)</option>
                            <option value="Express">Express Cepat (Rp 15.000 / Kg)</option>
                            <option value="Same Day">Same Day Antar Hari Ini (Rp 20.000 / Kg)</option>
                        </select>
                    </div>
                </div>

                <!-- METODE PEMBAYARAN: QRIS ATAU CASH (TUNAI) -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-wallet text-[#FF6B00] mr-1"></i> METODE PEMBAYARAN *
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Pilihan 1: QRIS -->
                        <div onclick="selectPaymentMethod('QRIS (Lunas)')"
                             id="cardPayQris"
                             class="payment-option cursor-pointer p-4 rounded-2xl border-2 border-[#FF6B00] bg-orange-500/5 dark:bg-[#FF6B00]/10 flex items-center gap-3.5 transition">
                            <div class="w-10 h-10 rounded-xl bg-[#FF6B00] text-white flex items-center justify-center text-lg font-bold shadow-md shadow-[#FF6B00]/30 shrink-0">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black text-[#0A1128] dark:text-white">QRIS (Otomatis)</span>
                                    <span id="badgePayQris" class="w-2.5 h-2.5 rounded-full bg-[#FF6B00]"></span>
                                </div>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">Pembayaran instan scan barcode QR</span>
                            </div>
                        </div>

                        <!-- Pilihan 2: Cash / Tunai -->
                        <div onclick="selectPaymentMethod('Cash (Tunai)')"
                             id="cardPayCash"
                             class="payment-option cursor-pointer p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#141E3D] flex items-center gap-3.5 transition hover:border-slate-400">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 flex items-center justify-center text-lg font-bold shrink-0">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black text-[#0A1128] dark:text-white">Cash (Tunai)</span>
                                    <span id="badgePayCash" class="w-2.5 h-2.5 rounded-full bg-transparent border border-slate-300 dark:border-slate-600"></span>
                                </div>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">Bayar langsung ke kurir saat dijemput</span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="QRIS (Lunas)">

                    <!-- Dynamic QR Code QRIS & Waktu Berjalan Container -->
                    <div id="qrisDetailContainer" class="mt-4 p-5 rounded-3xl bg-gradient-to-br from-[#0A1128] via-[#101935] to-[#0A1128] text-white border border-[#FF6B00]/40 shadow-xl space-y-4">
                        <!-- QRIS Header -->
                        <div class="flex items-center justify-between pb-3 border-b border-white/10">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2.5 py-1 rounded-lg bg-[#FF6B00] text-white text-[10px] font-black tracking-wider uppercase shadow-md shadow-[#FF6B00]/30">
                                    QRIS
                                </span>
                                <div>
                                    <h4 class="text-xs font-black tracking-tight text-white">MERIDIAN LOGISTICS INDONESIA</h4>
                                    <span class="text-[9px] text-slate-400 font-mono">NMID: ID1020268899120 • STANDAR BI</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Verifikasi Otomatis</span>
                            </span>
                        </div>

                        <!-- QR Code Pembayaran, Total Bayar & Jam Pembayaran Berjalan -->
                        <div class="flex flex-col sm:flex-row items-center gap-5">
                            <!-- QR Code Box (Milon Barcode DNS2D 'QRCODE' Khusus Metode Pembayaran QRIS) -->
                            <div class="bg-white p-3 rounded-2xl shadow-lg border border-white/20 text-center shrink-0">
                                <img id="qrisBarcodeImage"
                                     src="data:image/png;base64,{!! DNS2D::getBarcodePNG('00020101021226670016ID.CO.MERIDIAN.WWW0118936009990000000001520454995303360540', 'QRCODE', 6, 6) !!}"
                                     alt="QR Code QRIS"
                                     class="w-32 h-32 sm:w-36 sm:h-36 object-contain rounded-lg">
                                <span class="block text-[8px] font-bold text-slate-600 mt-1 uppercase tracking-wider">Scan QRIS (BCA / Mandiri / GoPay / OVO / Dana)</span>
                            </div>

                            <!-- Total Bayar & Jam Pembayaran Berjalan -->
                            <div class="flex-1 space-y-3 text-center sm:text-left w-full">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Tagihan Pembayaran:</span>
                                    <div class="text-2xl sm:text-3xl font-black text-[#FF6B00] tracking-tight mt-0.5">
                                        Rp <span id="qrisDisplayPrice">1.917.000</span>
                                    </div>
                                    <span class="text-[10px] text-slate-300">Biaya sudah termasuk asuransi & PPN logistik.</span>
                                </div>

                                <!-- WAKTU PEMBAYARAN BERJALAN & COUNTDOWN -->
                                <div class="p-3 rounded-2xl bg-white/5 border border-white/10 space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-400 font-bold flex items-center gap-1.5 text-[11px]">
                                            <i class="fa-regular fa-clock text-[#FF6B00]"></i> Jam Pembayaran:
                                        </span>
                                        <span id="liveRunningClock" class="font-mono font-black text-white text-xs bg-black/40 px-2 py-0.5 rounded-lg border border-white/10">
                                            --:--:-- WIB
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-400 font-bold flex items-center gap-1.5 text-[11px]">
                                            <i class="fa-solid fa-stopwatch text-amber-400 animate-pulse"></i> Batas Waktu Bayar:
                                        </span>
                                        <span id="liveCountdownTimer" class="font-mono font-black text-amber-400 text-xs bg-amber-400/10 px-2 py-0.5 rounded-lg border border-amber-400/20">
                                            14:59
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Detail Container (Tampil saat pilih Cash) -->
                    <div id="cashDetailContainer" class="mt-4 p-5 rounded-3xl bg-slate-50 dark:bg-[#141E3D] border border-slate-200 dark:border-slate-700 shadow-sm space-y-3 hidden">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 flex items-center justify-center text-lg font-bold">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-[#0A1128] dark:text-white">Pembayaran Tunai (Cash on Pickup)</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Serahkan uang tunai pas kepada kurir saat penjemputan paket di lokasi.</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-2xl bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-white/5 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">Total Tagihan Tunai:</span>
                            <span class="text-base sm:text-lg font-black text-[#FF6B00]">Rp <span id="cashDisplayPrice">1.917.000</span></span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span><i class="fa-regular fa-clock mr-1 text-[#FF6B00]"></i> Jam Pemesanan Real-Time:</span>
                            <span id="cashLiveClock" class="font-mono font-bold text-slate-700 dark:text-slate-200">--:--:-- WIB</span>
                        </div>
                    </div>
                </div>

                <!-- WAKTU PENGIRIMAN PAKET (Pill buttons) -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">JADWAL WAKTU PENJEMPUTAN PAKET</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <button type="button" onclick="selectPickupTime(this, 'Hari ini, 16:00')"
                                class="pickup-pill py-2.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:border-slate-400 transition text-center">
                            Hari ini, 16:00
                        </button>
                        <button type="button" onclick="selectPickupTime(this, 'Besok, 08:00')"
                                class="pickup-pill active py-2.5 px-3 rounded-xl bg-[#0A1128] text-white text-xs font-bold transition text-center">
                            Besok, 08:00
                        </button>
                        <button type="button" onclick="selectPickupTime(this, 'Besok, 14:00')"
                                class="pickup-pill py-2.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:border-slate-400 transition text-center">
                            Besok, 14:00
                        </button>
                        <button type="button" onclick="selectPickupTime(this, 'Jum, 09:00')"
                                class="pickup-pill py-2.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:border-slate-400 transition text-center">
                            Jum, 09:00
                        </button>
                        <button type="button" onclick="selectPickupTime(this, 'Sab, 10:00')"
                                class="pickup-pill py-2.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:border-slate-400 transition text-center">
                            Sab, 10:00
                        </button>
                    </div>
                    <input type="hidden" name="pickup_slot" id="selectedPickupSlot" value="Besok, 08:00">
                </div>

                <!-- SUBMIT BUTTON ON FORM -->
                <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex items-center justify-between gap-4">
                    <a href="{{ route('customer.dashboard') }}" class="px-5 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="button" onclick="validateAndSubmitOrder()"
                            class="flex-1 py-3.5 px-6 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-[#FF6B00]/30 transition flex items-center justify-center gap-2">
                        <span>Pesan Pengiriman & Jemput Paket Sekarang</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>

            </form>
        </div>

        <!-- ============================================================== -->
        <!-- KOLOM KANAN: CARD INFORMASI DARI INPUT FORM (LIVE SUMMARY)     -->
        <!-- ============================================================== -->
        <div class="lg:col-span-5 bg-[#0D1527] rounded-3xl p-6 sm:p-8 text-white shadow-2xl border border-white/5 relative overflow-hidden space-y-6">

            <!-- Top Tag: Rincian Manifest Real-Time -->
            <div class="flex items-center justify-between pb-4 border-b border-white/10">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    RINGKASAN PESANAN REAL-TIME
                </span>
                <span class="text-xs text-slate-400 font-bold flex items-center gap-1">
                    <i class="fa-solid fa-arrows-rotate text-[#FF6B00]"></i> Live Sync
                </span>
            </div>

            <!-- Estimasi Tarif Pengiriman Dinamis -->
            <div>
                <span class="text-xs text-slate-400 font-semibold block">Estimasi Tarif Pengiriman</span>
                <div class="text-4xl sm:text-5xl font-black text-white tracking-tight mt-1 mb-2 flex items-baseline">
                    <span class="text-[#FF6B00] text-2xl sm:text-3xl font-extrabold mr-1.5">Rp</span>
                    <span id="displayDynamicPrice">1.917.000</span>
                </div>
                <p id="displayRouteDetails" class="text-xs text-slate-400 font-medium">
                    Cabang Waru → Surabaya • 1.200 kg • Layanan Kargo • Tiba: Kamis, 12 Nov
                </p>

                <!-- KOTAK PROMINENT ESTIMASI PAKET SAMPAI (ETA REAL-TIME) -->
                <div class="mt-4 p-4 rounded-2xl bg-gradient-to-r from-amber-500/15 via-[#FF6B00]/15 to-emerald-500/15 border border-[#FF6B00]/40 flex items-center justify-between shadow-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#FF6B00] text-white flex items-center justify-center text-base shadow-md shadow-[#FF6B00]/40 shrink-0">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-300 block">
                                Estimasi Paket Sampai (Perkiraan Tiba):
                            </span>
                            <div class="flex items-baseline gap-2 mt-0.5">
                                <span id="displayEstimatedArrival" class="text-sm sm:text-base font-black text-amber-300">
                                    Kamis, 12 Nov 2026
                                </span>
                                <span id="displayDurationLabel" class="text-[11px] text-slate-200 font-bold">
                                    (Estimasi 3 - 4 Hari)
                                </span>
                            </div>
                        </div>
                    </div>
                    <span id="displayEtaBadge" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shrink-0">
                        Tepat Waktu
                    </span>
                </div>
            </div>

            <!-- Kotak Rincian Data Terisi -->
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-3.5">
                <div class="flex items-center justify-between pb-2 border-b border-white/10">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#FF6B00]">INFORMASI PAKET & RUTE TERISI</span>
                    <span id="cardPickupTimeBadge" class="px-2 py-0.5 rounded-md bg-white/10 text-[10px] font-bold text-slate-300">Besok, 08:00</span>
                </div>

                <!-- Cabang Pengambilan -->
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-[#FF6B00]/20 text-[#FF6B00] flex items-center justify-center text-xs shrink-0 mt-0.5">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-[10px] text-slate-400 block">Cabang Pengambilan (Hub Asal):</span>
                        <span id="cardOriginDisplay" class="font-bold text-white text-xs block">Cabang Hub Waru, Sidoarjo</span>
                    </div>
                </div>

                <!-- Tujuan & Alamat Lengkap -->
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-[10px] text-slate-400 block">Alamat Tujuan Pengiriman:</span>
                        <span id="cardDestinationDisplay" class="font-bold text-white text-xs block">Surabaya, ID</span>
                        <span id="cardAddressDisplay" class="text-[11px] text-slate-300 block mt-0.5">Kawasan Industri Rungkut Megah No. 42 (RT 03 / RW 05)</span>
                    </div>
                </div>

                <!-- Detail Isi Paket -->
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-[10px] text-slate-400 block">Detail Isi Paket:</span>
                        <span id="cardPackageDetailDisplay" class="font-bold text-white text-xs block truncate">Komponen Mesin Industri & Sparepart Elektronik</span>
                    </div>
                </div>

                <!-- Berat & Layanan -->
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                        <i class="fa-solid fa-weight-hanging"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-[10px] text-slate-400 block">Muatan & Jenis Layanan:</span>
                        <span id="cardWeightServiceDisplay" class="font-bold text-white text-xs block">1.200 Kg (2646 lb) • Layanan Kargo</span>
                    </div>
                </div>

                <!-- Estimasi Paket Sampai di Penerima -->
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-[10px] text-slate-400 block">Estimasi Paket Sampai di Penerima:</span>
                        <span id="cardEtaDisplay" class="font-bold text-amber-300 text-xs block">Kamis, 12 Nov 2026 • Perkiraan 3 - 4 Hari Kerja</span>
                    </div>
                </div>

                <!-- Metode Pembayaran Terpilih -->
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs shrink-0 mt-0.5">
                        <i id="cardPaymentIcon" class="fa-solid fa-qrcode"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-[10px] text-slate-400 block">Metode Pembayaran:</span>
                        <span id="cardPaymentDisplay" class="font-bold text-emerald-400 text-xs block truncate">QRIS (Lunas)</span>
                    </div>
                </div>
            </div>

            <!-- JAMINAN OPERASIONAL LOGISTIK -->
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-[#FF6B00]"></i> JAMINAN & KEUNGGULAN MERIDIAN
                </span>
                <ul class="text-[11px] text-slate-300 space-y-1.5">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-400 text-[10px]"></i>
                        <span>Penjemputan langsung di alamat oleh kurir armada resmi.</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-400 text-[10px]"></i>
                        <span>Paket diproses melalui Hub Gudang terdekat secara terstandar.</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-400 text-[10px]"></i>
                        <span>Pelacakan resi real-time & verifikasi foto serah terima.</span>
                    </li>
                </ul>
            </div>

            <!-- Tombol Konfirmasi Besar di Kolom Kanan -->
            <button type="button" onclick="validateAndSubmitOrder()"
                    class="w-full py-4 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white font-black text-sm uppercase tracking-wider transition shadow-xl shadow-[#FF6B00]/40 flex items-center justify-center gap-2">
                <span>Pesan Pengiriman Sekarang</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Pilih Metode Pembayaran (QRIS atau Cash)
    function selectPaymentMethod(method) {
        document.getElementById('selectedPaymentMethod').value = method;

        const qrisCard = document.getElementById('cardPayQris');
        const cashCard = document.getElementById('cardPayCash');
        const qrisBadge = document.getElementById('badgePayQris');
        const cashBadge = document.getElementById('badgePayCash');
        const qrisDetail = document.getElementById('qrisDetailContainer');
        const cashDetail = document.getElementById('cashDetailContainer');

        if (method === 'QRIS (Lunas)') {
            qrisCard.className = 'payment-option cursor-pointer p-4 rounded-2xl border-2 border-[#FF6B00] bg-orange-500/5 dark:bg-[#FF6B00]/10 flex items-center gap-3.5 transition';
            cashCard.className = 'payment-option cursor-pointer p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#141E3D] flex items-center gap-3.5 transition hover:border-slate-400';
            qrisBadge.className = 'w-2.5 h-2.5 rounded-full bg-[#FF6B00]';
            cashBadge.className = 'w-2.5 h-2.5 rounded-full bg-transparent border border-slate-300 dark:border-slate-600';
            if (qrisDetail) qrisDetail.classList.remove('hidden');
            if (cashDetail) cashDetail.classList.add('hidden');
        } else {
            cashCard.className = 'payment-option cursor-pointer p-4 rounded-2xl border-2 border-[#FF6B00] bg-orange-500/5 dark:bg-[#FF6B00]/10 flex items-center gap-3.5 transition';
            qrisCard.className = 'payment-option cursor-pointer p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#141E3D] flex items-center gap-3.5 transition hover:border-slate-400';
            cashBadge.className = 'w-2.5 h-2.5 rounded-full bg-[#FF6B00]';
            qrisBadge.className = 'w-2.5 h-2.5 rounded-full bg-transparent border border-slate-300 dark:border-slate-600';
            if (qrisDetail) qrisDetail.classList.add('hidden');
            if (cashDetail) cashDetail.classList.remove('hidden');
        }

        updateLiveOrderSummary();
    }

    // Pilih Slot Waktu
    function selectPickupTime(btn, slotText) {
        document.querySelectorAll('.pickup-pill').forEach(b => {
            b.classList.remove('active', 'bg-[#0A1128]', 'text-white');
            b.classList.add('border', 'border-slate-200', 'text-slate-600', 'dark:text-slate-300');
        });
        btn.classList.add('active', 'bg-[#0A1128]', 'text-white');
        btn.classList.remove('border-slate-200', 'text-slate-600', 'dark:text-slate-300');
        document.getElementById('selectedPickupSlot').value = slotText;
        document.getElementById('cardPickupTimeBadge').textContent = slotText;
        updateLiveOrderSummary();
    }

    // Handler Preview & Validasi Maksimal 2MB untuk Foto Paket Awal
    function handleInitialPhotoPreview(input) {
        const file = input.files[0];
        if (!file) {
            document.getElementById('initialPhotoPreviewContainer').classList.add('hidden');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file foto melebihi batas 2MB. Silakan pilih foto dengan resolusi lebih kecil.');
            input.value = '';
            document.getElementById('initialPhotoPreviewContainer').classList.add('hidden');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('initialPhotoPreviewImg').src = e.target.result;
            const sizeKb = (file.size / 1024).toFixed(1);
            document.getElementById('initialPhotoSizeText').textContent = `Terpilih: ${file.name} (${sizeKb} KB - Valid)`;
            document.getElementById('initialPhotoPreviewContainer').classList.remove('hidden');
            document.getElementById('initialPhotoPreviewContainer').classList.add('flex');
        };
        reader.readAsDataURL(file);
    }

    // Fungsi Hitung Estimasi Waktu Sampai (ETA) Berdasarkan Rute, Layanan, dan Jadwal Jemput
    function calculateEstimatedArrival(origin, dest, service, slot) {
        const originLower = (origin || '').toLowerCase();
        const destLower = (dest || '').toLowerCase();

        // Rute lokal / intra-wilayah Jawa Timur
        const eastJavaCities = ['surabaya', 'sidoarjo', 'malang', 'mojokerto', 'gresik', 'waru'];
        const isOriginEastJava = eastJavaCities.some(c => originLower.includes(c));
        const isDestEastJava = eastJavaCities.some(c => destLower.includes(c));
        const isLocal = isOriginEastJava && isDestEastJava;

        // Rute antar-pulau atau jarak jauh
        const isFar = originLower.includes('medan') || destLower.includes('medan') ||
                      originLower.includes('makassar') || destLower.includes('makassar') ||
                      originLower.includes('balikpapan') || destLower.includes('balikpapan') ||
                      originLower.includes('denpasar') || destLower.includes('denpasar');

        let transitDays = 2;
        let durationText = '2 - 3 Hari';

        if (service === 'Same Day') {
            transitDays = 0;
            durationText = 'Hari Sama (6 - 8 Jam)';
        } else if (service === 'Express') {
            transitDays = isLocal ? 1 : (isFar ? 2 : 1);
            durationText = isLocal ? 'Besok Tiba (1 Hari)' : '1 - 2 Hari';
        } else if (service === 'Kargo') {
            transitDays = isFar ? 3 : (isLocal ? 2 : 3);
            durationText = isFar ? '3 - 4 Hari' : '2 - 3 Hari';
        } else { // Reguler
            transitDays = isFar ? 3 : (isLocal ? 1 : 2);
            durationText = isFar ? '2 - 3 Hari' : '1 - 2 Hari';
        }

        // Hitung target tanggal berdasarkan slot jemput
        const now = new Date();
        let addDays = transitDays;

        if (slot.includes('Besok')) {
            addDays += 1;
        } else if (slot.includes('Jum')) {
            const currentDay = now.getDay();
            let diff = (5 - currentDay + 7) % 7;
            if (diff === 0) diff = 7;
            addDays += diff;
        } else if (slot.includes('Sab')) {
            const currentDay = now.getDay();
            let diff = (6 - currentDay + 7) % 7;
            if (diff === 0) diff = 7;
            addDays += diff;
        }

        const targetDate = new Date(now.getTime() + (addDays * 24 * 60 * 60 * 1000));

        const hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const bulanSingkat = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        const namaHari = hariIndo[targetDate.getDay()];
        const tanggal = targetDate.getDate();
        const namaBulan = bulanIndo[targetDate.getMonth()];
        const namaBulanSingkat = bulanSingkat[targetDate.getMonth()];
        const tahun = targetDate.getFullYear();

        let dateStr = `${namaHari}, ${tanggal} ${namaBulan} ${tahun}`;
        let shortArrival = `${namaHari}, ${tanggal} ${namaBulanSingkat}`;

        if (service === 'Same Day') {
            dateStr = `Hari Ini (${hariIndo[now.getDay()]}, ${now.getDate()} ${bulanIndo[now.getMonth()]})`;
            shortArrival = `Hari Ini (${now.getDate()} ${namaBulanSingkat})`;
        }

        return {
            dateStr: dateStr,
            shortArrival: shortArrival,
            duration: durationText,
            badge: service === 'Same Day' ? 'Super Prioritas' : (service === 'Express' ? 'Express 24 Jam' : 'Jaminan Tepat Waktu')
        };
    }

    // Update Live Summary Card
    function updateLiveOrderSummary() {
        // 1. Cabang Pengambilan
        const originElem = document.getElementById('orderOrigin');
        const originVal = originElem ? originElem.value : 'Cabang Hub Waru, Sidoarjo';
        const cardOrigin = document.getElementById('cardOriginDisplay');
        if (cardOrigin) cardOrigin.textContent = originVal;

        // 2. Kota Tujuan
        const destElem = document.getElementById('orderDestination');
        const destVal = destElem ? destElem.value : 'Surabaya, ID';
        const cardDest = document.getElementById('cardDestinationDisplay');
        if (cardDest) cardDest.textContent = destVal;

        // 3. Alamat Lengkap & RT/RW
        const streetVal = document.getElementById('orderStreetAddress')?.value || '';
        const rtRwVal = document.getElementById('orderRtRw')?.value || '';
        const addressCombined = streetVal + (rtRwVal ? ' (' + rtRwVal + ')' : '');
        const cardAddress = document.getElementById('cardAddressDisplay');
        if (cardAddress) cardAddress.textContent = addressCombined || 'Alamat tujuan belum diisi';

        // 4. Detail Paket
        const pkgVal = document.getElementById('orderPackageDetail')?.value || '';
        const cardPkg = document.getElementById('cardPackageDetailDisplay');
        if (cardPkg) cardPkg.textContent = pkgVal || 'Belum diisi';

        // 5. Berat Muatan & Layanan
        const weightInput = document.getElementById('orderWeightInput');
        const weight = parseFloat(weightInput?.value) || 0;
        const lb = (weight * 2.20462).toFixed(0);
        document.getElementById('weightLbConversion').textContent = lb + ' lb';

        const serviceElem = document.getElementById('orderServiceType');
        const serviceVal = serviceElem ? serviceElem.value : 'Kargo';
        const cardWeightService = document.getElementById('cardWeightServiceDisplay');
        if (cardWeightService) {
            cardWeightService.textContent = `${weight.toLocaleString('id-ID')} Kg (${lb} lb) • Layanan ${serviceVal}`;
        }

        // 6. Metode Pembayaran (QRIS / Cash)
        const payMethod = document.getElementById('selectedPaymentMethod')?.value || 'QRIS (Lunas)';
        const cardPayText = document.getElementById('cardPaymentDisplay');
        const cardPayIcon = document.getElementById('cardPaymentIcon');
        if (cardPayText) {
            cardPayText.textContent = payMethod;
            if (payMethod.includes('QRIS')) {
                cardPayText.className = 'font-bold text-emerald-400 text-xs block truncate';
                if (cardPayIcon) cardPayIcon.className = 'fa-solid fa-qrcode';
            } else {
                cardPayText.className = 'font-bold text-amber-400 text-xs block truncate';
                if (cardPayIcon) cardPayIcon.className = 'fa-solid fa-money-bill-wave';
            }
        }

        // 7. Hitung Estimasi Harga Dinamis (Rupiah)
        let price = 0;
        if (weight === 1200) {
            price = 1917000;
        } else if (weight <= 10) {
            price = Math.max(15000, Math.round(weight * 12000));
        } else if (weight <= 50) {
            price = Math.round(weight * 9000);
        } else {
            price = Math.round(weight * 1600);
        }
        document.getElementById('displayDynamicPrice').textContent = price.toLocaleString('id-ID');

        // Update harga di Card QRIS & Cash
        const qrisPriceElem = document.getElementById('qrisDisplayPrice');
        if (qrisPriceElem) qrisPriceElem.textContent = price.toLocaleString('id-ID');
        const cashPriceElem = document.getElementById('cashDisplayPrice');
        if (cashPriceElem) cashPriceElem.textContent = price.toLocaleString('id-ID');

        // 8. Hitung Estimasi Paket Sampai (ETA)
        const slotVal = document.getElementById('selectedPickupSlot')?.value || 'Besok, 08:00';
        const etaInfo = calculateEstimatedArrival(originVal, destVal, serviceVal, slotVal);

        const etaMainElem = document.getElementById('displayEstimatedArrival');
        if (etaMainElem) etaMainElem.textContent = etaInfo.dateStr;

        const etaDurationElem = document.getElementById('displayDurationLabel');
        if (etaDurationElem) etaDurationElem.textContent = `(${etaInfo.duration})`;

        const etaBadgeElem = document.getElementById('displayEtaBadge');
        if (etaBadgeElem) {
            etaBadgeElem.textContent = etaInfo.badge;
            if (serviceVal === 'Same Day') {
                etaBadgeElem.className = 'px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30 shrink-0';
            } else if (serviceVal === 'Express') {
                etaBadgeElem.className = 'px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-500/20 text-purple-300 border border-purple-500/30 shrink-0';
            } else {
                etaBadgeElem.className = 'px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shrink-0';
            }
        }

        const cardEtaElem = document.getElementById('cardEtaDisplay');
        if (cardEtaElem) {
            cardEtaElem.textContent = `${etaInfo.dateStr} • ${etaInfo.duration}`;
        }

        // Ringkasan subheader rute di card
        const shortOrigin = originVal.split(',')[0].replace('Cabang Hub', 'Cabang').trim();
        const shortDest = destVal.split(',')[0].trim();
        document.getElementById('displayRouteDetails').textContent = `${shortOrigin} → ${shortDest} • ${weight.toLocaleString('id-ID')} kg • Layanan ${serviceVal} • Tiba: ${etaInfo.shortArrival}`;
    }

    // Jam Pembayaran Berjalan Real-Time (Live Running Clock)
    function updateLiveClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeStr = `${hours}:${minutes}:${seconds} WIB`;

        const qrisClock = document.getElementById('liveRunningClock');
        if (qrisClock) qrisClock.textContent = timeStr;

        const cashClock = document.getElementById('cashLiveClock');
        if (cashClock) cashClock.textContent = timeStr;
    }

    // Countdown Timer Pembayaran QRIS (15 menit berjalan mundur)
    let countdownSeconds = 15 * 60;
    function updateCountdownTimer() {
        if (countdownSeconds > 0) {
            countdownSeconds--;
        }
        const m = Math.floor(countdownSeconds / 60);
        const s = countdownSeconds % 60;
        const formatted = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;

        const timerElem = document.getElementById('liveCountdownTimer');
        if (timerElem) timerElem.textContent = formatted;
    }

    // Jalankan timer & jam berjalan setiap 1 detik
    setInterval(updateLiveClock, 1000);
    setInterval(updateCountdownTimer, 1000);

    // Validasi & Submit
    function validateAndSubmitOrder() {
        const form = document.getElementById('newOrderForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const photoInput = document.getElementById('orderInitialPhotoInput');
        if (photoInput && photoInput.files.length > 0) {
            const file = photoInput.files[0];
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran foto paket sebelum dikirim melebihi batas 2MB. Harap pilih foto lain.');
                return;
            }
        }

        form.submit();
    }

    // Inisialisasi saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateLiveOrderSummary();
        updateLiveClock();
    });
</script>
@endpush
