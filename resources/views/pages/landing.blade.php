@extends('layouts.app')

@section('content')

<!-- ===================================== -->
<!-- CSS MURNI / VANILLA CSS               -->
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

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

* {
    box-sizing: inherit;
}

/* --- HERO SECTION --- */
.hero-section {
    position: relative;
    padding: 120px 20px 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    overflow: hidden;
}

.glow-bg {
    position: absolute;
    top: 30%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 600px;
    height: 600px;
    background-color: rgba(255, 107, 0, 0.08);
    filter: blur(100px);
    border-radius: 50%;
    pointer-events: none;
}

.hero-content {
    position: relative;
    z-index: 10;
    max-width: 900px;
    margin: 0 auto;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: var(--dark-blue);
    line-height: 1.1;
    margin-bottom: 1.5rem;
}

.hero-title span {
    color: var(--orange);
}

.hero-subtitle {
    font-size: 1.125rem;
    color: var(--text-gray);
    max-width: 700px;
    margin: 0 auto 3rem;
    line-height: 1.6;
}

/* --- INTERACTIVE CARD (TABS) --- */
.interactive-card {
    background: #fff;
    padding: 20px;
    border-radius: 24px;
    box-shadow: var(--shadow-card);
    border: 1px solid #f1f5f9;
    max-width: 768px;
    margin: 0 auto;
    text-align: left;
}

.tab-menu {
    display: flex;
    background: var(--bg-light);
    border-radius: 16px;
    padding: 6px;
    margin-bottom: 20px;
}

.tab-btn {
    flex: 1;
    padding: 14px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    background: transparent;
    color: var(--text-gray);
}

.tab-btn.active {
    background: var(--dark-blue);
    color: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* --- FORMS --- */
.form-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.form-input {
    flex: 1;
    min-width: 200px;
    padding: 16px 20px;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    font-size: 1rem;
    outline: none;
    transition: 0.3s;
}

.form-input:focus {
    border-color: var(--orange);
    box-shadow: 0 0 0 2px rgba(255, 107, 0, 0.2);
}

.btn-primary {
    background: var(--orange);
    color: #fff;
    border: none;
    padding: 16px 40px;
    border-radius: 12px;
    font-weight: bold;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 8px 16px rgba(255, 107, 0, 0.3);
    white-space: nowrap;
}

.btn-primary:hover {
    background: var(--orange-hover);
}

/* --- TRACKING RESULT --- */
.tracking-result {
    display: none;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}

.tracking-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.tracking-resi {
    font-weight: bold;
    color: var(--dark-blue);
}

.tracking-status {
    background: #dcfce7;
    color: #15803d;
    font-size: 0.75rem;
    font-weight: bold;
    padding: 6px 12px;
    border-radius: 20px;
    border: 1px solid #bbf7d0;
}

.progress-bar-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    padding-bottom: 10px;
}

.progress-line-bg {
    position: absolute;
    top: 16px;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--border-color);
    z-index: 1;
}

.progress-line-active {
    position: absolute;
    top: 16px;
    left: 0;
    width: 60%;
    height: 4px;
    background: var(--orange);
    z-index: 1;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    z-index: 2;
}

.step-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.875rem;
}

.step-circle.done {
    background: var(--orange);
    color: #fff;
}

.step-circle.current {
    background: var(--orange);
    color: #fff;
    box-shadow: 0 0 10px rgba(255, 107, 0, 0.5);
}

.step-circle.pending {
    background: var(--border-color);
    color: var(--text-gray);
}

.step-label {
    font-size: 0.75rem;
    font-weight: bold;
    color: var(--dark-blue);
}

.step-label.pending {
    color: var(--text-gray);
    font-weight: normal;
}

/* --- PLATFORM FEATURE SECTION --- */
.platform-section {
    max-width: 1200px;
    margin: 80px auto 0;
    padding: 0 20px;
    text-align: left;
}

.platform-label {
    color: var(--orange);
    font-size: 0.8rem;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.platform-title {
    font-size: 2.75rem;
    font-weight: 800;
    color: var(--dark-blue);
    margin: 0 0 48px;
    line-height: 1.2;
}

.platform-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

.platform-card {
    background: #ffffff;
    border-radius: 28px;
    padding: 36px 30px;
    border: 1px solid #f1f5f9;
    box-shadow: var(--shadow-card);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 420px;
}

.card-number {
    font-family: monospace;
    font-size: 0.9rem;
    color: #94a3b8;
    margin-bottom: 20px;
}

.card-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--dark-blue);
    line-height: 1.25;
    margin: 0 0 16px;
}

.card-desc {
    font-size: 0.95rem;
    color: var(--text-gray);
    line-height: 1.6;
    margin: 0 0 32px;
}

/* Visuals Inside Cards */
.chart-bars {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    height: 90px;
    padding-top: 10px;
}

.bar {
    flex: 1;
    background: #e2e8f0;
    border-radius: 6px;
}

.bar.highlight {
    background: var(--orange);
}

.route-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.route-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.route-info {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #94a3b8;
}

.route-bar {
    height: 6px;
    border-radius: 10px;
    width: 100%;
}

.route-bar.red {
    background: #ff4d4d;
}

.route-bar.yellow {
    background: #ffc107;
    width: 60%;
}

.route-bar.green {
    background: #10b981;
    width: 35%;
}

.tracking-curve {
    width: 100%;
    height: auto;
    margin-top: auto;
}

/* --- MAP SECTION --- */
.map-section {
    background: #fff;
    padding: 100px 20px 80px;
    text-align: center;
}

.section-label {
    color: var(--orange);
    font-size: 0.875rem;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--dark-blue);
    margin: 16px 0 60px;
}

.map-container {
    position: relative;
    max-width: 1000px;
    margin: 0 auto 60px;
    background: #FAFAFA;
    border-radius: 24px;
    padding: 40px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.map-svg {
    width: 100%;
    height: auto;
    display: block;
    opacity: 0.85;
}

.map-pin-wrapper {
    position: absolute;
    transform: translate(-50%, -50%);
    cursor: pointer;
    z-index: 10;
}

.map-pin {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pin-core {
    width: 10px;
    height: 10px;
    background-color: var(--orange);
    border-radius: 50%;
    position: relative;
    z-index: 2;
    box-shadow: 0 0 8px rgba(255, 107, 0, 0.8);
}

.pin-pulse {
    position: absolute;
    width: 22px;
    height: 22px;
    background-color: var(--orange);
    border-radius: 50%;
    opacity: 0.75;
    animation: pulseAnim 1.6s cubic-bezier(0, 0, 0.2, 1) infinite;
    z-index: 1;
}

@keyframes pulseAnim {

    75%,
    100% {
        transform: scale(2.2);
        opacity: 0;
    }
}

.map-tooltip {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-bottom: 12px;
    display: none;
    flex-direction: column;
    align-items: center;
    z-index: 20;
    width: max-content;
}

.map-pin-wrapper:hover .map-tooltip {
    display: flex;
}

.tooltip-box {
    background: #fff;
    color: var(--dark-blue);
    padding: 8px 14px;
    border-radius: 12px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 8px;
}

.tooltip-icon {
    width: 16px;
    height: 16px;
    fill: none;
    stroke: var(--orange);
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.tooltip-city {
    display: block;
    font-size: 0.875rem;
    font-weight: bold;
    text-align: left;
}

.tooltip-branch {
    display: block;
    font-size: 0.7rem;
    color: var(--text-gray);
    text-align: left;
    font-weight: normal;
}

.tooltip-arrow {
    width: 8px;
    height: 8px;
    background: #fff;
    transform: rotate(45deg);
    margin-top: -4px;
    border-bottom: 1px solid var(--border-color);
    border-right: 1px solid var(--border-color);
}

/* --- STATISTIK --- */
.stats-container {
    display: flex;
    justify-content: center;
    gap: 40px;
    max-width: 1000px;
    margin: 0 auto;
    flex-wrap: wrap;
}

.stat-item {
    flex: 1;
    min-width: 150px;
    border-left: 1px solid var(--border-color);
}

.stat-item:first-child {
    border-left: none;
}

.stat-value {
    font-size: 3rem;
    font-weight: 800;
    color: var(--orange);
    margin: 0 0 8px;
}

.stat-label {
    color: var(--text-gray);
    font-weight: 500;
    margin: 0;
}

@media (max-width: 992px) {
    .platform-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }

    .form-group {
        flex-direction: column;
    }

    .stat-item {
        border-left: none;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 20px;
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .map-container {
        padding: 20px 10px;
    }
}
</style>

<!-- ===================================== -->
<!-- HERO SECTION (Lacak & Cek Tarif)      -->
<!-- ===================================== -->
<div class="hero-section">
    <div class="glow-bg"></div>

    <div class="hero-content">
        <h1 class="hero-title">
            Kirim Paket <span>Cepat</span>, Aman, dan Tepat Waktu.
        </h1>
        <p class="hero-subtitle">
            Layanan ekspedisi terpercaya ke seluruh pelosok Nusantara. Lacak keberadaan paket atau cek estimasi tarif
            pengiriman Anda sekarang.
        </p>

        <div class="interactive-card">
            <!-- Tab Menu -->
            <div class="tab-menu">
                <button id="btn-lacak" class="tab-btn active" onclick="switchTab('lacak')">Lacak Paket</button>
                <button id="btn-tarif" class="tab-btn" onclick="switchTab('tarif')">Cek Tarif</button>
            </div>

            <!-- Konten Lacak Paket -->
            <div id="content-lacak" class="tab-content active">
                <div class="form-group">
                    <input type="text" placeholder="Masukkan resi (cth: MRD-48207)" class="form-input">
                    <button class="btn-primary" onclick="showTrackingResult()">Lacak</button>
                </div>

                <!-- Hasil Pelacakan -->
                <div id="tracking-result" class="tracking-result">
                    <div class="tracking-header">
                        <span class="tracking-resi">MRD-48207</span>
                        <span class="tracking-status">Tepat waktu • Estimasi 19 Okt</span>
                    </div>

                    <div class="progress-bar-container">
                        <div class="progress-line-bg"></div>
                        <div class="progress-line-active"></div>

                        <div class="step-item">
                            <div class="step-circle done">✓</div>
                            <span class="step-label">Dijemput</span>
                        </div>
                        <div class="step-item">
                            <div class="step-circle done">✓</div>
                            <span class="step-label">Di hub</span>
                        </div>
                        <div class="step-item">
                            <div class="step-circle current">3</div>
                            <span class="step-label">Dalam perjalanan</span>
                        </div>
                        <div class="step-item">
                            <div class="step-circle pending">4</div>
                            <span class="step-label pending">Terkirim</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konten Cek Tarif -->
            <div id="content-tarif" class="tab-content">
                <div class="form-group">
                    <input type="text" placeholder="Asal (cth: Jakarta)" class="form-input">
                    <input type="text" placeholder="Tujuan (cth: Surabaya)" class="form-input">
                    <input type="number" min="1" placeholder="Kg" class="form-input"
                        style="min-width: 80px; flex: 0.5;">
                    <button class="btn-primary">Cek</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================== -->
    <!-- SEKSI PLATFORM / KECERDASAN (FITUR)   -->
    <!-- ===================================== -->
    <div id="layanan" class="platform-section">
        <div class="platform-label">PLATFORM</div>
        <h2 class="platform-title">Kecerdasan di setiap kilometer.</h2>

        <div class="platform-grid">
            <!-- Card 01 -->
            <div class="platform-card">
                <div>
                    <div class="card-number">01 · Harga Dinamis</div>
                    <h3 class="card-title">Harga yang bergerak mengikuti pasar.</h3>
                    <p class="card-desc">Model kami menimbang permintaan jalur, berat, dan waktu untuk memberi tarif
                        paling adil dalam hitungan milidetik.</p>
                </div>
                <div class="chart-bars">
                    <div class="bar" style="height: 35%;"></div>
                    <div class="bar" style="height: 55%;"></div>
                    <div class="bar" style="height: 40%;"></div>
                    <div class="bar" style="height: 75%;"></div>
                    <div class="bar" style="height: 50%;"></div>
                    <div class="bar highlight" style="height: 95%;"></div>
                    <div class="bar" style="height: 65%;"></div>
                </div>
            </div>

            <!-- Card 02 -->
            <div class="platform-card">
                <div>
                    <div class="card-number">02 · Prediksi Keterlambatan</div>
                    <h3 class="card-title" style="visibility: hidden; height: 0; margin: 0;">Rute Otomatis</h3>
                    <p class="card-desc">Machine learning menilai risiko kemacetan dan cuaca di setiap rute, lalu
                        mengalihkan rute secara otomatis.</p>
                </div>
                <div class="route-list">
                    <div class="route-item">
                        <div class="route-info">
                            <span>Jakarta → Surabaya</span>
                            <span style="color: #ff4d4d; font-weight: bold;">Tinggi</span>
                        </div>
                        <div class="route-bar red"></div>
                    </div>
                    <div class="route-item">
                        <div class="route-info">
                            <span>Sidoarjo → Malang</span>
                            <span style="color: #ffc107; font-weight: bold;">Sedang</span>
                        </div>
                        <div class="route-bar yellow"></div>
                    </div>
                    <div class="route-item">
                        <div class="route-info">
                            <span>Sidoarjo → Surabaya</span>
                            <span style="color: #10b981; font-weight: bold;">Rendah</span>
                        </div>
                        <div class="route-bar green"></div>
                    </div>
                </div>
            </div>

            <!-- Card 03 -->
            <div class="platform-card">
                <div>
                    <div class="card-number">03 · Pelacakan Langsung</div>
                    <h3 class="card-title">Setiap paket, secara real-time.</h3>
                    <p class="card-desc">Visibilitas hingga level pemindaian dari penjemputan sampai depan pintu, dengan
                        notifikasi status instan.</p>
                </div>
                <svg class="tracking-curve" viewBox="0 0 260 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 55 C 60 55, 70 15, 120 20 C 170 25, 190 50, 240 10" stroke="#FF6B00" stroke-width="3"
                        stroke-dasharray="6 6" fill="none" />
                    <circle cx="10" cy="55" r="5" fill="#0A1128" />
                    <circle cx="120" cy="20" r="14" fill="#FF6B00" fill-opacity="0.2" />
                    <circle cx="120" cy="20" r="6" fill="#FF6B00" />
                    <circle cx="240" cy="10" r="5" fill="#CBD5E1" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- ===================================== -->
<!-- SEKSI JARINGAN (Peta Indonesia SVG)   -->
<!-- ===================================== -->
<section id="jaringan" class="map-section">
    <div class="section-label">Jaringan Jangkauan</div>
    <h2 class="section-title">Menjangkau {{ $jumlahKota ?? 0 }}+ Kota di <br> Indonesia</h2>

    <!-- Container Peta -->
    <div class="map-container">

        <!-- PETA SVG INDONESIA DARI FIGMA -->
        <svg class="map-svg" viewBox="0 0 1049 388" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M498.02 2.49927H498.031M506.713 2.49927H506.724M489.326 11.1926H489.337M498.02 11.1926H498.031M506.713 11.1926H506.724M515.406 11.1926H515.417M471.94 19.8859H471.951M480.633 19.8859H480.644M489.326 19.8859H489.337M498.02 19.8859H498.031M506.713 19.8859H506.724M515.406 19.8859H515.417M2.49939 28.5793H2.51026M11.1927 28.5793H11.2036M463.246 28.5793H463.257M471.94 28.5793H471.951M480.633 28.5793H480.644M489.326 28.5793H489.337M498.02 28.5793H498.031M506.713 28.5793H506.724M515.406 28.5793H515.417M524.1 28.5793H524.111M11.1927 37.2726H11.2036M19.8861 37.2726H19.8969M28.5794 37.2726H28.5903M37.2728 37.2726H37.2836M45.9661 37.2726H45.977M454.553 37.2726H454.564M463.246 37.2726H463.257M471.94 37.2726H471.951M480.633 37.2726H480.644M489.326 37.2726H489.337M498.02 37.2726H498.031M506.713 37.2726H506.724M515.406 37.2726H515.417M524.1 37.2726H524.111M532.793 37.2726H532.804M11.1927 45.966H11.2036M19.8861 45.966H19.8969M28.5794 45.966H28.5903M37.2728 45.966H37.2836M45.9661 45.966H45.977M54.6594 45.966H54.6703M445.86 45.966H445.871M454.553 45.966H454.564M463.246 45.966H463.257M471.94 45.966H471.951M480.633 45.966H480.644M489.326 45.966H489.337M498.02 45.966H498.031M506.713 45.966H506.724M515.406 45.966H515.417M524.1 45.966H524.111M532.793 45.966H532.804M11.1927 54.6593H11.2036M19.8861 54.6593H19.8969M28.5794 54.6593H28.5903M37.2728 54.6593H37.2836M45.9661 54.6593H45.977M54.6594 54.6593H54.6703M63.3528 54.6593H63.3636M72.0461 54.6593H72.057M428.473 54.6593H428.484M437.166 54.6593H437.177M445.86 54.6593H445.871M454.553 54.6593H454.564M463.246 54.6593H463.257M471.94 54.6593H471.951M480.633 54.6593H480.644M489.326 54.6593H489.337M498.02 54.6593H498.031M506.713 54.6593H506.724M515.406 54.6593H515.417M524.1 54.6593H524.111M19.8861 63.3526H19.8969M28.5794 63.3526H28.5903M37.2728 63.3526H37.2836M45.9661 63.3526H45.977M54.6594 63.3526H54.6703M63.3528 63.3526H63.3636M72.0461 63.3526H72.057M80.7395 63.3526H80.7503M419.78 63.3526H419.791M428.473 63.3526H428.484M437.166 63.3526H437.177M445.86 63.3526H445.871M454.553 63.3526H454.564M463.246 63.3526H463.257M471.94 63.3526H471.951M480.633 63.3526H480.644M489.326 63.3526H489.337M498.02 63.3526H498.031M506.713 63.3526H506.724M515.406 63.3526H515.417M524.1 63.3526H524.111M19.8861 72.046H19.8969M28.5794 72.046H28.5903M37.2728 72.046H37.2836M45.9661 72.046H45.977M54.6594 72.046H54.6703M63.3528 72.046H63.3636M72.0461 72.046H72.057M80.7395 72.046H80.7503M89.4328 72.046H89.4437M411.086 72.046H411.097M419.78 72.046H419.791M428.473 72.046H428.484M437.166 72.046H437.177M445.86 72.046H445.871M454.553 72.046H454.564M463.246 72.046H463.257M471.94 72.046H471.951M480.633 72.046H480.644M489.326 72.046H489.337M498.02 72.046H498.031M506.713 72.046H506.724M515.406 72.046H515.417M524.1 72.046H524.111M28.5794 80.7393H28.5903M37.2728 80.7393H37.2836M45.9661 80.7393H45.977M54.6594 80.7393H54.6703M63.3528 80.7393H63.3636M72.0461 80.7393H72.057M80.7395 80.7393H80.7503M89.4328 80.7393H89.4437M98.1261 80.7393H98.137M402.393 80.7393H402.404M411.086 80.7393H411.097M419.78 80.7393H419.791M428.473 80.7393H428.484M437.166 80.7393H437.177M445.86 80.7393H445.871M454.553 80.7393H454.564M463.246 80.7393H463.257M471.94 80.7393H471.951M480.633 80.7393H480.644M489.326 80.7393H489.337M498.02 80.7393H498.031M506.713 80.7393H506.724M515.406 80.7393H515.417M37.2728 89.4327H37.2836M45.9661 89.4327H45.977M54.6594 89.4327H54.6703M63.3528 89.4327H63.3636M72.0461 89.4327H72.057M80.7395 89.4327H80.7503M89.4328 89.4327H89.4437M98.1261 89.4327H98.137M106.819 89.4327H106.83M393.7 89.4327H393.711M402.393 89.4327H402.404M411.086 89.4327H411.097M419.78 89.4327H419.791M428.473 89.4327H428.484M437.166 89.4327H437.177M445.86 89.4327H445.871M454.553 89.4327H454.564M463.246 89.4327H463.257M471.94 89.4327H471.951M480.633 89.4327H480.644M489.326 89.4327H489.337M498.02 89.4327H498.031M506.713 89.4327H506.724M515.406 89.4327H515.417M54.6594 98.126H54.6703M63.3528 98.126H63.3636M72.0461 98.126H72.057M80.7395 98.126H80.7503M89.4328 98.126H89.4437M98.1261 98.126H98.137M106.819 98.126H106.83M115.513 98.126H115.524M124.206 98.126H124.217M385.006 98.126H385.017M393.7 98.126H393.711M402.393 98.126H402.404M411.086 98.126H411.097M419.78 98.126H419.791M428.473 98.126H428.484M437.166 98.126H437.177M445.86 98.126H445.871M454.553 98.126H454.564M463.246 98.126H463.257M471.94 98.126H471.951M480.633 98.126H480.644M489.326 98.126H489.337M498.02 98.126H498.031M506.713 98.126H506.724M63.3528 106.819H63.3636M72.0461 106.819H72.057M80.7395 106.819H80.7503M89.4328 106.819H89.4437M98.1261 106.819H98.137M106.819 106.819H106.83M115.513 106.819H115.524M124.206 106.819H124.217M132.899 106.819H132.91M376.313 106.819H376.324M385.006 106.819H385.017M393.7 106.819H393.711M402.393 106.819H402.404M411.086 106.819H411.097M419.78 106.819H419.791M428.473 106.819H428.484M437.166 106.819H437.177M445.86 106.819H445.871M454.553 106.819H454.564M463.246 106.819H463.257M471.94 106.819H471.951M480.633 106.819H480.644M489.326 106.819H489.337M498.02 106.819H498.031M506.713 106.819H506.724M72.0461 115.513H72.057M80.7395 115.513H80.7503M89.4328 115.513H89.4437M98.1261 115.513H98.137M106.819 115.513H106.83M115.513 115.513H115.524M124.206 115.513H124.217M132.899 115.513H132.91M141.593 115.513H141.604M315.46 115.513H315.471M324.153 115.513H324.164M332.846 115.513H332.857M367.62 115.513H367.631M376.313 115.513H376.324M385.006 115.513H385.017M393.7 115.513H393.711M402.393 115.513H402.404M411.086 115.513H411.097M419.78 115.513H419.791M428.473 115.513H428.484M437.166 115.513H437.177M445.86 115.513H445.871M454.553 115.513H454.564M463.246 115.513H463.257M471.94 115.513H471.951M480.633 115.513H480.644M489.326 115.513H489.337M498.02 115.513H498.031M506.713 115.513H506.724M654.5 115.513H654.511M663.193 115.513H663.204M671.887 115.513H671.897M80.7395 124.206H80.7503M89.4328 124.206H89.4437M98.1261 124.206H98.137M106.819 124.206H106.83M115.513 124.206H115.524M124.206 124.206H124.217M132.899 124.206H132.91M141.593 124.206H141.604M150.286 124.206H150.297M158.98 124.206H158.99M315.46 124.206H315.471M324.153 124.206H324.164M332.846 124.206H332.857M341.54 124.206H341.551M350.233 124.206H350.244M358.926 124.206H358.937M367.62 124.206H367.631M376.313 124.206H376.324M385.006 124.206H385.017M393.7 124.206H393.711M402.393 124.206H402.404M411.086 124.206H411.097M419.78 124.206H419.791M428.473 124.206H428.484M437.166 124.206H437.177M445.86 124.206H445.871M454.553 124.206H454.564M463.246 124.206H463.257M471.94 124.206H471.951M480.633 124.206H480.644M489.326 124.206H489.337M498.02 124.206H498.031M584.953 124.206H584.964M593.647 124.206H593.657M602.34 124.206H602.351M611.033 124.206H611.044M619.727 124.206H619.737M628.42 124.206H628.431M637.113 124.206H637.124M645.807 124.206H645.817M654.5 124.206H654.511M663.193 124.206H663.204M671.887 124.206H671.897M732.74 124.206H732.751M741.433 124.206H741.444M80.7395 132.899H80.7503M89.4328 132.899H89.4437M98.1261 132.899H98.137M106.819 132.899H106.83M115.513 132.899H115.524M124.206 132.899H124.217M132.899 132.899H132.91M141.593 132.899H141.604M150.286 132.899H150.297M158.98 132.899H158.99M167.673 132.899H167.684M315.46 132.899H315.471M324.153 132.899H324.164M332.846 132.899H332.857M341.54 132.899H341.551M350.233 132.899H350.244M358.926 132.899H358.937M367.62 132.899H367.631M376.313 132.899H376.324M385.006 132.899H385.017M393.7 132.899H393.711M402.393 132.899H402.404M411.086 132.899H411.097M419.78 132.899H419.791M428.473 132.899H428.484M437.166 132.899H437.177M445.86 132.899H445.871M454.553 132.899H454.564M463.246 132.899H463.257M471.94 132.899H471.951M480.633 132.899H480.644M489.326 132.899H489.337M498.02 132.899H498.031M558.873 132.899H558.884M576.26 132.899H576.271M584.953 132.899H584.964M593.647 132.899H593.657M602.34 132.899H602.351M611.033 132.899H611.044M619.727 132.899H619.737M628.42 132.899H628.431M637.113 132.899H637.124M645.807 132.899H645.817M654.5 132.899H654.511M663.193 132.899H663.204M671.887 132.899H671.897M732.74 132.899H732.751M741.433 132.899H741.444M750.127 132.899H750.138M89.4328 141.593H89.4437M98.1261 141.593H98.137M106.819 141.593H106.83M115.513 141.593H115.524M124.206 141.593H124.217M132.899 141.593H132.91M141.593 141.593H141.604M150.286 141.593H150.297M158.98 141.593H158.99M167.673 141.593H167.684M176.366 141.593H176.377M315.46 141.593H315.471M324.153 141.593H324.164M332.846 141.593H332.857M341.54 141.593H341.551M350.233 141.593H350.244M358.926 141.593H358.937M367.62 141.593H367.631M376.313 141.593H376.324M385.006 141.593H385.017M393.7 141.593H393.711M402.393 141.593H402.404M411.086 141.593H411.097M419.78 141.593H419.791M428.473 141.593H428.484M437.166 141.593H437.177M445.86 141.593H445.871M454.553 141.593H454.564M463.246 141.593H463.257M471.94 141.593H471.951M480.633 141.593H480.644M489.326 141.593H489.337M498.02 141.593H498.031M541.487 141.593H541.497M550.18 141.593H550.191M584.953 141.593H584.964M593.647 141.593H593.657M602.34 141.593H602.351M611.033 141.593H611.044M619.727 141.593H619.737M628.42 141.593H628.431M732.74 141.593H732.751M741.433 141.593H741.444M98.1261 150.286H98.137M106.819 150.286H106.83M115.513 150.286H115.524M124.206 150.286H124.217M132.899 150.286H132.91M141.593 150.286H141.604M150.286 150.286H150.297M158.98 150.286H158.99M167.673 150.286H167.684M176.366 150.286H176.377M185.06 150.286H185.07M315.46 150.286H315.471M324.153 150.286H324.164M332.846 150.286H332.857M341.54 150.286H341.551M350.233 150.286H350.244M358.926 150.286H358.937M367.62 150.286H367.631M376.313 150.286H376.324M385.006 150.286H385.017M393.7 150.286H393.711M402.393 150.286H402.404M411.086 150.286H411.097M419.78 150.286H419.791M428.473 150.286H428.484M437.166 150.286H437.177M445.86 150.286H445.871M454.553 150.286H454.564M463.246 150.286H463.257M471.94 150.286H471.951M480.633 150.286H480.644M489.326 150.286H489.337M541.487 150.286H541.497M550.18 150.286H550.191M732.74 150.286H732.751M741.433 150.286H741.444M98.1261 158.979H98.137M106.819 158.979H106.83M115.513 158.979H115.524M124.206 158.979H124.217M132.899 158.979H132.91M141.593 158.979H141.604M150.286 158.979H150.297M158.98 158.979H158.99M167.673 158.979H167.684M176.366 158.979H176.377M185.06 158.979H185.07M193.753 158.979H193.764M315.46 158.979H315.471M324.153 158.979H324.164M332.846 158.979H332.857M341.54 158.979H341.551M350.233 158.979H350.244M358.926 158.979H358.937M367.62 158.979H367.631M376.313 158.979H376.324M385.006 158.979H385.017M393.7 158.979H393.711M402.393 158.979H402.404M411.086 158.979H411.097M419.78 158.979H419.791M428.473 158.979H428.484M437.166 158.979H437.177M445.86 158.979H445.871M454.553 158.979H454.564M463.246 158.979H463.257M471.94 158.979H471.951M480.633 158.979H480.644M489.326 158.979H489.337M550.18 158.979H550.191M741.433 158.979H741.444M106.819 167.673H106.83M115.513 167.673H115.524M124.206 167.673H124.217M132.899 167.673H132.91M141.593 167.673H141.604M150.286 167.673H150.297M158.98 167.673H158.99M167.673 167.673H167.684M176.366 167.673H176.377M185.06 167.673H185.07M193.753 167.673H193.764M202.446 167.673H202.457M315.46 167.673H315.471M324.153 167.673H324.164M332.846 167.673H332.857M341.54 167.673H341.551M350.233 167.673H350.244M358.926 167.673H358.937M367.62 167.673H367.631M376.313 167.673H376.324M385.006 167.673H385.017M393.7 167.673H393.711M402.393 167.673H402.404M411.086 167.673H411.097M419.78 167.673H419.791M428.473 167.673H428.484M437.166 167.673H437.177M445.86 167.673H445.871M454.553 167.673H454.564M463.246 167.673H463.257M471.94 167.673H471.951M480.633 167.673H480.644M489.326 167.673H489.337M863.14 167.673H863.151M871.833 167.673H871.844M880.527 167.673H880.538M106.819 176.366H106.83M115.513 176.366H115.524M124.206 176.366H124.217M132.899 176.366H132.91M141.593 176.366H141.604M150.286 176.366H150.297M158.98 176.366H158.99M167.673 176.366H167.684M176.366 176.366H176.377M185.06 176.366H185.07M193.753 176.366H193.764M202.446 176.366H202.457M324.153 176.366H324.164M332.846 176.366H332.857M341.54 176.366H341.551M350.233 176.366H350.244M358.926 176.366H358.937M367.62 176.366H367.631M376.313 176.366H376.324M385.006 176.366H385.017M393.7 176.366H393.711M402.393 176.366H402.404M411.086 176.366H411.097M419.78 176.366H419.791M428.473 176.366H428.484M437.166 176.366H437.177M445.86 176.366H445.871M454.553 176.366H454.564M463.246 176.366H463.257M471.94 176.366H471.951M480.633 176.366H480.644M489.326 176.366H489.337M819.673 176.366H819.684M828.367 176.366H828.378M837.06 176.366H837.071M845.753 176.366H845.764M854.447 176.366H854.458M863.14 176.366H863.151M871.833 176.366H871.844M880.527 176.366H880.538M889.22 176.366H889.231M897.913 176.366H897.924M906.607 176.366H906.618M915.3 176.366H915.311M115.513 185.059H115.524M124.206 185.059H124.217M132.899 185.059H132.91M141.593 185.059H141.604M150.286 185.059H150.297M158.98 185.059H158.99M167.673 185.059H167.684M176.366 185.059H176.377M185.06 185.059H185.07M193.753 185.059H193.764M202.446 185.059H202.457M211.14 185.059H211.15M324.153 185.059H324.164M332.846 185.059H332.857M341.54 185.059H341.551M350.233 185.059H350.244M358.926 185.059H358.937M367.62 185.059H367.631M376.313 185.059H376.324M385.006 185.059H385.017M393.7 185.059H393.711M402.393 185.059H402.404M411.086 185.059H411.097M419.78 185.059H419.791M428.473 185.059H428.484M437.166 185.059H437.177M445.86 185.059H445.871M454.553 185.059H454.564M463.246 185.059H463.257M471.94 185.059H471.951M480.633 185.059H480.644M558.873 185.059H558.884M819.673 185.059H819.684M828.367 185.059H828.378M837.06 185.059H837.071M845.753 185.059H845.764M854.447 185.059H854.458M863.14 185.059H863.151M871.833 185.059H871.844M880.527 185.059H880.538M889.22 185.059H889.231M897.913 185.059H897.924M906.607 185.059H906.618M915.3 185.059H915.311M923.993 185.059H924.004M932.687 185.059H932.698M941.38 185.059H941.391M124.206 193.753H124.217M132.899 193.753H132.91M141.593 193.753H141.604M150.286 193.753H150.297M158.98 193.753H158.99M167.673 193.753H167.684M176.366 193.753H176.377M185.06 193.753H185.07M193.753 193.753H193.764M202.446 193.753H202.457M211.14 193.753H211.15M219.833 193.753H219.844M332.846 193.753H332.857M341.54 193.753H341.551M350.233 193.753H350.244M358.926 193.753H358.937M367.62 193.753H367.631M376.313 193.753H376.324M385.006 193.753H385.017M393.7 193.753H393.711M402.393 193.753H402.404M411.086 193.753H411.097M419.78 193.753H419.791M428.473 193.753H428.484M437.166 193.753H437.177M445.86 193.753H445.871M454.553 193.753H454.564M463.246 193.753H463.257M471.94 193.753H471.951M480.633 193.753H480.644M550.18 193.753H550.191M558.873 193.753H558.884M828.367 193.753H828.378M837.06 193.753H837.071M845.753 193.753H845.764M854.447 193.753H854.458M863.14 193.753H863.151M871.833 193.753H871.844M880.527 193.753H880.538M889.22 193.753H889.231M897.913 193.753H897.924M906.607 193.753H906.618M915.3 193.753H915.311M923.993 193.753H924.004M932.687 193.753H932.698M941.38 193.753H941.391M950.073 193.753H950.084M958.767 193.753H958.778M967.46 193.753H967.471M976.154 193.753H976.164M124.206 202.446H124.217M132.899 202.446H132.91M141.593 202.446H141.604M150.286 202.446H150.297M158.98 202.446H158.99M167.673 202.446H167.684M176.366 202.446H176.377M185.06 202.446H185.07M193.753 202.446H193.764M202.446 202.446H202.457M211.14 202.446H211.15M219.833 202.446H219.844M332.846 202.446H332.857M341.54 202.446H341.551M350.233 202.446H350.244M358.926 202.446H358.937M367.62 202.446H367.631M376.313 202.446H376.324M385.006 202.446H385.017M393.7 202.446H393.711M402.393 202.446H402.404M411.086 202.446H411.097M419.78 202.446H419.791M428.473 202.446H428.484M437.166 202.446H437.177M445.86 202.446H445.871M454.553 202.446H454.564M463.246 202.446H463.257M471.94 202.446H471.951M480.633 202.446H480.644M550.18 202.446H550.191M558.873 202.446H558.884M567.567 202.446H567.577M828.367 202.446H828.378M837.06 202.446H837.071M845.753 202.446H845.764M854.447 202.446H854.458M863.14 202.446H863.151M871.833 202.446H871.844M880.527 202.446H880.538M889.22 202.446H889.231M897.913 202.446H897.924M906.607 202.446H906.618M915.3 202.446H915.311M923.993 202.446H924.004M932.687 202.446H932.698M941.38 202.446H941.391M950.073 202.446H950.084M958.767 202.446H958.778M967.46 202.446H967.471M976.154 202.446H976.164M984.847 202.446H984.858M993.54 202.446H993.551M1002.23 202.446H1002.24M132.899 211.139H132.91M141.593 211.139H141.604M150.286 211.139H150.297M158.98 211.139H158.99M167.673 211.139H167.684M176.366 211.139H176.377M185.06 211.139H185.07M193.753 211.139H193.764M202.446 211.139H202.457M211.14 211.139H211.15M219.833 211.139H219.844M228.526 211.139H228.537M341.54 211.139H341.551M350.233 211.139H350.244M358.926 211.139H358.937M367.62 211.139H367.631M376.313 211.139H376.324M385.006 211.139H385.017M393.7 211.139H393.711M402.393 211.139H402.404M411.086 211.139H411.097M419.78 211.139H419.791M428.473 211.139H428.484M437.166 211.139H437.177M445.86 211.139H445.871M454.553 211.139H454.564M463.246 211.139H463.257M471.94 211.139H471.951M550.18 211.139H550.191M558.873 211.139H558.884M567.567 211.139H567.577M837.06 211.139H837.071M845.753 211.139H845.764M854.447 211.139H854.458M863.14 211.139H863.151M871.833 211.139H871.844M880.527 211.139H880.538M889.22 211.139H889.231M897.913 211.139H897.924M906.607 211.139H906.618M915.3 211.139H915.311M923.993 211.139H924.004M932.687 211.139H932.698M941.38 211.139H941.391M950.073 211.139H950.084M958.767 211.139H958.778M967.46 211.139H967.471M976.154 211.139H976.164M984.847 211.139H984.858M993.54 211.139H993.551M1002.23 211.139H1002.24M1010.93 211.139H1010.94M1019.62 211.139H1019.63M1028.31 211.139H1028.32M1037.01 211.139H1037.02M141.593 219.833H141.604M150.286 219.833H150.297M158.98 219.833H158.99M167.673 219.833H167.684M176.366 219.833H176.377M185.06 219.833H185.07M193.753 219.833H193.764M202.446 219.833H202.457M211.14 219.833H211.15M219.833 219.833H219.844M228.526 219.833H228.537M237.22 219.833H237.23M358.926 219.833H358.937M367.62 219.833H367.631M376.313 219.833H376.324M385.006 219.833H385.017M393.7 219.833H393.711M402.393 219.833H402.404M411.086 219.833H411.097M419.78 219.833H419.791M428.473 219.833H428.484M437.166 219.833H437.177M445.86 219.833H445.871M454.553 219.833H454.564M463.246 219.833H463.257M471.94 219.833H471.951M550.18 219.833H550.191M558.873 219.833H558.884M567.567 219.833H567.577M576.26 219.833H576.271M837.06 219.833H837.071M845.753 219.833H845.764M854.447 219.833H854.458M863.14 219.833H863.151M871.833 219.833H871.844M880.527 219.833H880.538M889.22 219.833H889.231M897.913 219.833H897.924M906.607 219.833H906.618M915.3 219.833H915.311M923.993 219.833H924.004M932.687 219.833H932.698M941.38 219.833H941.391M950.073 219.833H950.084M958.767 219.833H958.778M967.46 219.833H967.471M976.154 219.833H976.164M984.847 219.833H984.858M993.54 219.833H993.551M1002.23 219.833H1002.24M1010.93 219.833H1010.94M1019.62 219.833H1019.63M1028.31 219.833H1028.32M1037.01 219.833H1037.02M1045.7 219.833H1045.71M141.593 228.526H141.604M150.286 228.526H150.297M158.98 228.526H158.99M167.673 228.526H167.684M176.366 228.526H176.377M185.06 228.526H185.07M193.753 228.526H193.764M202.446 228.526H202.457M211.14 228.526H211.15M219.833 228.526H219.844M228.526 228.526H228.537M237.22 228.526H237.23M385.006 228.526H385.017M393.7 228.526H393.711M402.393 228.526H402.404M411.086 228.526H411.097M419.78 228.526H419.791M428.473 228.526H428.484M437.166 228.526H437.177M445.86 228.526H445.871M454.553 228.526H454.564M463.246 228.526H463.257M471.94 228.526H471.951M550.18 228.526H550.191M558.873 228.526H558.884M567.567 228.526H567.577M576.26 228.526H576.271M837.06 228.526H837.071M845.753 228.526H845.764M854.447 228.526H854.458M863.14 228.526H863.151M871.833 228.526H871.844M880.527 228.526H880.538M889.22 228.526H889.231M897.913 228.526H897.924M906.607 228.526H906.618M915.3 228.526H915.311M923.993 228.526H924.004M932.687 228.526H932.698M941.38 228.526H941.391M950.073 228.526H950.084M958.767 228.526H958.778M967.46 228.526H967.471M976.154 228.526H976.164M984.847 228.526H984.858M993.54 228.526H993.551M1002.23 228.526H1002.24M1010.93 228.526H1010.94M1019.62 228.526H1019.63M1028.31 228.526H1028.32M1037.01 228.526H1037.02M1045.7 228.526H1045.71M150.286 237.219H150.297M158.98 237.219H158.99M167.673 237.219H167.684M176.366 237.219H176.377M185.06 237.219H185.07M193.753 237.219H193.764M202.446 237.219H202.457M211.14 237.219H211.15M219.833 237.219H219.844M228.526 237.219H228.537M237.22 237.219H237.23M411.086 237.219H411.097M419.78 237.219H419.791M428.473 237.219H428.484M437.166 237.219H437.177M445.86 237.219H445.871M454.553 237.219H454.564M463.246 237.219H463.257M550.18 237.219H550.191M558.873 237.219H558.884M567.567 237.219H567.577M576.26 237.219H576.271M593.647 237.219H593.657M845.753 237.219H845.764M854.447 237.219H854.458M863.14 237.219H863.151M871.833 237.219H871.844M880.527 237.219H880.538M889.22 237.219H889.231M897.913 237.219H897.924M906.607 237.219H906.618M915.3 237.219H915.311M923.993 237.219H924.004M932.687 237.219H932.698M941.38 237.219H941.391M950.073 237.219H950.084M958.767 237.219H958.778M967.46 237.219H967.471M976.154 237.219H976.164M984.847 237.219H984.858M993.54 237.219H993.551M1002.23 237.219H1002.24M1010.93 237.219H1010.94M1019.62 237.219H1019.63M1028.31 237.219H1028.32M1037.01 237.219H1037.02M1045.7 237.219H1045.71M158.98 245.913H158.99M167.673 245.913H167.684M176.366 245.913H176.377M185.06 245.913H185.07M193.753 245.913H193.764M202.446 245.913H202.457M211.14 245.913H211.15M219.833 245.913H219.844M228.526 245.913H228.537M237.22 245.913H237.23M550.18 245.913H550.191M558.873 245.913H558.884M567.567 245.913H567.577M576.26 245.913H576.271M602.34 245.913H602.351M611.033 245.913H611.044M845.753 245.913H845.764M854.447 245.913H854.458M863.14 245.913H863.151M871.833 245.913H871.844M880.527 245.913H880.538M889.22 245.913H889.231M897.913 245.913H897.924M906.607 245.913H906.618M915.3 245.913H915.311M923.993 245.913H924.004M932.687 245.913H932.698M941.38 245.913H941.391M950.073 245.913H950.084M958.767 245.913H958.778M967.46 245.913H967.471M976.154 245.913H976.164M984.847 245.913H984.858M993.54 245.913H993.551M1002.23 245.913H1002.24M1010.93 245.913H1010.94M1019.62 245.913H1019.63M1028.31 245.913H1028.32M1037.01 245.913H1037.02M1045.7 245.913H1045.71M167.673 254.606H167.684M176.366 254.606H176.377M185.06 254.606H185.07M193.753 254.606H193.764M202.446 254.606H202.457M211.14 254.606H211.15M219.833 254.606H219.844M228.526 254.606H228.537M237.22 254.606H237.23M550.18 254.606H550.191M558.873 254.606H558.884M567.567 254.606H567.577M602.34 254.606H602.351M611.033 254.606H611.044M619.727 254.606H619.737M889.22 254.606H889.231M897.913 254.606H897.924M906.607 254.606H906.618M915.3 254.606H915.311M923.993 254.606H924.004M932.687 254.606H932.698M941.38 254.606H941.391M950.073 254.606H950.084M958.767 254.606H958.778M967.46 254.606H967.471M976.154 254.606H976.164M984.847 254.606H984.858M993.54 254.606H993.551M1002.23 254.606H1002.24M1010.93 254.606H1010.94M1019.62 254.606H1019.63M1028.31 254.606H1028.32M1037.01 254.606H1037.02M1045.7 254.606H1045.71M176.366 263.299H176.377M185.06 263.299H185.07M193.753 263.299H193.764M202.446 263.299H202.457M211.14 263.299H211.15M219.833 263.299H219.844M228.526 263.299H228.537M237.22 263.299H237.23M550.18 263.299H550.191M558.873 263.299H558.884M567.567 263.299H567.577M611.033 263.299H611.044M619.727 263.299H619.737M906.607 263.299H906.618M915.3 263.299H915.311M923.993 263.299H924.004M932.687 263.299H932.698M941.38 263.299H941.391M950.073 263.299H950.084M958.767 263.299H958.778M967.46 263.299H967.471M976.154 263.299H976.164M984.847 263.299H984.858M993.54 263.299H993.551M1002.23 263.299H1002.24M1010.93 263.299H1010.94M1019.62 263.299H1019.63M1028.31 263.299H1028.32M1037.01 263.299H1037.02M1045.7 263.299H1045.71M193.753 271.993H193.764M202.446 271.993H202.457M211.14 271.993H211.15M219.833 271.993H219.844M228.526 271.993H228.537M237.22 271.993H237.23M550.18 271.993H550.191M558.873 271.993H558.884M567.567 271.993H567.577M611.033 271.993H611.044M619.727 271.993H619.737M915.3 271.993H915.311M923.993 271.993H924.004M932.687 271.993H932.698M941.38 271.993H941.391M950.073 271.993H950.084M958.767 271.993H958.778M967.46 271.993H967.471M976.154 271.993H976.164M984.847 271.993H984.858M993.54 271.993H993.551M1002.23 271.993H1002.24M1010.93 271.993H1010.94M1019.62 271.993H1019.63M1028.31 271.993H1028.32M1037.01 271.993H1037.02M1045.7 271.993H1045.71M202.446 280.686H202.457M211.14 280.686H211.15M219.833 280.686H219.844M228.526 280.686H228.537M237.22 280.686H237.23M923.993 280.686H924.004M932.687 280.686H932.698M941.38 280.686H941.391M950.073 280.686H950.084M958.767 280.686H958.778M967.46 280.686H967.471M976.154 280.686H976.164M984.847 280.686H984.858M993.54 280.686H993.551M1002.23 280.686H1002.24M1010.93 280.686H1010.94M1019.62 280.686H1019.63M1028.31 280.686H1028.32M1037.01 280.686H1037.02M1045.7 280.686H1045.71M923.993 289.379H924.004M932.687 289.379H932.698M941.38 289.379H941.391M950.073 289.379H950.084M958.767 289.379H958.778M967.46 289.379H967.471M976.154 289.379H976.164M984.847 289.379H984.858M993.54 289.379H993.551M1002.23 289.379H1002.24M1010.93 289.379H1010.94M1019.62 289.379H1019.63M1028.31 289.379H1028.32M1037.01 289.379H1037.02M1045.7 289.379H1045.71M245.913 298.073H245.924M254.606 298.073H254.617M263.3 298.073H263.31M271.993 298.073H272.004M280.686 298.073H280.697M932.687 298.073H932.698M941.38 298.073H941.391M950.073 298.073H950.084M958.767 298.073H958.778M967.46 298.073H967.471M976.154 298.073H976.164M984.847 298.073H984.858M993.54 298.073H993.551M1002.23 298.073H1002.24M1010.93 298.073H1010.94M1019.62 298.073H1019.63M1028.31 298.073H1028.32M1037.01 298.073H1037.02M1045.7 298.073H1045.71M237.22 306.766H237.23M245.913 306.766H245.924M254.606 306.766H254.617M263.3 306.766H263.31M271.993 306.766H272.004M280.686 306.766H280.697M289.38 306.766H289.391M298.073 306.766H298.084M306.766 306.766H306.777M315.46 306.766H315.471M324.153 306.766H324.164M332.846 306.766H332.857M341.54 306.766H341.551M350.233 306.766H350.244M358.926 306.766H358.937M367.62 306.766H367.631M941.38 306.766H941.391M950.073 306.766H950.084M958.767 306.766H958.778M967.46 306.766H967.471M976.154 306.766H976.164M984.847 306.766H984.858M993.54 306.766H993.551M1002.23 306.766H1002.24M1010.93 306.766H1010.94M1019.62 306.766H1019.63M1028.31 306.766H1028.32M1037.01 306.766H1037.02M1045.7 306.766H1045.71M237.22 315.46H237.23M245.913 315.46H245.924M254.606 315.46H254.617M263.3 315.46H263.31M271.993 315.46H272.004M280.686 315.46H280.697M289.38 315.46H289.391M298.073 315.46H298.084M306.766 315.46H306.777M315.46 315.46H315.471M324.153 315.46H324.164M332.846 315.46H332.857M341.54 315.46H341.551M350.233 315.46H350.244M358.926 315.46H358.937M367.62 315.46H367.631M376.313 315.46H376.324M385.006 315.46H385.017M393.7 315.46H393.711M950.073 315.46H950.084M958.767 315.46H958.778M967.46 315.46H967.471M976.154 315.46H976.164M984.847 315.46H984.858M993.54 315.46H993.551M1002.23 315.46H1002.24M1010.93 315.46H1010.94M1019.62 315.46H1019.63M1028.31 315.46H1028.32M1037.01 315.46H1037.02M1045.7 315.46H1045.71M254.606 324.153H254.617M263.3 324.153H263.31M271.993 324.153H272.004M280.686 324.153H280.697M289.38 324.153H289.391M298.073 324.153H298.084M306.766 324.153H306.777M315.46 324.153H315.471M324.153 324.153H324.164M332.846 324.153H332.857M341.54 324.153H341.551M350.233 324.153H350.244M358.926 324.153H358.937M367.62 324.153H367.631M376.313 324.153H376.324M385.006 324.153H385.017M393.7 324.153H393.711M402.393 324.153H402.404M411.086 324.153H411.097M958.767 324.153H958.778M967.46 324.153H967.471M976.154 324.153H976.164M984.847 324.153H984.858M993.54 324.153H993.551M1002.23 324.153H1002.24M1010.93 324.153H1010.94M1019.62 324.153H1019.63M1028.31 324.153H1028.32M1037.01 324.153H1037.02M1045.7 324.153H1045.71M298.073 332.846H298.084M306.766 332.846H306.777M315.46 332.846H315.471M324.153 332.846H324.164M332.846 332.846H332.857M341.54 332.846H341.551M350.233 332.846H350.244M358.926 332.846H358.937M367.62 332.846H367.631M376.313 332.846H376.324M385.006 332.846H385.017M393.7 332.846H393.711M402.393 332.846H402.404M411.086 332.846H411.097M419.78 332.846H419.791M428.473 332.846H428.484M958.767 332.846H958.778M967.46 332.846H967.471M976.154 332.846H976.164M984.847 332.846H984.858M993.54 332.846H993.551M1002.23 332.846H1002.24M1010.93 332.846H1010.94M1019.62 332.846H1019.63M1028.31 332.846H1028.32M1037.01 332.846H1037.02M1045.7 332.846H1045.71M341.54 341.54H341.551M350.233 341.54H350.244M358.926 341.54H358.937M367.62 341.54H367.631M376.313 341.54H376.324M385.006 341.54H385.017M393.7 341.54H393.711M402.393 341.54H402.404M411.086 341.54H411.097M419.78 341.54H419.791M428.473 341.54H428.484M967.46 341.54H967.471M976.154 341.54H976.164M984.847 341.54H984.858M993.54 341.54H993.551M1002.23 341.54H1002.24M1010.93 341.54H1010.94M1019.62 341.54H1019.63M1028.31 341.54H1028.32M1037.01 341.54H1037.02M1045.7 341.54H1045.71M411.086 350.233H411.097M419.78 350.233H419.791M428.473 350.233H428.484M454.553 350.233H454.564M463.246 350.233H463.257M471.94 350.233H471.951M480.633 350.233H480.644M489.326 350.233H489.337M498.02 350.233H498.031M506.713 350.233H506.724M515.406 350.233H515.417M524.1 350.233H524.111M532.793 350.233H532.804M541.487 350.233H541.497M550.18 350.233H550.191M558.873 350.233H558.884M567.567 350.233H567.577M576.26 350.233H576.271M584.953 350.233H584.964M593.647 350.233H593.657M602.34 350.233H602.351M611.033 350.233H611.044M619.727 350.233H619.737M628.42 350.233H628.431M993.54 350.233H993.551M1002.23 350.233H1002.24M1010.93 350.233H1010.94M1019.62 350.233H1019.63M1028.31 350.233H1028.32M1037.01 350.233H1037.02M1045.7 350.233H1045.71M506.713 358.926H506.724M515.406 358.926H515.417M524.1 358.926H524.111M532.793 358.926H532.804M541.487 358.926H541.497M550.18 358.926H550.191M558.873 358.926H558.884M567.567 358.926H567.577M576.26 358.926H576.271M584.953 358.926H584.964M593.647 358.926H593.657M602.34 358.926H602.351M611.033 358.926H611.044M619.727 358.926H619.737M628.42 358.926H628.431M637.113 358.926H637.124M645.807 358.926H645.817M654.5 358.926H654.511M1045.7 358.926H1045.71M567.567 367.62H567.577M576.26 367.62H576.271M584.953 367.62H584.964M593.647 367.62H593.657M602.34 367.62H602.351M611.033 367.62H611.044M619.727 367.62H619.737M628.42 367.62H628.431M637.113 367.62H637.124M645.807 367.62H645.817M654.5 367.62H654.511M663.193 367.62H663.204M593.647 376.313H593.657M602.34 376.313H602.351M611.033 376.313H611.044M619.727 376.313H619.737M628.42 376.313H628.431M637.113 376.313H637.124M645.807 376.313H645.817M628.42 385.006H628.431"
                stroke-width="4.99867" stroke-linecap="round" stroke="#CBD5E1" />
        </svg>

        <!-- LOGIKA TITIK ORANYE KOTA DARI DATABASE -->
        @if(isset($branches) && $branches->count() > 0)
        @foreach($branches as $branch)
        <!-- Posisi berdasarkan database (position_x & position_y dalam persentase %) -->
        <div class="map-pin-wrapper" style="left: {{ $branch->position_x }}%; top: {{ $branch->position_y }}%;">
            <div class="map-pin">
                <div class="pin-pulse"></div>
                <div class="pin-core"></div>
            </div>

            <!-- Tooltip saat Hover -->
            <div class="map-tooltip">
                <div class="tooltip-box">
                    <svg class="tooltip-icon" viewBox="0 0 24 24">
                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z">
                        </path>
                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <div>
                        <span class="tooltip-city">{{ $branch->city }}</span>
                        <span class="tooltip-branch">{{ $branch->name }}</span>
                    </div>
                </div>
                <div class="tooltip-arrow"></div>
            </div>
        </div>
        @endforeach
        @else
        <!-- Sample titik cadangan jika Database belum diisi data cabang -->
        <div class="map-pin-wrapper" style="left: 28%; top: 62%;">
            <div class="map-pin">
                <div class="pin-pulse"></div>
                <div class="pin-core"></div>
            </div>
        </div>
        <div class="map-pin-wrapper" style="left: 42%; top: 68%;">
            <div class="map-pin">
                <div class="pin-pulse"></div>
                <div class="pin-core"></div>
            </div>
        </div>
        @endif

    </div>

    <!-- STATISTIK -->
    <div class="stats-container">
        <div class="stat-item">
            <h3 class="stat-value">{{ $jumlahKota ?? 0 }}+</h3>
            <p class="stat-label">Kota</p>
        </div>
        <div class="stat-item">
            <h3 class="stat-value">{{ number_format($totalKurir ?? 2400, 0, ',', '.') }}+</h3>
            <p class="stat-label">Kurir</p>
        </div>
        <div class="stat-item">
            <h3 class="stat-value">99%</h3>
            <p class="stat-label">Tepat Waktu</p>
        </div>
        <div class="stat-item">
            <h3 class="stat-value">24/7</h3>
            <p class="stat-label">Operasional</p>
        </div>
    </div>
</section>

<!-- Script Tab Interaktif -->
<script>
function switchTab(tab) {
    const btnLacak = document.getElementById('btn-lacak');
    const btnTarif = document.getElementById('btn-tarif');
    const contentLacak = document.getElementById('content-lacak');
    const contentTarif = document.getElementById('content-tarif');

    if (tab === 'lacak') {
        btnLacak.classList.add('active');
        btnTarif.classList.remove('active');
        contentLacak.classList.add('active');
        contentTarif.classList.remove('active');
    } else {
        btnTarif.classList.add('active');
        btnLacak.classList.remove('active');
        contentTarif.classList.add('active');
        contentLacak.classList.remove('active');
    }
}

function showTrackingResult() {
    const trackingResult = document.getElementById('tracking-result');
    trackingResult.style.display = 'block';
}
</script>
@endsection