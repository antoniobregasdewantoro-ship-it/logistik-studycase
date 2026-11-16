@extends('layouts.admin')

@section('title', 'Pesan Pengiriman Baru')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- SUBHEADER (Clean Logistics Style - No ML References) -->
    <div>
        <span class="text-[11px] font-bold tracking-widest uppercase text-[#FF6B00]">
            LANGKAH 1 DARI 2 • DETAIL PENGIRIMAN
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-[#0A1128] dark:text-white tracking-tight mt-1">
            Pesan pengiriman, harga real-time.
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 max-w-2xl">
            Lengkapi data cabang pengambilan asal, alamat tujuan, detail paket, dan muatan kargo Anda. Sistem menghitung tarif pengiriman dan menyiapkan manifest armada secara real-time.
        </p>
    </div>

    <!-- MAIN TWO-COLUMN: FORM KIRI & LIVE SUMMARY CARD KANAN -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- ============================================================== -->
        <!-- KOLOM KIRI: FORM DETAIL PENGIRIMAN                             -->
        <!-- ============================================================== -->
        <div class="lg:col-span-7 bg-white dark:bg-[#0D1527] rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm transition">
            <form id="newOrderForm" action="{{ route('admin.orders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- PENGIRIM & PENERIMA -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">NAMA PENGIRIM *</label>
                        <input type="text" name="sender_name" id="orderSender" value="{{ auth()->user()->name ?? 'Nadia Kessler' }}" required
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
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">NO. TELEPON / WHATSAPP PENERIMA</label>
                    <input type="text" name="recipient_phone" id="orderPhone" placeholder="Contoh: 081234567890" value="081298765432"
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
                        <input type="text" name="street_address" id="orderStreetAddress" value="Kawasan Industri Rungkut Megah No. 42" required
                               oninput="updateLiveOrderSummary()"
                               placeholder="Nama Jalan, Nomor Gedung/Rumah, Blok..."
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">RT / RW</label>
                        <input type="text" name="rt_rw" id="orderRtRw" value="RT 03 / RW 05"
                               oninput="updateLiveOrderSummary()"
                               placeholder="Contoh: RT 03 / RW 05"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                </div>

                <!-- DETAIL / ISI PAKET -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">
                        <i class="fa-solid fa-box-open text-[#FF6B00] mr-1"></i> DETAIL / DESKRIPSI ISI PAKET *
                    </label>
                    <input type="text" name="package_detail" id="orderPackageDetail" value="Komponen Mesin Industri & Sparepart Otomotif" required
                           oninput="updateLiveOrderSummary()"
                           placeholder="Contoh: Pakaian, Sparepart Mesin, Dokumen Arsip, Makanan Kering..."
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                </div>

                <!-- FOTO PAKET SEBELUM DIKIRIM (KONDISI AWAL HUB, MAKS 2MB) -->
                <div class="bg-slate-50 dark:bg-white/5 rounded-2xl p-4 border border-slate-200 dark:border-white/10 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-[10px] font-extrabold text-slate-600 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-camera text-[#FF6B00]"></i>
                            <span>FOTO PAKET SEBELUM DIKIRIM (DOKUMENTASI KONDISI AWAL DI CABANG)</span>
                        </label>
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30">
                            Maks. 2MB
                        </span>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="relative flex-1">
                            <input type="file" name="initial_photo" id="orderInitialPhotoInput" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   onchange="handleInitialPhotoPreview(this)"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-[#FF6B00] file:text-white hover:file:bg-[#E55F00] file:cursor-pointer cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-[#141E3D]">
                        </div>
                        <div id="initialPhotoPreviewContainer" class="hidden shrink-0">
                            <img id="initialPhotoThumb" src="" alt="Preview Paket" class="w-12 h-12 rounded-xl object-cover border-2 border-[#FF6B00] shadow-sm">
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400">
                        Format file yang didukung: JPG, PNG, WEBP. Batas ukuran ketat: 2048 KB (2MB). Foto ini akan menjadi arsip resmi kondisi paket saat diserahkan di cabang sebelum diantar kurir.
                    </p>
                </div>

                <!-- BERAT (Input Angka Besar + lb converter) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">BERAT MUATAN</label>
                        <span class="text-[10px] font-bold text-slate-400">kg</span>
                    </div>
                    <div class="relative flex items-center">
                        <input type="number" id="orderWeightInput" name="weight" step="1" min="1" value="1200" required
                               oninput="updateLiveOrderSummary()"
                               class="w-full px-5 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xl sm:text-2xl font-black text-[#0A1128] focus:outline-none focus:border-[#FF6B00]">
                        <span id="weightLbConversion" class="absolute right-5 text-xs font-bold text-slate-400">
                            2646 lb
                        </span>
                    </div>
                </div>

                <!-- PILIHAN LAYANAN & KURIR -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">JENIS LAYANAN</label>
                        <select name="service_type" id="orderServiceType" onchange="updateLiveOrderSummary()"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                            <option value="Kargo" selected>Kargo (Armada Truk & Muatan Berat)</option>
                            <option value="Express">Express (1-2 Hari Kerja)</option>
                            <option value="Same Day">Same Day (Hari Ini Tiba)</option>
                            <option value="Reguler">Reguler (Standard Delivery)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">TUGASKAN KURIR ARMADA</label>
                        <select name="courier_id" id="orderCourierSelect" onchange="updateLiveOrderSummary()"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                            @if(isset($couriers) && $couriers->count() > 0)
                                @foreach($couriers as $courier)
                                    <option value="{{ $courier->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $courier->name }} ({{ $courier->phone ?? 'Aktif' }})
                                    </option>
                                @endforeach
                            @else
                                <option value="">Otomatis dipilihkan kurir terdekat</option>
                            @endif
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
                                    <span class="text-xs font-black text-[#0A1128] dark:text-white">QRIS (Lunas)</span>
                                    <span id="badgePayQris" class="w-2.5 h-2.5 rounded-full bg-[#FF6B00]"></span>
                                </div>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">Pembayaran instan kode QR</span>
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
                            <!-- QR Code Box -->
                            <div class="bg-white p-3 rounded-2xl shadow-lg border border-white/20 text-center shrink-0">
                                <img id="qrisBarcodeImage"
                                     src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=00020101021226670016ID.CO.MERIDIAN.WWW0118936009990000000001520454995303360540"
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

                <!-- WAKTU PENGIRIMAN PAKET (Pill buttons persis Gambar 1) -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">JADWAL WAKTU PENGIRIMAN PAKET</label>
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

            </form>
        </div>

        <!-- ============================================================== -->
        <!-- KOLOM KANAN: CARD INFORMASI DARI INPUT FORM (LIVE SUMMARY)     -->
        <!-- Sesuai permintaan user: card menampilkan data yang diisi tadi  -->
        <!-- ============================================================== -->
        <div class="lg:col-span-5 bg-[#0D1527] rounded-3xl p-6 sm:p-8 text-white shadow-2xl border border-white/5 relative overflow-hidden">

            <!-- Top Tag: Rincian Manifest Real-Time -->
            <div class="flex items-center justify-between pb-4 border-b border-white/10 mb-6">
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

            <!-- KOTAK INFORMASI LENGKAP YANG DIISI USER (LIVE SUMMARY CARD) -->
            <div class="mt-6 p-5 rounded-2xl bg-white/5 border border-white/10 space-y-4">
                <div class="flex items-center justify-between pb-2.5 border-b border-white/10">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#FF6B00] flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Informasi Paket & Rute Terisi</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono" id="cardPickupTimeBadge">
                        Besok, 08:00
                    </span>
                </div>

                <div class="space-y-3 text-xs">
                    <!-- Cabang Pengambilan Paket (Hub Asal) -->
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-orange-500/20 text-[#FF6B00] flex items-center justify-center shrink-0 text-xs mt-0.5">
                            <i class="fa-solid fa-warehouse"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Cabang Pengambilan (Hub Asal):</span>
                            <span id="cardOriginDisplay" class="font-bold text-white text-xs block truncate">Cabang Hub Waru, Sidoarjo</span>
                        </div>
                    </div>

                    <!-- Tujuan Pengiriman & Alamat Lengkap -->
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-xs mt-0.5">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Alamat Tujuan Pengiriman:</span>
                            <span id="cardDestinationDisplay" class="font-bold text-white text-xs block">Surabaya, ID</span>
                            <span id="cardAddressDisplay" class="text-[11px] text-slate-300 block truncate">Kawasan Industri Rungkut Megah No. 42 (RT 03 / RW 05)</span>
                        </div>
                    </div>

                    <!-- Detail Isi Paket -->
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0 text-xs mt-0.5">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Detail Isi Paket:</span>
                            <span id="cardPackageDetailDisplay" class="font-bold text-white text-xs block truncate">Komponen Mesin Industri & Sparepart Otomotif</span>
                        </div>
                    </div>

                    <!-- Berat Muatan & Layanan -->
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center shrink-0 text-xs mt-0.5">
                            <i class="fa-solid fa-weight-hanging"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Berat & Jenis Layanan:</span>
                            <span id="cardWeightServiceDisplay" class="font-bold text-white text-xs block">1.200 Kg (2646 lb) • Layanan Kargo</span>
                        </div>
                    </div>

                    <!-- Estimasi Paket Sampai di Penerima -->
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 text-xs mt-0.5">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Estimasi Paket Sampai di Penerima:</span>
                            <span id="cardEtaDisplay" class="font-bold text-amber-300 text-xs block">Kamis, 12 Nov 2026 • Perkiraan 3 - 4 Hari Kerja</span>
                        </div>
                    </div>

                    <!-- Kurir Ditugaskan -->
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 text-xs mt-0.5">
                            <i class="fa-solid fa-motorcycle"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Kurir Ditugaskan:</span>
                            <span id="cardCourierDisplay" class="font-bold text-white text-xs block truncate">Budi Santoso (Kurir Armada)</span>
                        </div>
                    </div>

                    <!-- Metode Pembayaran Terpilih -->
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-xs mt-0.5">
                            <i id="cardPaymentIcon" class="fa-solid fa-qrcode"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Metode Pembayaran:</span>
                            <span id="cardPaymentDisplay" class="font-bold text-emerald-400 text-xs block truncate">QRIS (Lunas)</span>
                        </div>
                    </div>

                    <!-- Foto Paket Sebelum Dikirim Preview di Card -->
                    <div class="pt-2 border-t border-white/10">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-image text-slate-400"></i>
                            <span>Foto Paket Sebelum Dikirim (Max 2MB):</span>
                        </span>
                        <div id="cardPhotoPlaceholder" class="p-2.5 rounded-xl bg-white/5 border border-dashed border-white/20 text-center text-[11px] text-slate-400">
                            <i class="fa-solid fa-cloud-arrow-up text-base text-slate-500 block mb-1"></i>
                            <span>Belum ada foto paket dipilih (opsional)</span>
                        </div>
                        <div id="cardPhotoPreviewWrap" class="hidden flex items-center gap-3 p-2 rounded-xl bg-white/10 border border-white/20">
                            <img id="cardPhotoPreviewImg" src="" alt="Foto Paket" class="w-14 h-14 rounded-lg object-cover border border-[#FF6B00]">
                            <div class="text-[11px]">
                                <span class="font-bold text-white block">Foto Terverifikasi</span>
                                <span id="cardPhotoSizeText" class="text-[10px] text-emerald-400 font-mono block">Ukuran valid &lt; 2MB</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FASILITAS & JAMINAN LAYANAN RESMI MERIDIAN -->
            <div class="mt-4 p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-300">
                    <span class="flex items-center gap-1.5 font-bold">
                        <i class="fa-solid fa-shield-halved text-[#FF6B00]"></i> Proteksi Kargo Resmi
                    </span>
                    <span class="text-[10px] text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                        Garansi 100%
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Manifest pesanan langsung tersinkronisasi ke kurir armada dan nomor resi terbit otomatis.
                </p>
            </div>

            <!-- TOMBOL PESAN PENGIRIMAN -->
            <div class="mt-6 space-y-3">
                <button type="button" onclick="validateAndSubmitOrder()"
                        class="w-full py-4 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white font-extrabold text-sm transition shadow-lg shadow-[#FF6B00]/40 flex items-center justify-center gap-2 cursor-pointer">
                    <span>Pesan Pengiriman</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

                <p class="text-[11px] text-center text-slate-400">
                    Data tersimpan langsung di sistem • Resi otomatis aktif
                </p>
            </div>
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

    // Fungsi Hitung Estimasi Waktu Sampai (ETA) Berdasarkan Rute, Layanan, dan Jadwal Jemput
    function calculateEstimatedArrival(origin, dest, service, slot) {
        const originLower = (origin || '').toLowerCase();
        const destLower = (dest || '').toLowerCase();

        const eastJavaCities = ['surabaya', 'sidoarjo', 'malang', 'mojokerto', 'gresik', 'waru'];
        const isOriginEastJava = eastJavaCities.some(c => originLower.includes(c));
        const isDestEastJava = eastJavaCities.some(c => destLower.includes(c));
        const isLocal = isOriginEastJava && isDestEastJava;

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
        } else {
            transitDays = isFar ? 3 : (isLocal ? 1 : 2);
            durationText = isFar ? '2 - 3 Hari' : '1 - 2 Hari';
        }

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

    // Handler Preview & Validasi Maksimal 2MB untuk Foto Paket Awal
    function handleInitialPhotoPreview(input) {
        const file = input.files[0];
        if (!file) {
            document.getElementById('initialPhotoPreviewContainer').classList.add('hidden');
            document.getElementById('cardPhotoPlaceholder').classList.remove('hidden');
            document.getElementById('cardPhotoPreviewWrap').classList.add('hidden');
            return;
        }

        // Cek ukuran file max 2MB (2 * 1024 * 1024 = 2,097,152 bytes)
        const maxBytes = 2 * 1024 * 1024;
        if (file.size > maxBytes) {
            alert('Perhatian: Ukuran file foto paket melebihi batas maksimal 2MB (' + (file.size / 1024 / 1024).toFixed(2) + ' MB). Silakan pilih foto dengan ukuran lebih kecil dari 2MB.');
            input.value = '';
            document.getElementById('initialPhotoPreviewContainer').classList.add('hidden');
            document.getElementById('cardPhotoPlaceholder').classList.remove('hidden');
            document.getElementById('cardPhotoPreviewWrap').classList.add('hidden');
            return;
        }

        // Baca file gambar dan tampilkan thumbnail di form dan di Card Rincian
        const reader = new FileReader();
        reader.onload = function(e) {
            // Preview di Form
            document.getElementById('initialPhotoThumb').src = e.target.result;
            document.getElementById('initialPhotoPreviewContainer').classList.remove('hidden');

            // Preview di Card Kanan
            document.getElementById('cardPhotoPreviewImg').src = e.target.result;
            const kbSize = (file.size / 1024).toFixed(1) + ' KB';
            document.getElementById('cardPhotoSizeText').textContent = 'Ukuran valid: ' + kbSize + ' (Maks 2MB)';
            document.getElementById('cardPhotoPlaceholder').classList.add('hidden');
            document.getElementById('cardPhotoPreviewWrap').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    // Update Real-Time Card Rincian Paket
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

        // 6. Kurir Ditugaskan
        const courierElem = document.getElementById('orderCourierSelect');
        const courierText = courierElem && courierElem.selectedIndex >= 0 ? courierElem.options[courierElem.selectedIndex].text : 'Belum dipilih';
        const cardCourier = document.getElementById('cardCourierDisplay');
        if (cardCourier) cardCourier.textContent = courierText;

        // 6b. Metode Pembayaran (QRIS / Cash)
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
