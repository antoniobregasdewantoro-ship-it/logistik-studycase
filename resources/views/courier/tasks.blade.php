@extends('layouts.courier')

@section('title', 'Manifes Pengiriman - Meridian Kurir')

@section('content')
@php
    // Koordinat titik henti darat di koridor Surabaya - Sidoarjo - Mojokerto (100% di darat, bebas laut)
    $urbanWaypoints = [
        ['name' => 'Hub Pusat Logistik Surabaya', 'address' => 'Jl. Pemuda No. 18, Surabaya Pusat', 'lat' => -7.2605, 'lng' => 112.7480],
        ['name' => 'Drop Point Darmo / Wonokromo', 'address' => 'Jl. Raya Darmo No. 82, Surabaya', 'lat' => -7.2885, 'lng' => 112.7390],
        ['name' => 'Transit Hub Waru - Gedangan', 'address' => 'Jl. Raya Ahmad Yani No. 120, Sidoarjo Utara', 'lat' => -7.3450, 'lng' => 112.7290],
        ['name' => 'Depot Sidoarjo Kota', 'address' => 'Jl. Pahlawan No. 45, Sidoarjo', 'lat' => -7.4478, 'lng' => 112.7183],
        ['name' => 'Hub Transit Mojokerto Raya', 'address' => 'Jl. Mojopahit No. 110, Mojokerto', 'lat' => -7.4726, 'lng' => 112.4381],
        ['name' => 'Cabang Krian Barat', 'address' => 'Jl. Raya Krian No. 25, Krian', 'lat' => -7.4080, 'lng' => 112.5850],
    ];

    $stopsList = [];
    // Perhentian Awal: Hub Utama
    $stopsList[] = [
        'id' => 0,
        'label' => 'HUB',
        'is_hub' => true,
        'name' => 'Hub Logistik Surabaya - Sidoarjo',
        'address' => 'Jl. Pemuda No. 18, Surabaya Pusat',
        'resi' => 'HUB-ORIGIN',
        'lat' => -7.2575,
        'lng' => 112.7485,
        'status' => 'Terkirim',
        'completed' => true,
        'active' => false,
        'weight' => 0,
        'proof_url' => null,
        'notes' => 'Pusat sortir & keberangkatan armada.',
    ];

    foreach ($tasks as $idx => $t) {
        $fallback = $urbanWaypoints[($idx + 1) % count($urbanWaypoints)];

        // Validasi koordinat daratan Jawa Timur (Lat -7.1 s/d -7.8, Lng 112.3 s/d 112.85)
        $validJatimLand = $t->latitude && $t->longitude && $t->latitude < -7.0 && $t->latitude > -7.8 && $t->longitude > 112.3 && $t->longitude < 112.85;

        $lat = $validJatimLand ? (float)$t->latitude : $fallback['lat'];
        $lng = $validJatimLand ? (float)$t->longitude : $fallback['lng'];

        $stopsList[] = [
            'id' => $t->id,
            'label' => (string)($idx + 1),
            'is_hub' => false,
            'name' => $t->recipient_name ?: $t->destination,
            'address' => $t->full_address ?: ($t->destination . ', Jawa Timur'),
            'resi' => $t->tracking_number,
            'lat' => $lat,
            'lng' => $lng,
            'status' => $t->status,
            'completed' => $t->status === 'Terkirim',
            'in_transit' => $t->status === 'Dalam Perjalanan',
            'active' => ($selectedTask && $selectedTask->id === $t->id) || (!$selectedTask && $idx === 0),
            'weight' => $t->weight,
            'vehicle_type' => $t->vehicle_type,
            'vehicle_icon' => $t->vehicle_icon,
            'vehicle_label' => $t->vehicle_label,
            'scope_label' => $t->scope_label,
            'proof_url' => $t->proof_image_url,
            'notes' => $t->notes,
        ];
    }
@endphp

<div class="space-y-6">

    <!-- ============================================== -->
    <!-- TAMPILAN MANIFES 2 KOLOM DESKTOP WEB           -->
    <!-- ============================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- KOLOM KIRI: MANIFES PENGIRIMAN -->
        <div class="lg:col-span-5 space-y-4">

            <!-- MANIFEST HEADER -->
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-black tracking-widest uppercase text-[#FF6B00] block mb-1">
                        HARI INI · {{ strtoupper(now()->translatedFormat('D, d M')) }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A1128] dark:text-white tracking-tight">
                        Manifes pengiriman
                    </h1>
                    <div class="h-1 w-12 bg-[#FF6B00] rounded-full mt-2"></div>
                </div>

                <div class="text-right">
                    <span class="text-xl sm:text-2xl font-black text-[#0A1128] dark:text-white">
                        {{ $deliveredCount }}/{{ $totalCount ?: 5 }}
                    </span>
                    <span class="text-[11px] font-bold text-slate-400 block -mt-1">selesai</span>
                </div>
            </div>

            <!-- TAB FILTER CEPAT -->
            <div class="flex items-center gap-1.5 overflow-x-auto py-1">
                <a href="{{ route('courier.tasks') }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ !request('tab') ? 'bg-[#0A1128] dark:bg-white text-white dark:text-[#0A1128]' : 'bg-white dark:bg-[#0D1527] text-slate-500 border border-slate-200 dark:border-white/10 hover:text-slate-800' }}">
                    Semua ({{ $totalCount }})
                </a>
                <a href="{{ route('courier.tasks', ['tab' => 'in_transit']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('tab') === 'in_transit' ? 'bg-[#FF6B00] text-white shadow-sm' : 'bg-white dark:bg-[#0D1527] text-slate-500 border border-slate-200 dark:border-white/10 hover:text-slate-800' }}">
                    Dalam Perjalanan ({{ $inTransitCount }})
                </a>
                <a href="{{ route('courier.tasks', ['tab' => 'delivered']) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('tab') === 'delivered' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-[#0D1527] text-slate-500 border border-slate-200 dark:border-white/10 hover:text-slate-800' }}">
                    Terkirim ({{ $deliveredCount }})
                </a>
            </div>

            <!-- SHIPMENT CARDS LIST -->
            <div class="space-y-3.5 max-h-[calc(100vh-230px)] overflow-y-auto pr-1">
                @forelse($tasks as $index => $task)
                    @php
                        $isSelected = ($selectedTask && $selectedTask->id === $task->id) || (!$selectedTask && $index === 0);
                        $isDelivered = $task->status === 'Terkirim';
                        $isInTransit = $task->status === 'Dalam Perjalanan';
                    @endphp

                    <div id="task-card-{{ $task->id }}"
                         onclick="selectManifestTask({{ $task->id }}, '{{ $task->tracking_number }}', '{{ addslashes($task->recipient_name) }}', '{{ $task->status }}', {{ $index + 1 }})"
                         class="cursor-pointer bg-white dark:bg-[#0D1527] rounded-3xl p-4 sm:p-5 border transition-all duration-200 relative overflow-hidden shadow-sm hover:shadow-md {{ $isSelected ? 'border-2 border-[#FF6B00] ring-4 ring-[#FF6B00]/10' : 'border-slate-200/80 dark:border-white/10 hover:border-slate-300' }}">

                        <!-- Top Row: Icon Number/Check + Resi + Status Badge -->
                        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-white/5">
                            <div class="flex items-center gap-2.5">
                                @if($isDelivered)
                                    <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shadow-sm shadow-emerald-500/30">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                @else
                                    <div class="w-7 h-7 rounded-full bg-[#FF6B00] text-white flex items-center justify-center text-xs font-bold shadow-sm shadow-[#FF6B00]/30">
                                        {{ $index + 1 }}
                                    </div>
                                @endif
                                <span class="text-xs sm:text-sm font-black font-mono tracking-wider text-[#0A1128] dark:text-white">
                                    {{ $task->tracking_number }}
                                </span>
                            </div>

                            @if($isDelivered)
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                                    <span>Terkirim</span>
                                </span>
                            @elseif($isInTransit)
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/40 flex items-center gap-1.5">
                                    <i class="fa-solid fa-truck-fast text-[10px]"></i>
                                    <span>Dalam perjalanan</span>
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40 flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock text-[10px]"></i>
                                    <span>Verifikasi</span>
                                </span>
                            @endif
                        </div>

                        <!-- 2-Column Addresses (PENJEMPUTAN vs PENGANTARAN) -->
                        <div class="grid grid-cols-2 gap-4 py-3.5 text-left">
                            <div class="space-y-0.5">
                                <div class="flex items-center justify-between text-[10px] font-extrabold uppercase text-slate-400">
                                    <span>Penjemputan</span>
                                    <span>08:30</span>
                                </div>
                                <p class="text-xs font-black text-slate-900 dark:text-white truncate">
                                    {{ $task->origin }} Depot
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                    Hub Pusat Logistik
                                </p>
                            </div>

                            <div class="space-y-0.5">
                                <div class="flex items-center justify-between text-[10px] font-extrabold uppercase text-slate-400">
                                    <span>Pengantaran</span>
                                    <span>{{ $isDelivered ? '09:10' : '10:30' }}</span>
                                </div>
                                <p class="text-xs font-black text-slate-900 dark:text-white truncate">
                                    {{ $task->recipient_name }}
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate" title="{{ $task->full_address }}">
                                    {{ $task->full_address }}
                                </p>
                            </div>
                        </div>

                        <!-- Bottom Row: Weight & Vehicle Badge & Time Window & Proof Button -->
                        <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-[11px] font-medium">
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $task->weight }} kg</span>
                                <span>•</span>
                                @if($task->vehicle_type === 'pickup')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-orange-100 dark:bg-orange-950/60 text-[#FF6B00] border border-orange-300 dark:border-orange-800/40" title="Kirim Luar Kota dengan Mobil Pickup">
                                        <i class="fa-solid fa-truck-pickup"></i>
                                        <span>Pickup (Luar Kota)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-300 dark:border-blue-800/40" title="Kirim di Kecamatan dengan Sepeda">
                                        <i class="fa-solid fa-bicycle"></i>
                                        <span>Sepeda (Kecamatan)</span>
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('resi.cetak', $task->tracking_number) }}" target="_blank" onclick="event.stopPropagation()"
                                   class="px-2 py-1 rounded-xl bg-orange-50 dark:bg-orange-950/40 hover:bg-[#FF6B00] text-[#FF6B00] hover:text-white text-[11px] font-bold flex items-center gap-1 transition border border-orange-200 dark:border-orange-500/20"
                                   title="Cetak Resi">
                                    <i class="fa-solid fa-print"></i>
                                    <span>Resi</span>
                                </a>

                                <span class="text-[11px] text-slate-400 font-mono">
                                    {{ $isDelivered ? '08:30 - 09:30' : '09:45 - 11:00' }}
                                </span>

                                @if($task->proof_image)
                                    <button type="button"
                                            onclick="event.stopPropagation(); openPhotoLightbox('{{ $task->proof_image_url }}', '{{ $task->tracking_number }}', '{{ addslashes($task->notes ?? 'Paket telah diserahterimakan dengan aman.') }}')"
                                            class="px-2.5 py-1 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold flex items-center gap-1 transition shadow-sm">
                                        <i class="fa-solid fa-camera"></i>
                                        <span>Lihat Bukti</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-8 border border-slate-200 dark:border-white/10 text-center">
                        <i class="fa-solid fa-inbox text-3xl text-slate-300 dark:text-slate-600 mb-2"></i>
                        <p class="text-xs font-bold text-slate-500">Tidak ada manifes aktif pada kategori ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- KOLOM KANAN: MAP RUTE, LIVE TRACKING & FLOATING BOTTOM BAR -->
        <div class="lg:col-span-7 space-y-4 lg:sticky lg:top-16">

            <!-- LEAFLET ROUTE MAP CONTAINER (PAS DI LAYAR LAPTOP & DESKTOP) -->
            <div class="relative w-full rounded-3xl overflow-hidden border border-slate-200 dark:border-white/10 shadow-xl bg-[#070C1D] h-[480px] lg:h-[calc(100vh-140px)] min-h-[460px] max-h-[620px]">

                <!-- Map Canvas -->
                <div id="courierMap" class="w-full h-full z-0"></div>

                <!-- FLOATING TOP-LEFT BADGE: RUTE OPTIMAL (Persis Figma) -->
                <div class="absolute top-4 left-4 z-[800] bg-white/95 dark:bg-[#0D1527]/95 backdrop-blur-md rounded-2xl p-3 sm:p-4 shadow-xl border border-slate-100 dark:border-white/10 flex items-center gap-3 sm:gap-4">
                    <div>
                        <span class="text-[10px] font-black tracking-widest uppercase text-slate-400 block mb-0.5">
                            RUTE OPTIMAL
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm sm:text-lg font-black text-slate-900 dark:text-white">38.4 km</span>
                            <span class="text-sm sm:text-lg font-black text-slate-900 dark:text-white">5j 12m</span>
                        </div>
                    </div>
                    <span class="px-2 py-1 sm:px-2.5 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 whitespace-nowrap">
                        -14% dari rencana
                    </span>
                </div>

                <!-- FLOATING TOP-RIGHT BADGE: LIVE TELEMETRY & KURIR HUD -->
                <div class="absolute top-4 right-4 z-[800] bg-[#0A1128]/95 dark:bg-[#070C1D]/95 backdrop-blur-md text-white rounded-2xl p-2.5 sm:p-3 shadow-xl border border-white/10 flex items-center gap-2.5">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <div class="text-left hidden sm:block">
                            <span class="text-[9px] font-black uppercase tracking-wider text-emerald-400 block leading-none">Live Tracking</span>
                            <span class="text-[11px] font-mono font-bold text-slate-200" id="liveSpeedText">36 km/jam</span>
                        </div>
                    </div>

                    <div class="h-6 w-px bg-white/15"></div>

                    <!-- Button: Center Map to Live Courier -->
                    <button type="button" onclick="centerToLiveCourier()" title="Pusatkan Peta ke Posisi Kurir"
                            class="w-7 h-7 rounded-xl bg-white/10 hover:bg-[#FF6B00] text-white flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-crosshairs"></i>
                    </button>

                    <!-- Button: Toggle Simulation Play/Pause -->
                    <button type="button" onclick="toggleSimulation()" id="btnToggleSim" title="Jeda / Lanjutkan Perjalanan Kurir"
                            class="w-7 h-7 rounded-xl bg-white/10 hover:bg-[#FF6B00] text-white flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-pause" id="iconToggleSim"></i>
                    </button>
                </div>

                <!-- FLOATING MARKER CALLOUT PILL -->
                <div id="floatingRouteCallout"
                     class="absolute top-[88px] left-4 sm:left-6 z-[800] bg-[#0A1128]/95 text-white px-3.5 py-1.5 rounded-xl shadow-2xl text-[11px] font-bold flex items-center gap-2 border border-white/10 pointer-events-none transition-all duration-300">
                    <span class="w-2 h-2 rounded-full bg-[#FF6B00] animate-ping"></span>
                    <span id="calloutText">Berikutnya: Perhentian {{ $selectedTask ? 2 : 1 }} · {{ $selectedTask->recipient_name ?? 'Toko Sentosa Mojokerto' }}</span>
                </div>

                <!-- FLOATING BOTTOM DARK BAR: CONTROL & AKSI (TIDAK TERPOTONG) -->
                <div class="absolute bottom-3 sm:bottom-4 left-3 sm:left-4 right-3 sm:right-4 z-[800]">
                    <div class="bg-[#0A1128]/95 dark:bg-[#070C1D]/95 backdrop-blur-md text-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 sm:px-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xl border border-white/10">

                        <!-- Selected Stop Info -->
                        <div class="space-y-0.5 text-left">
                            <span class="text-[10px] font-black tracking-widest uppercase text-slate-400 block" id="selectedStopHeader">
                                DIPILIH · PERHENTIAN {{ $selectedTask ? 2 : 1 }}
                            </span>
                            <h3 class="text-sm sm:text-base font-black text-white tracking-tight truncate max-w-xs" id="selectedStopTitle">
                                {{ $selectedTask->recipient_name ?? 'Toko Sentosa Mojokerto' }}
                            </h3>
                            <p class="text-[11px] text-slate-400 font-mono truncate" id="selectedStopSub">
                                {{ $selectedTask->tracking_number ?? 'MRD-48209' }} • {{ $selectedTask->status ?? 'Dalam Perjalanan' }}
                            </p>
                        </div>

                        <!-- Right Buttons -->
                        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                            <!-- Tandai Terkirim Button -->
                            <button type="button" onclick="openConfirmDeliveredModal()"
                                    class="px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fa-solid fa-check text-emerald-400"></i>
                                <span>Tandai terkirim</span>
                            </button>

                            <!-- Pindai QR / Upload Bukti (Orange Button) -->
                            <button type="button" onclick="openProofModal()"
                                    class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl sm:rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white font-black text-xs uppercase tracking-wider transition shadow-lg shadow-[#FF6B00]/40 flex items-center gap-1.5 sm:gap-2">
                                <i class="fa-solid fa-camera text-xs sm:text-sm"></i>
                                <span>Pindai / Upload Bukti</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- ======================================================== -->
<!-- MODAL CUSTOM KONFIRMASI TERKIRIM (Z-[99999] ON TOP)     -->
<!-- ======================================================== -->
<div id="confirmDeliveredModal" onclick="if(event.target === this) closeConfirmDeliveredModal()" class="fixed inset-0 z-[99999] bg-black/85 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200 dark:border-white/10 animate-in fade-in zoom-in duration-200 relative z-[100000]">
        <div class="p-6 text-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-3xl mx-auto shadow-inner">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-black text-[#0A1128] dark:text-white">Konfirmasi Serah Terima Paket</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                    Apakah Anda yakin ingin menandai paket <strong id="confirmResiText" class="text-[#FF6B00] font-mono">{{ $selectedTask->tracking_number ?? 'MRD-48209' }}</strong> sebagai <span class="text-emerald-500 font-bold">TERKIRIM</span>?
                </p>
            </div>
            <div class="p-3.5 bg-slate-50 dark:bg-white/5 rounded-2xl text-[11px] text-slate-500 dark:text-slate-400 text-left border border-slate-100 dark:border-white/5">
                <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i>
                Status akan langsung diperbarui di portal Admin & live tracking pelanggan secara instan.
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeConfirmDeliveredModal()" class="flex-1 py-3 rounded-2xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-white/5 transition">
                    Batal
                </button>
                <button type="button" onclick="submitQuickStatus()" class="flex-1 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider transition shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Ya, Terkirim</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL UPLOAD BUKTI / PINDAI QR (Z-[99999] ON TOP)       -->
<!-- ======================================================== -->
<div id="uploadProofModal" onclick="if(event.target === this) closeProofModal()" class="fixed inset-0 z-[99999] bg-black/85 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200 dark:border-white/10 animate-in fade-in zoom-in duration-200 relative z-[100000]">

        <!-- Modal Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-black text-[#0A1128] dark:text-white">Pindai QR / Upload Bukti Pengiriman</h3>
                    <p id="modalTrackingResi" class="text-xs font-mono font-bold text-[#FF6B00]">
                        {{ $selectedTask->tracking_number ?? 'MRD-48209' }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeProofModal()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-white/10 text-slate-500 hover:text-rose-500 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Form Upload -->
        <form id="proofUploadForm" action="{{ route('courier.tasks.upload-proof', $selectedTask->id ?? 1) }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-6 space-y-4">
            @csrf

            <!-- Mode Switcher -->
            <div class="flex items-center gap-2 p-1 bg-slate-100 dark:bg-white/5 rounded-2xl">
                <button type="button" onclick="switchProofMode('upload')" id="tabModeUpload" class="flex-1 py-2 rounded-xl text-xs font-bold bg-white dark:bg-[#0D1527] text-[#0A1128] dark:text-white shadow-sm transition">
                    <i class="fa-solid fa-cloud-arrow-up mr-1 text-[#FF6B00]"></i> Upload Foto
                </button>
                <button type="button" onclick="switchProofMode('qr')" id="tabModeQr" class="flex-1 py-2 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white transition">
                    <i class="fa-solid fa-qrcode mr-1 text-[#FF6B00]"></i> Pindai Barcode QR
                </button>
            </div>

            <!-- Tab 1: Upload Foto Bukti -->
            <div id="sectionUploadProof">
                <label class="text-xs font-black text-slate-700 dark:text-slate-300 block mb-2">
                    Foto Bukti Serah Terima (JPG / JPEG / PNG, Batas 2MB) *
                </label>

                <!-- Dropzone Box -->
                <div onclick="document.getElementById('proofImageFileInput').click()"
                     ondragover="event.preventDefault(); this.classList.add('border-[#FF6B00]')"
                     ondragleave="event.preventDefault(); this.classList.remove('border-[#FF6B00]')"
                     ondrop="event.preventDefault(); this.classList.remove('border-[#FF6B00]'); handleFileDrop(event)"
                     class="border-2 border-dashed border-slate-200 dark:border-white/15 hover:border-[#FF6B00] rounded-3xl p-5 sm:p-6 text-center cursor-pointer bg-slate-50/70 dark:bg-white/5 transition group">
                    <input type="file" name="proof_image" id="proofImageFileInput" accept="image/jpeg,image/png,image/jpg" required class="hidden" onchange="handleFileChosen(this)">

                    <div id="dropzonePlaceholder" class="space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-slate-200/60 dark:bg-white/10 text-slate-500 group-hover:text-[#FF6B00] group-hover:scale-110 flex items-center justify-center mx-auto transition">
                            <i class="fa-solid fa-camera text-xl"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            Sentuh atau seret foto bukti serah terima ke sini
                        </p>
                        <p class="text-[11px] text-slate-400">
                            Validasi sistem otomatis: Maksimal ukuran file 2048 KB (2MB)
                        </p>
                    </div>

                    <!-- File Chosen Preview -->
                    <div id="dropzonePreview" class="hidden space-y-3">
                        <img id="filePreviewImg" src="" alt="Bukti Foto" class="max-h-44 mx-auto rounded-2xl shadow-md object-contain border border-slate-200 dark:border-white/10">
                        <div class="flex items-center justify-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                            <i class="fa-solid fa-circle-check"></i>
                            <span id="fileChosenName">bukti_pengiriman.jpg</span>
                            <span id="fileChosenSize" class="text-slate-400 font-mono"></span>
                        </div>
                        <button type="button" onclick="event.stopPropagation(); resetChosenFile()" class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-white/10 text-[11px] text-slate-600 dark:text-slate-300 font-bold hover:text-rose-500 transition">
                            Ganti Foto
                        </button>
                    </div>
                </div>

                <div id="fileSizeError" class="hidden mt-2 p-2.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 text-xs font-bold flex items-center gap-2 border border-rose-200 dark:border-rose-900/40">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 shrink-0"></i>
                    <span>Ukuran foto melebihi batas 2MB (2048 KB). Mohon pilih foto dengan kompresi lebih ringan.</span>
                </div>
            </div>

            <!-- Tab 2: Pindai Barcode QR Kamera / Simulasi -->
            <div id="sectionScanQr" class="hidden text-center p-6 bg-slate-50 dark:bg-white/5 rounded-3xl border border-slate-200 dark:border-white/10 space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-[#FF6B00]/10 text-[#FF6B00] flex items-center justify-center text-3xl mx-auto animate-pulse">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Pindai Barcode Resi Pelanggan</h4>
                <p class="text-[11px] text-slate-400 max-w-xs mx-auto">
                    Arahkan kamera kurir ke barcode QR pada label paket untuk konfirmasi serah terima otomatis.
                </p>
                <button type="button" onclick="simulateQrScanned()"
                        class="px-5 py-2.5 rounded-2xl bg-[#0A1128] dark:bg-white text-white dark:text-[#0A1128] text-xs font-bold shadow-md transition hover:scale-105">
                    <i class="fa-solid fa-barcode mr-1.5 text-[#FF6B00]"></i> Simulasi Pindai QR Berhasil
                </button>
            </div>

            <!-- Catatan Pengantaran -->
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400">Catatan Pengantaran (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Diterima oleh satpam / keluarga penerima"
                       class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-xs text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00]">
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-end gap-3">
                <button type="button" onclick="closeProofModal()" class="px-5 py-2.5 rounded-2xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" id="submitProofBtn"
                        class="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider transition shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Simpan & Tandai Terkirim</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- FORM QUICK STATUS HIDDEN -->
<form id="quickStatusForm" action="{{ route('courier.tasks.status', $selectedTask->id ?? 1) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="status" value="Terkirim">
    <input type="hidden" name="checkpoint" id="quickCheckpointInput" value="Paket berhasil diserahterimakan di alamat tujuan">
</form>

@endsection

@push('scripts')
<script>
    const stopsData = @json($stopsList);
    let currentSelectedId = {{ $selectedTask->id ?? ($tasks->first()->id ?? 1) }};

    let courierMap = null;
    let mapMarkers = [];
    let routePolyline = null;
    let courierVehicleMarker = null;

    // State Live Tracking
    let isSimulating = true;
    let simInterval = null;
    let currentLegIndex = 0;
    let currentStepInLeg = 0;
    const stepsPerLeg = 14; // Jumlah interpolasi halus antar titik henti
    let activeWaypoints = [];

    document.addEventListener('DOMContentLoaded', function() {
        @if($isOnline)
            initCourierLiveMap();
        @endif
    });

    function initCourierLiveMap() {
        const mapEl = document.getElementById('courierMap');
        if (!mapEl) return;

        // Ambil titik awal (Hub atau stop pertama)
        const initLat = stopsData[0]?.lat || -7.2605;
        const initLng = stopsData[0]?.lng || 112.7480;

        courierMap = L.map('courierMap', {
            zoomControl: false,
            attributionControl: false
        }).setView([initLat, initLng], 13);

        // OpenStreetMap standard tile (Bebas Watermark & Tanpa API Key)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors | Meridian Live Routing'
        }).addTo(courierMap);

        L.control.zoom({ position: 'topright' }).addTo(courierMap);

        // Render Stop Markers & Polyline
        renderRouteElements();

        // Setup Live Courier Vehicle Marker
        setupLiveVehicleMarker();

        // Mulai simulasi live pergerakan kendaraan kurir di peta
        startLiveSimulation();
    }

    function renderRouteElements() {
        if (!stopsData || stopsData.length === 0) return;

        const latlngs = stopsData.map(s => [s.lat, s.lng]);

        // Polyline Rute Pengantaran (Safety Orange Dashed)
        routePolyline = L.polyline(latlngs, {
            color: '#FF6B00',
            weight: 4,
            dashArray: '8, 8',
            opacity: 0.9,
            lineJoin: 'round'
        }).addTo(courierMap);

        // Marker Setiap Perhentian
        stopsData.forEach((stop, idx) => {
            const isCompleted = stop.completed;
            const isActive = stop.active;
            const isHub = stop.is_hub;

            let iconHtml = '';
            if (isHub) {
                iconHtml = `
                    <div class="w-8 h-8 rounded-full bg-[#0A1128] text-white flex items-center justify-center font-black text-[10px] shadow-xl border-2 border-[#FF6B00]">
                        <i class="fa-solid fa-warehouse text-[11px] text-[#FF6B00]"></i>
                    </div>`;
            } else if (isCompleted) {
                iconHtml = `
                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shadow-lg border-2 border-white">
                        <i class="fa-solid fa-check"></i>
                    </div>`;
            } else if (isActive) {
                iconHtml = `
                    <div class="relative flex items-center justify-center">
                        <div class="w-9 h-9 rounded-full bg-[#FF6B00] text-white flex items-center justify-center font-black text-xs shadow-2xl border-2 border-white">
                            ${stop.label}
                        </div>
                        <div class="absolute -inset-1.5 rounded-full bg-[#FF6B00]/40 animate-ping"></div>
                    </div>`;
            } else {
                iconHtml = `
                    <div class="w-8 h-8 rounded-full bg-[#0A1128] text-white flex items-center justify-center font-black text-xs shadow-lg border-2 border-white/60">
                        ${stop.label}
                    </div>`;
            }

            const customIcon = L.divIcon({
                html: iconHtml,
                className: `stop-marker-${stop.id}`,
                iconSize: [36, 36],
                iconAnchor: [18, 18]
            });

            const marker = L.marker([stop.lat, stop.lng], { icon: customIcon }).addTo(courierMap);
            marker.bindPopup(`
                <div class="p-2 text-xs font-sans">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">${isHub ? 'Hub Asal' : 'Perhentian ' + stop.label}</span>
                    <strong class="text-slate-900 block font-black text-sm">${stop.name}</strong>
                    <span class="text-slate-500 block text-[11px] mt-0.5">${stop.address}</span>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold ${isCompleted ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-[#FF6B00]'}">${stop.status}</span>
                </div>
            `);

            marker.on('click', () => {
                if (!isHub) {
                    selectManifestTask(stop.id, stop.resi, stop.name, stop.status, parseInt(stop.label));
                }
            });

            mapMarkers.push({ id: stop.id, marker: marker });
        });

        // Kunci bounds pada jalur rute dengan bantalan nyaman agar tidak melayang ke laut
        courierMap.fitBounds(routePolyline.getBounds(), { padding: [60, 60], maxZoom: 14 });
        setTimeout(() => { if (courierMap) courierMap.invalidateSize(); }, 250);
        window.addEventListener('resize', () => { if (courierMap) courierMap.invalidateSize(); });
    }

    function setupLiveVehicleMarker() {
        if (!stopsData || stopsData.length === 0) return;

        const startPt = [stopsData[0].lat, stopsData[0].lng];

        // Tentukan ikon awal: Luar Kota = Mobil Pickup, Di Kecamatan = Sepeda
        const initialStop = stopsData.find(s => s.active) || stopsData.find(s => !s.is_hub) || stopsData[0];
        const isLuarKota = initialStop && initialStop.vehicle_type === 'pickup';
        const initIconClass = isLuarKota ? 'fa-solid fa-truck-pickup' : 'fa-solid fa-bicycle';

        // Custom HTML Marker: Kendaraan Bergerak dengan Efek Gelombang Radar GPS
        const vehicleHtml = `
            <div id="courierVehicleEl" class="relative flex items-center justify-center">
                <span class="absolute w-12 h-12 rounded-full bg-[#FF6B00]/30 animate-ping"></span>
                <span class="absolute w-8 h-8 rounded-full bg-emerald-500/25 animate-pulse"></span>
                <div class="relative w-11 h-11 rounded-full bg-[#0A1128] border-2 border-[#FF6B00] shadow-2xl flex items-center justify-center text-white text-sm">
                    <i class="${initIconClass} text-[#FF6B00] transition-transform duration-500 text-base" id="vehicleCompassIcon"></i>
                </div>
                <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white shadow"></span>
            </div>
        `;

        const vehicleIcon = L.divIcon({
            html: vehicleHtml,
            className: 'courier-live-icon',
            iconSize: [44, 44],
            iconAnchor: [22, 22]
        });

        courierVehicleMarker = L.marker(startPt, { icon: vehicleIcon, zIndexOffset: 1000 }).addTo(courierMap);
        courierVehicleMarker.bindPopup(`
            <div class="p-1 text-xs">
                <strong class="text-[#FF6B00] font-black">Meridian Kurir Live</strong><br>
                <span>Armada sedang bergerak aktif di rute manifes.</span>
            </div>
        `);
    }

    // Engine Simulasi Perjalanan Live (GPS Interpolation)
    function startLiveSimulation() {
        if (stopsData.length < 2) return;

        // Cari leg pertama yang belum terkirim
        let activeIdx = stopsData.findIndex((s, i) => i > 0 && !s.completed);
        if (activeIdx === -1) activeIdx = 1;
        currentLegIndex = activeIdx - 1;
        currentStepInLeg = 0;

        simInterval = setInterval(advanceLiveCourier, 1200);
    }

    function advanceLiveCourier() {
        if (!isSimulating || !courierVehicleMarker || stopsData.length < 2) return;

        const fromStop = stopsData[currentLegIndex];
        const toStop = stopsData[currentLegIndex + 1];

        if (!toStop) {
            // Putar kembali ke awal jika seluruh rute selesai
            currentLegIndex = 0;
            currentStepInLeg = 0;
            return;
        }

        currentStepInLeg++;
        const ratio = currentStepInLeg / stepsPerLeg;

        // Interpolasi Latitude & Longitude
        const curLat = fromStop.lat + (toStop.lat - fromStop.lat) * ratio;
        const curLng = fromStop.lng + (toStop.lng - fromStop.lng) * ratio;

        courierVehicleMarker.setLatLng([curLat, curLng]);

        // Hitung sudut rotasi arah kendaraan (bearing)
        const dLng = toStop.lng - fromStop.lng;
        const dLat = toStop.lat - fromStop.lat;
        const angleDeg = (Math.atan2(dLng, dLat) * 180 / Math.PI);
        const compassIcon = document.getElementById('vehicleCompassIcon');
        if (compassIcon) {
            compassIcon.style.transform = `rotate(${angleDeg}deg)`;
        }

        // Variasi kecepatan alami (30 - 42 km/jam)
        const speed = Math.floor(32 + Math.random() * 11);
        const speedTextEl = document.getElementById('liveSpeedText');
        if (speedTextEl) {
            speedTextEl.textContent = `${speed} km/jam`;
        }

        // Jika sampai di titik tujuan
        if (currentStepInLeg >= stepsPerLeg) {
            currentStepInLeg = 0;
            currentLegIndex++;

            if (currentLegIndex >= stopsData.length - 1) {
                currentLegIndex = 0; // loop
            }

            // Update callout berikutnya
            const nextStop = stopsData[currentLegIndex + 1];
            if (nextStop) {
                document.getElementById('calloutText').textContent = `Berikutnya: Perhentian ${nextStop.label} · ${nextStop.name}`;
            }
        }
    }

    function toggleSimulation() {
        isSimulating = !isSimulating;
        const icon = document.getElementById('iconToggleSim');
        const btn = document.getElementById('btnToggleSim');

        if (isSimulating) {
            icon.className = 'fa-solid fa-pause';
            btn.classList.remove('bg-[#FF6B00]');
            btn.classList.add('bg-white/10');
        } else {
            icon.className = 'fa-solid fa-play';
            btn.classList.add('bg-[#FF6B00]');
            btn.classList.remove('bg-white/10');
        }
    }

    function centerToLiveCourier() {
        if (courierMap && courierVehicleMarker) {
            courierMap.flyTo(courierVehicleMarker.getLatLng(), 15, { duration: 1.0 });
        }
    }

    function selectManifestTask(id, resi, recipient, status, labelIndex) {
        currentSelectedId = id;

        // Update highlight card kiri
        document.querySelectorAll('[id^="task-card-"]').forEach(card => {
            card.classList.remove('border-2', 'border-[#FF6B00]', 'ring-4', 'ring-[#FF6B00]/10');
            card.classList.add('border-slate-200/80', 'dark:border-white/10');
        });
        const activeCard = document.getElementById(`task-card-${id}`);
        if (activeCard) {
            activeCard.classList.remove('border-slate-200/80', 'dark:border-white/10');
            activeCard.classList.add('border-2', 'border-[#FF6B00]', 'ring-4', 'ring-[#FF6B00]/10');
        }

        // Update floating bar
        document.getElementById('selectedStopHeader').textContent = 'DIPILIH · PERHENTIAN ' + labelIndex;
        document.getElementById('selectedStopTitle').textContent = recipient;
        document.getElementById('selectedStopSub').textContent = resi + ' • ' + status;
        document.getElementById('calloutText').textContent = 'Berikutnya: Perhentian ' + labelIndex + ' · ' + recipient;

        // Update modal forms
        document.getElementById('modalTrackingResi').textContent = resi;
        document.getElementById('confirmResiText').textContent = resi;
        document.getElementById('proofUploadForm').action = '/courier/tasks/' + id + '/upload-proof';
        document.getElementById('quickStatusForm').action = '/courier/tasks/' + id + '/status';

        // Cari koordinat stop & ubah icon kendaraan di map
        const targetStop = stopsData.find(s => s.id === id);
        if (targetStop) {
            const compassIcon = document.getElementById('vehicleCompassIcon');
            if (compassIcon && targetStop.vehicle_icon) {
                compassIcon.className = `${targetStop.vehicle_icon} text-[#FF6B00] transition-transform duration-500 text-base`;
            }
            if (courierMap) {
                courierMap.flyTo([targetStop.lat, targetStop.lng], 14, { duration: 1.0 });
            }
        }
    }

    // Modal Confirmation Custom Handlers
    function openConfirmDeliveredModal() {
        document.getElementById('confirmDeliveredModal').classList.remove('hidden');
    }
    function closeConfirmDeliveredModal() {
        document.getElementById('confirmDeliveredModal').classList.add('hidden');
    }
    function submitQuickStatus() {
        document.getElementById('quickStatusForm').submit();
    }

    // Modal Upload Proof Handlers
    function openProofModal() {
        document.getElementById('uploadProofModal').classList.remove('hidden');
    }
    function closeProofModal() {
        document.getElementById('uploadProofModal').classList.add('hidden');
    }

    function switchProofMode(mode) {
        const tabUpload = document.getElementById('tabModeUpload');
        const tabQr = document.getElementById('tabModeQr');
        const secUpload = document.getElementById('sectionUploadProof');
        const secQr = document.getElementById('sectionScanQr');
        const fileInput = document.getElementById('proofImageFileInput');

        if (mode === 'upload') {
            tabUpload.classList.add('bg-white', 'dark:bg-[#0D1527]', 'shadow-sm', 'text-[#0A1128]', 'dark:text-white');
            tabUpload.classList.remove('text-slate-500');
            tabQr.classList.remove('bg-white', 'dark:bg-[#0D1527]', 'shadow-sm', 'text-[#0A1128]', 'dark:text-white');
            tabQr.classList.add('text-slate-500');
            secUpload.classList.remove('hidden');
            secQr.classList.add('hidden');
            fileInput.required = true;
        } else {
            tabQr.classList.add('bg-white', 'dark:bg-[#0D1527]', 'shadow-sm', 'text-[#0A1128]', 'dark:text-white');
            tabQr.classList.remove('text-slate-500');
            tabUpload.classList.remove('bg-white', 'dark:bg-[#0D1527]', 'shadow-sm', 'text-[#0A1128]', 'dark:text-white');
            tabUpload.classList.add('text-slate-500');
            secQr.classList.remove('hidden');
            secUpload.classList.add('hidden');
            fileInput.required = false;
        }
    }

    function simulateQrScanned() {
        submitQuickStatus();
    }

    // Strict 2MB File Validation & Preview
    function handleFileChosen(input) {
        if (!input.files || !input.files[0]) return;
        processFile(input.files[0]);
    }

    function handleFileDrop(event) {
        const files = event.dataTransfer.files;
        if (files && files[0]) {
            document.getElementById('proofImageFileInput').files = files;
            processFile(files[0]);
        }
    }

    function processFile(file) {
        const errorBox = document.getElementById('fileSizeError');
        const submitBtn = document.getElementById('submitProofBtn');
        const maxBytes = 2 * 1024 * 1024; // 2MB
        const sizeKb = Math.round(file.size / 1024);

        if (file.size > maxBytes) {
            errorBox.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('proofImageFileInput').value = '';
            document.getElementById('dropzonePlaceholder').classList.remove('hidden');
            document.getElementById('dropzonePreview').classList.add('hidden');
            return;
        }

        errorBox.classList.add('hidden');
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

        document.getElementById('fileChosenName').textContent = file.name;
        document.getElementById('fileChosenSize').textContent = `(${sizeKb} KB)`;

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('filePreviewImg').src = e.target.result;
            document.getElementById('dropzonePlaceholder').classList.add('hidden');
            document.getElementById('dropzonePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    function resetChosenFile() {
        document.getElementById('proofImageFileInput').value = '';
        document.getElementById('dropzonePlaceholder').classList.remove('hidden');
        document.getElementById('dropzonePreview').classList.add('hidden');
        document.getElementById('fileSizeError').classList.add('hidden');
    }
</script>
@endpush
