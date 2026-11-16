@extends('layouts.app')

@section('title', 'Meridian Logistics - Solusi Logistik Modern & Tracking Cerdas')

@section('content')

<!-- ===================================== -->
<!-- CSS CUSTOM / THEME STYLING            -->
<!-- ===================================== -->
<style>
:root {
    --orange: #FF6B00;
    --orange-hover: #e66000;
    --dark-blue: #0A1128;
    --text-gray: #64748b;
    --border-color: #e2e8f0;
    --bg-light: #f8fafc;
    --shadow-card: 0 20px 50px -12px rgba(0, 0, 0, 0.08);
}

.hero-section {
    position: relative;
    padding: 60px 20px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    overflow: hidden;
}

.glow-bg {
    position: absolute;
    top: 25%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 650px;
    height: 650px;
    background-color: rgba(255, 107, 0, 0.08);
    filter: blur(120px);
    border-radius: 50%;
    pointer-events: none;
}

.tab-menu {
    display: flex;
    border-radius: 18px;
    padding: 6px;
    margin-bottom: 24px;
}

.tab-btn {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.tab-btn.active {
    background: #0A1128;
    color: #fff;
    box-shadow: 0 4px 12px rgba(10, 17, 40, 0.15);
}
.dark .tab-btn.active {
    background: #FF6B00;
    color: #fff;
}

/* Map SVG styling */
.map-section {
    max-width: 1200px;
    margin: 60px auto 0;
    padding: 0 20px;
    text-align: center;
}
.map-container {
    position: relative;
    max-width: 1000px;
    margin: 40px auto 60px;
}
.map-svg {
    width: 100%;
    height: auto;
}
.map-pin-wrapper {
    position: absolute;
    transform: translate(-50%, -50%);
    cursor: pointer;
    z-index: 20;
}
.map-pin {
    position: relative;
    width: 16px;
    height: 16px;
}
.pin-pulse {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #FF6B00;
    border-radius: 50%;
    opacity: 0.75;
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}
.pin-core {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 10px;
    height: 10px;
    background-color: #FF6B00;
    border-radius: 50%;
    border: 2px solid #fff;
}
.map-tooltip {
    position: absolute;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    display: none;
    white-space: nowrap;
    z-index: 30;
}
.map-pin-wrapper:hover .map-tooltip {
    display: block;
}
.tooltip-box {
    background: #0A1128;
    color: white;
    padding: 6px 12px;
    border-radius: 10px;
    font-size: 11px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
@keyframes ping {
    75%, 100% {
        transform: scale(2.2);
        opacity: 0;
    }
}
</style>

<!-- ===================================== -->
<!-- 1. HERO SECTION (Lacak & Cek Tarif)   -->
<!-- ===================================== -->
<div class="hero-section">
    <div class="glow-bg"></div>

    <div class="hero-content w-full max-w-4xl mx-auto">
        <!-- Promosi Badge Dinamis -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-orange-50 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-800/40 text-[#FF6B00] text-xs font-bold mb-4 shadow-sm">
            <i class="fa-solid fa-satellite-dish"></i>
            <span>{{ $heroContent->badge_text ?? 'SISTEM LOGISTIK & PELACAKAN REAL-TIME TERINTEGRASI' }}</span>
        </div>

        <!-- Headline Dinamis -->
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-[#0A1128] dark:text-white tracking-tight leading-tight mb-4">
            {{ $heroContent->title ?? 'Kirim Paket Cepat, Aman, dan Tepat Waktu.' }}
        </h1>

        <!-- Subtitle Dinamis -->
        <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base max-w-2xl mx-auto mb-8 leading-relaxed">
            {{ $heroContent->subtitle ?? 'Layanan ekspedisi terpercaya ke seluruh pelosok Nusantara. Lacak keberadaan paket Anda secara live melalui peta atau cek kalkulasi estimasi tarif otomatis.' }}
        </p>

        <!-- KARTU INTERAKTIF: KALKULATOR ESTIMASI TARIF ONGKIR DINAMIS -->
        <div id="kalkulator" class="bg-white dark:bg-[#0D1527] p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-100 dark:border-white/10 max-w-3xl w-full mx-auto text-left transition-all relative overflow-hidden">

            <!-- Header Kartu -->
            <div class="flex items-center justify-between pb-5 border-b border-slate-100 dark:border-white/10 mb-6">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-orange-500/10 text-[#FF6B00] flex items-center justify-center text-2xl font-bold shadow-sm">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-[#FF6B00] block mb-0.5">Estimasi Biaya Pengiriman Real-Time</span>
                        <h3 class="text-lg sm:text-xl font-black text-[#0A1128] dark:text-white">Kalkulator Tarif Ongkir Cerdas</h3>
                    </div>
                </div>
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Tarif Resmi {{ date('Y') }}</span>
                </span>
            </div>

            <!-- Form Kalkulator Ongkir -->
            <form id="publicTariffForm" onsubmit="event.preventDefault(); calculateLandingTariff();" class="space-y-4">
                <!-- Rute Asal & Tujuan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            <i class="fa-solid fa-location-arrow text-[#FF6B00] mr-1"></i> Kota Asal Pengiriman
                        </label>
                        <div class="relative">
                            <select id="tariffOrigin" class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-xs sm:text-sm font-bold text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00]">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->city }}" {{ $branch->city === 'Sidoarjo' ? 'selected' : '' }}>
                                        {{ $branch->name }} ({{ $branch->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            <i class="fa-solid fa-location-dot text-[#FF6B00] mr-1"></i> Kota Tujuan Pengiriman
                        </label>
                        <div class="relative">
                            <select id="tariffDestination" class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-xs sm:text-sm font-bold text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00]">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->city }}" {{ $branch->city === 'Surabaya' ? 'selected' : '' }}>
                                        {{ $branch->name }} ({{ $branch->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Berat Paket & Pilihan Layanan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-weight-scale text-[#FF6B00] mr-1"></i> Berat Paket (Kg) *
                            </label>
                            <span class="text-[10px] text-slate-400 font-mono">Min. 0.1 Kg</span>
                        </div>
                        <div class="relative flex items-center">
                            <button type="button" onclick="adjustWeight(-0.5)" class="absolute left-2 w-8 h-8 rounded-xl bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 font-bold hover:bg-[#FF6B00] hover:text-white transition flex items-center justify-center">
                                -
                            </button>
                            <input type="number" id="landingWeight" name="weight" step="0.1" min="0.1" value="2.0" required
                                   oninput="calculateLandingTariff()"
                                   class="w-full text-center px-12 py-3 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-sm font-black text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00]">
                            <button type="button" onclick="adjustWeight(0.5)" class="absolute right-2 w-8 h-8 rounded-xl bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 font-bold hover:bg-[#FF6B00] hover:text-white transition flex items-center justify-center">
                                +
                            </button>
                        </div>
                        <!-- Quick Weight Chips -->
                        <div class="flex items-center gap-1.5 mt-2">
                            <button type="button" onclick="setWeight(1)" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-orange-100 hover:text-[#FF6B00] text-[11px] font-bold text-slate-600 dark:text-slate-400 transition">1 Kg</button>
                            <button type="button" onclick="setWeight(2)" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-orange-100 hover:text-[#FF6B00] text-[11px] font-bold text-slate-600 dark:text-slate-400 transition">2 Kg</button>
                            <button type="button" onclick="setWeight(5)" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-orange-100 hover:text-[#FF6B00] text-[11px] font-bold text-slate-600 dark:text-slate-400 transition">5 Kg</button>
                            <button type="button" onclick="setWeight(10)" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-orange-100 hover:text-[#FF6B00] text-[11px] font-bold text-slate-600 dark:text-slate-400 transition">10 Kg</button>
                            <button type="button" onclick="setWeight(20)" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/5 hover:bg-orange-100 hover:text-[#FF6B00] text-[11px] font-bold text-slate-600 dark:text-slate-400 transition">20 Kg</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            <i class="fa-solid fa-truck-fast text-[#FF6B00] mr-1"></i> Jenis Layanan Pengiriman *
                        </label>
                        <select id="landingService" name="service" required onchange="calculateLandingTariff()"
                                class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-[#0D1527] border border-slate-200 dark:border-white/10 text-xs sm:text-sm font-bold text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00]">
                            <option value="Reguler">Meridian Reguler (Rp 9.000 / Kg • 2-3 Hari)</option>
                            <option value="Express">Meridian Express (Rp 15.000 / Kg • Next Day)</option>
                            <option value="Kargo">Meridian Kargo (Rp 6.000 / Kg • Muatan Berat)</option>
                            <option value="Same Day">Meridian Same Day (Rp 20.000 / Kg • Hari Sama)</option>
                        </select>
                        <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                            <span>Termasuk asuransi standar, barcode resi resmi & pembayaran QRIS.</span>
                        </p>
                    </div>
                </div>

                <!-- HASIL ESTIMASI ONGKIR (REAL-TIME KALKULASI DINAMIS) -->
                <div class="p-5 rounded-2xl bg-gradient-to-r from-orange-50 via-orange-50/50 to-transparent dark:from-orange-950/40 dark:via-orange-950/20 border border-orange-200 dark:border-orange-800/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-2">
                    <div>
                        <span class="text-[11px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">
                            TOTAL ESTIMASI ONGKIR
                        </span>
                        <div class="flex items-baseline gap-2">
                            <span id="landingTariffOutput" class="text-3xl sm:text-4xl font-black text-[#FF6B00]">
                                Rp 18.000
                            </span>
                        </div>
                        <p id="landingTariffDetail" class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                            2.0 Kg × Rp 9.000 (Reguler) • Estimasi 2-3 hari
                        </p>
                    </div>

                    <div class="shrink-0">
                        @auth
                            <a href="{{ route('customer.dashboard') }}"
                               class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-[#0A1128] dark:bg-white text-white dark:text-[#0A1128] font-black text-xs uppercase tracking-wider transition shadow-lg hover:scale-105 flex items-center justify-center gap-2">
                                <span>Pesan Pengiriman</span>
                                <i class="fa-solid fa-arrow-right text-[#FF6B00]"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white font-black text-xs uppercase tracking-wider transition shadow-lg shadow-[#FF6B00]/40 hover:scale-105 flex items-center justify-center gap-2">
                                <span>Pesan Pengiriman Sekarang</span>
                                <i class="fa-solid fa-arrow-right text-white"></i>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Footer Informasi Kalkulasi Ongkir -->
                <div class="pt-4 border-t border-slate-100 dark:border-white/5 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-500 dark:text-slate-400 gap-2">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i>
                        <span>Formula: Berat Paket (Kg) × Tarif Layanan Dasar</span>
                    </span>
                    <span class="text-slate-400 dark:text-slate-500 text-[11px]">
                        <i class="fa-solid fa-bolt text-[#FF6B00]"></i>
                        Kalkulasi tarif otomatis & instan
                    </span>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- 2. ANIMATED SHOWCASE CAROUSEL (BERGANTI OTOMATIS 4 DETIK) -->
<!-- ======================================================== -->
@php
    $slidesData = $carouselContent->extra_data ?? [
        [
            'tag' => 'ARMADA CEPAT',
            'title' => 'Armada Pickup & Kargo Antar Kota',
            'desc' => 'Layanan Same-Day dan Next-Day dengan armada van dan sepeda motor berinsulasi tinggi, menghubungkan pusat bisnis dan pergudangan dengan cepat.',
            'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80',
        ],
        [
            'tag' => 'LIVE TRACKING',
            'title' => 'Pemantauan Satelit & OpenStreetMap',
            'desc' => 'GPS real-time terhubung langsung ke ponsel kurir lapangan dan command center drop point untuk ketepatan waktu pengiriman optimal.',
            'image' => 'https://images.unsplash.com/photo-1519003722824-194d4455a60c?auto=format&fit=crop&w=1200&q=80',
        ],
        [
            'tag' => 'VERIFIKASI DIGITAL',
            'title' => 'Bukti Foto Serah Terima & Barcode Resi',
            'desc' => 'Penerima dan pengirim dapat langsung mengecek foto bukti serah terima asli yang diupload kurir saat serah terima di alamat tujuan.',
            'image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1200&q=80',
        ]
    ];
@endphp

<section id="showcase" class="w-full max-w-6xl mx-auto mt-20 px-4">
    <div class="text-center max-w-2xl mx-auto mb-10">
        <span class="text-xs font-black tracking-widest uppercase text-[#FF6B00] block mb-2">
            SHOWCASE ARMADA & JARINGAN
        </span>
        <h2 class="text-3xl sm:text-4xl font-black text-[#0A1128] dark:text-white tracking-tight">
            {{ $carouselContent->title ?? 'Armada & Jaringan Pengiriman Modern' }}
        </h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-2">
            {{ $carouselContent->subtitle ?? 'Teknologi otomasi logistik terdepan menghubungkan pulau-pulau di Indonesia.' }}
        </p>
    </div>

    <!-- CAROUSEL WRAPPER WITH AUTO-PLAY ANIMATION -->
    <div class="relative bg-white dark:bg-[#0D1527] rounded-3xl overflow-hidden shadow-2xl border border-slate-100 dark:border-white/10 group">

        <!-- Slides Container -->
        <div id="carouselSlides" class="relative w-full h-[400px] sm:h-[460px] overflow-hidden">
            @foreach($slidesData as $idx => $slide)
                <div class="carousel-slide absolute inset-0 transition-all duration-700 ease-in-out {{ $idx === 0 ? 'opacity-100 scale-100 pointer-events-auto' : 'opacity-0 scale-95 pointer-events-none' }}"
                     data-slide-index="{{ $idx }}">
                    @php
                        $mediaUrl = $slide['image'];
                        if (str_contains($mediaUrl, 'storage/')) {
                            $mediaUrl = asset(substr($mediaUrl, strpos($mediaUrl, 'storage/')));
                        }
                    @endphp
                    @if(preg_match('/\.(mp4|webm|ogg|mov)$/i', $mediaUrl))
                        <video src="{{ $mediaUrl }}" autoplay loop muted playsinline class="w-full h-full object-cover"></video>
                    @else
                        <img src="{{ $mediaUrl }}" alt="{{ $slide['title'] }}" class="w-full h-full object-cover">
                    @endif
                    <!-- Gradient Dark Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0A1128] via-[#0A1128]/50 to-transparent"></div>

                    <!-- Slide Content Caption -->
                    <div class="absolute bottom-8 left-6 sm:left-12 right-6 sm:right-12 text-white space-y-2 z-10">
                        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-[#FF6B00] text-white shadow-md">
                            {{ $slide['tag'] ?? 'LOGISTIK TERPADU' }}
                        </span>
                        <h3 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                            {{ $slide['title'] }}
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-200 max-w-2xl leading-relaxed">
                            {{ $slide['desc'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Controls: Next & Prev Arrows -->
        <button type="button" onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/40 hover:bg-[#FF6B00] text-white flex items-center justify-center transition backdrop-blur-sm z-20">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </button>
        <button type="button" onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/40 hover:bg-[#FF6B00] text-white flex items-center justify-center transition backdrop-blur-sm z-20">
            <i class="fa-solid fa-chevron-right text-sm"></i>
        </button>

        <!-- Bottom Indicators & Progress Bar -->
        <div class="absolute top-4 right-6 z-20 flex items-center gap-2 bg-black/50 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/20">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-[11px] font-bold text-white">Auto-Play:</span>
            <div class="flex items-center gap-1.5 ml-1">
                @foreach($slidesData as $idx => $slide)
                    <button type="button" onclick="goToSlide({{ $idx }})"
                            id="indicator-dot-{{ $idx }}"
                            class="w-2.5 h-2.5 rounded-full transition-all {{ $idx === 0 ? 'bg-[#FF6B00] w-6' : 'bg-white/40' }}"></button>
                @endforeach
            </div>
        </div>

        <!-- Auto-Cycle Countdown Progress Line -->
        <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-white/20 z-20">
            <div id="carouselProgressBar" class="h-full bg-[#FF6B00] w-0 transition-all duration-[4000ms] ease-linear"></div>
        </div>
    </div>
</section>

<!-- ===================================== -->
<!-- 3. SEKSI FITUR KEUNGGULAN MERIDIAN    -->
<!-- ===================================== -->
<section id="layanan" class="w-full max-w-6xl mx-auto mt-24 text-left px-4">
    <div class="text-center max-w-2xl mx-auto mb-16">
        <span class="text-xs font-black tracking-widest uppercase text-[#FF6B00] block mb-2">LOGISTIK MODERN</span>
        <h2 class="text-3xl sm:text-4xl font-black text-[#0A1128] dark:text-white tracking-tight">Kecerdasan di Setiap Kilometer</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-3">Platform manajemen armada terdepan dengan kalkulasi tarif real-time, prediksi keterlambatan rute, dan tracking presisi.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 01 -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-8 border border-slate-100 dark:border-white/5 shadow-sm hover:shadow-xl transition flex flex-col justify-between">
            <div>
                <span class="font-mono text-xs font-bold text-slate-400 block mb-4">01 • KALKULATOR DINAMIS</span>
                <h3 class="text-xl font-black text-[#0A1128] dark:text-white mb-3">Tarif Transparan & Otomatis</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed">
                    Perhitungan ongkos kirim presisi berbasis perkalian berat dan kelas layanan, memastikan efisiensi biaya maksimal bagi pengirim skala retail maupun industri.
                </p>
            </div>
            <div class="mt-8 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs font-bold text-[#FF6B00]">
                <span>Hitung Instan</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </div>

        <!-- Card 02 -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-8 border border-slate-100 dark:border-white/5 shadow-sm hover:shadow-xl transition flex flex-col justify-between">
            <div>
                <span class="font-mono text-xs font-bold text-slate-400 block mb-4">02 • RISK PREDICTION</span>
                <h3 class="text-xl font-black text-[#0A1128] dark:text-white mb-3">Peringatan Keterlambatan</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed">
                    Sistem deteksi dini potensi hambatan rute, cuaca, dan kepadatan jalan untuk mengamankan ketepatan estimasi waktu tiba (SLA) secara konsisten.
                </p>
            </div>
            <div class="mt-8 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs font-bold text-[#FF6B00]">
                <span>Aman & Terkendali</span>
                <i class="fa-solid fa-shield-check"></i>
            </div>
        </div>

        <!-- Card 03 -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-8 border border-slate-100 dark:border-white/5 shadow-sm hover:shadow-xl transition flex flex-col justify-between">
            <div>
                <span class="font-mono text-xs font-bold text-slate-400 block mb-4">03 • LIVE OPENSTREETMAP</span>
                <h3 class="text-xl font-black text-[#0A1128] dark:text-white mb-3">Pelacakan Real-Time</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed">
                    Setiap titik pergerakan armada kurir terpetakan langsung dengan koordinat GPS akurat, barcode resi pengiriman standar logistik, dan foto bukti serah terima.
                </p>
            </div>
            <div class="mt-8 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs font-bold text-[#FF6B00]">
                <span>Live Tracking Map</span>
                <i class="fa-solid fa-location-dot"></i>
            </div>
        </div>
    </div>
</section>

<!-- ===================================== -->
<!-- 4. SEKSI JARINGAN HUB (13 KOTA)       -->
<!-- ===================================== -->
<section id="cabang" class="w-full max-w-6xl mx-auto mt-24 text-center px-4">
    <span class="text-xs font-black tracking-widest uppercase text-[#FF6B00] block mb-2">JARINGAN LOGISTIK</span>
    <h2 class="text-3xl sm:text-4xl font-black text-[#0A1128] dark:text-white tracking-tight">Menjangkau {{ $jumlahKota ?? 13 }}+ Kota di Seluruh Indonesia</h2>
    <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 max-w-xl mx-auto">Didukung oleh jaringan armada pickup, truk kargo antar pulau, dan kurir motor terintegrasi.</p>

    <!-- PETA SVG INDONESIA -->
    <div class="map-container">
        <svg class="map-svg" viewBox="0 0 1049 388" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M498.02 2.49927H498.031M506.713 2.49927H506.724M489.326 11.1926H489.337M498.02 11.1926H498.031M506.713 11.1926H506.724M515.406 11.1926H515.417M471.94 19.8859H471.951M480.633 19.8859H480.644M489.326 19.8859H489.337M498.02 19.8859H498.031M506.713 19.8859H506.724M515.406 19.8859H515.417M2.49939 28.5793H2.51026M11.1927 28.5793H11.2036M463.246 28.5793H463.257M471.94 28.5793H471.951M480.633 28.5793H480.644M489.326 28.5793H489.337M498.02 28.5793H498.031M506.713 28.5793H506.724M515.406 28.5793H515.417M524.1 28.5793H524.111M11.1927 37.2726H11.2036M19.8861 37.2726H19.8969M28.5794 37.2726H28.5903M37.2728 37.2726H37.2836M45.9661 37.2726H45.977M454.553 37.2726H454.564M463.246 37.2726H463.257M471.94 37.2726H471.951M480.633 37.2726H480.644M489.326 37.2726H489.337M498.02 37.2726H498.031M506.713 37.2726H506.724M515.406 37.2726H515.417M524.1 37.2726H524.111M532.793 37.2726H532.804M11.1927 45.966H11.2036M19.8861 45.966H19.8969M28.5794 45.966H28.5903M37.2728 45.966H37.2836M45.9661 45.966H45.977M54.6594 45.966H54.6703M445.86 45.966H445.871M454.553 45.966H454.564M463.246 45.966H463.257M471.94 45.966H471.951M480.633 45.966H480.644M489.326 45.966H489.337M498.02 45.966H498.031M506.713 45.966H506.724M515.406 45.966H515.417M524.1 45.966H524.111M532.793 45.966H532.804M11.1927 54.6593H11.2036M19.8861 54.6593H19.8969M28.5794 54.6593H28.5903M37.2728 54.6593H37.2836M45.9661 54.6593H45.977M54.6594 54.6593H54.6703M63.3528 54.6593H63.3636M72.0461 54.6593H72.057M428.473 54.6593H428.484M437.166 54.6593H437.177M445.86 54.6593H445.871M454.553 54.6593H454.564M463.246 54.6593H463.257M471.94 54.6593H471.951M480.633 54.6593H480.644M489.326 54.6593H489.337M498.02 54.6593H498.031M506.713 54.6593H506.724M515.406 54.6593H515.417M524.1 54.6593H524.111M19.8861 63.3526H19.8969M28.5794 63.3526H28.5903M37.2728 63.3526H37.2836M45.9661 63.3526H45.977M54.6594 63.3526H54.6703M63.3528 63.3526H63.3636M72.0461 63.3526H72.057M80.7395 63.3526H80.7503M419.78 63.3526H419.791M428.473 63.3526H428.484M437.166 63.3526H437.177M445.86 63.3526H445.871M454.553 63.3526H454.564M463.246 63.3526H463.257M471.94 63.3526H471.951M480.633 63.3526H480.644M489.326 63.3526H489.337M498.02 63.3526H498.031M506.713 63.3526H506.724M515.406 63.3526H515.417M524.1 63.3526H524.111M19.8861 72.046H19.8969M28.5794 72.046H28.5903M37.2728 72.046H37.2836M45.9661 72.046H45.977M54.6594 72.046H54.6703M63.3528 72.046H63.3636M72.0461 72.046H72.057M80.7395 72.046H80.7503M89.4328 72.046H89.4437M411.086 72.046H411.097M419.78 72.046H419.791M428.473 72.046H428.484M437.166 72.046H437.177M445.86 72.046H445.871M454.553 72.046H454.564M463.246 72.046H463.257M471.94 72.046H471.951M480.633 72.046H480.644M489.326 72.046H489.337M498.02 72.046H498.031M506.713 72.046H506.724M515.406 72.046H515.417M524.1 72.046H524.111M28.5794 80.7393H28.5903M37.2728 80.7393H37.2836M45.9661 80.7393H45.977M54.6594 80.7393H54.6703M63.3528 80.7393H63.3636M72.0461 80.7393H72.057M80.7395 80.7393H80.7503M89.4328 80.7393H89.4437M98.1261 80.7393H98.137M402.393 80.7393H402.404M411.086 80.7393H411.097M419.78 80.7393H419.791M428.473 80.7393H428.484M437.166 80.7393H437.177M445.86 80.7393H445.871M454.553 80.7393H454.564M463.246 80.7393H463.257M471.94 80.7393H471.951M480.633 80.7393H480.644M489.326 80.7393H489.337M498.02 80.7393H498.031M506.713 80.7393H506.724M515.406 80.7393H515.417M37.2728 89.4327H37.2836M45.9661 89.4327H45.977M54.6594 89.4327H54.6703M63.3528 89.4327H63.3636M72.0461 89.4327H72.057M80.7395 89.4327H80.7503M89.4328 89.4327H89.4437M98.1261 89.4327H98.137M106.819 89.4327H106.83M393.7 89.4327H393.711M402.393 89.4327H402.404M411.086 89.4327H411.097M419.78 89.4327H419.791M428.473 89.4327H428.484M437.166 89.4327H437.177M445.86 89.4327H445.871M454.553 89.4327H454.564M463.246 89.4327H463.257M471.94 89.4327H471.951M480.633 89.4327H480.644M489.326 89.4327H489.337M498.02 89.4327H498.031M506.713 89.4327H506.724M515.406 89.4327H515.417"
                  stroke-width="5" stroke-linecap="round" class="stroke-slate-300 dark:stroke-white/20" />
        </svg>

        <!-- TITIK PIN INTERAKTIF CABANG MERIDIAN DARI DATABASE -->
        @foreach($branches as $branch)
            @if($branch->position_x && $branch->position_y)
                <div class="map-pin-wrapper" style="left: {{ $branch->position_x }}%; top: {{ $branch->position_y }}%;">
                    <div class="map-pin">
                        <div class="pin-pulse"></div>
                        <div class="pin-core"></div>
                    </div>
                    <div class="map-tooltip">
                        <div class="tooltip-box">
                            <div class="font-bold text-white">{{ $branch->city }}</div>
                            <div class="text-[10px] text-[#FF6B00]">{{ $branch->name }}</div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- STATISTIK JARINGAN MERIDIAN -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto pt-6 border-t border-slate-200 dark:border-white/10">
        <div>
            <h3 class="text-4xl font-black text-[#FF6B00]">{{ $jumlahKota ?? 13 }}+</h3>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">Kota & Hub Utama</p>
        </div>
        <div>
            <h3 class="text-4xl font-black text-[#FF6B00]">{{ number_format($totalKurir ?? 24, 0, ',', '.') }}+</h3>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">Armada Kurir</p>
        </div>
        <div>
            <h3 class="text-4xl font-black text-[#FF6B00]">99.2%</h3>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">SLA Tepat Waktu</p>
        </div>
        <div>
            <h3 class="text-4xl font-black text-[#FF6B00]">24/7</h3>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1">Layanan Support</p>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Tab switching (Lacak vs Cek Tarif)
    // Kalkulator Ongkir di Halaman Depan
    function setWeight(val) {
        const input = document.getElementById('landingWeight');
        if (input) {
            input.value = val.toFixed(1);
            calculateLandingTariff();
        }
    }

    function adjustWeight(delta) {
        const input = document.getElementById('landingWeight');
        if (input) {
            let cur = parseFloat(input.value) || 0;
            cur = Math.max(0.1, cur + delta);
            input.value = cur.toFixed(1);
            calculateLandingTariff();
        }
    }

    function calculateLandingTariff() {
        const weightInput = document.getElementById('landingWeight');
        const serviceSelect = document.getElementById('landingService');
        const output = document.getElementById('landingTariffOutput');
        const detail = document.getElementById('landingTariffDetail');

        if (!weightInput || !serviceSelect || !output) return;

        const weight = parseFloat(weightInput.value) || 0;
        const service = serviceSelect.value;
        const rates = {
            'Reguler': 9000,
            'Express': 15000,
            'Kargo': 6000,
            'Same Day': 20000
        };

        const ratePerKg = rates[service] || 9000;
        const total = Math.round(weight * ratePerKg);

        output.textContent = 'Rp ' + total.toLocaleString('id-ID');

        if (detail) {
            const timeEstimate = service === 'Express' ? 'Estimasi 1 hari (Next Day)' : (service === 'Same Day' ? 'Estimasi hari ini sampai' : (service === 'Kargo' ? 'Estimasi 3-4 hari' : 'Estimasi 2-3 hari'));
            detail.textContent = `${weight.toFixed(1)} Kg × Rp ${ratePerKg.toLocaleString('id-ID')} (${service}) • ${timeEstimate}`;
        }
    }

    // =========================================================
    // ANIMATED CAROUSEL SCRIPT (4 DETIK CYCLING DENGAN ANIMASI)
    // =========================================================
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const totalSlides = slides.length;
    let carouselTimer = null;

    function showSlide(index) {
        if (totalSlides === 0) return;

        slides.forEach((slide, i) => {
            const dot = document.getElementById(`indicator-dot-${i}`);
            if (i === index) {
                slide.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                slide.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
                if (dot) {
                    dot.classList.add('bg-[#FF6B00]', 'w-6');
                    dot.classList.remove('bg-white/40', 'w-2.5');
                }
            } else {
                slide.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                slide.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                if (dot) {
                    dot.classList.remove('bg-[#FF6B00]', 'w-6');
                    dot.classList.add('bg-white/40', 'w-2.5');
                }
            }
        });

        // Reset progress bar animation
        const bar = document.getElementById('carouselProgressBar');
        if (bar) {
            bar.style.transition = 'none';
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.transition = 'width 4000ms linear';
                bar.style.width = '100%';
            }, 50);
        }

        currentSlide = index;
    }

    function nextSlide() {
        const next = (currentSlide + 1) % totalSlides;
        showSlide(next);
        resetCarouselTimer();
    }

    function prevSlide() {
        const prev = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(prev);
        resetCarouselTimer();
    }

    function goToSlide(index) {
        showSlide(index);
        resetCarouselTimer();
    }

    function startCarouselTimer() {
        const bar = document.getElementById('carouselProgressBar');
        if (bar) {
            bar.style.transition = 'width 4000ms linear';
            bar.style.width = '100%';
        }
        carouselTimer = setInterval(() => {
            nextSlide();
        }, 4000);
    }

    function resetCarouselTimer() {
        if (carouselTimer) clearInterval(carouselTimer);
        startCarouselTimer();
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (totalSlides > 0) {
            showSlide(0);
            startCarouselTimer();
        }
    });

</script>
@endpush
