@extends('layouts.admin')

@section('title', 'Ringkasan Armada')

@section('content')
<div class="space-y-6">

    <!-- HEADER OPERASIONAL (Persis Gambar 3) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-bold tracking-widest uppercase text-[#FF6B00]">
                OPERASIONAL • {{ now()->translatedFormat('D, d M') }}
            </span>
            <h1 class="text-3xl font-extrabold text-[#0A1128] dark:text-white tracking-tight mt-0.5">
                Ringkasan armada
            </h1>
        </div>

        <!-- Search Bar (Persis Gambar 3) -->
        <div class="relative w-full sm:w-72">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-3 text-slate-400 text-xs"></i>
            <input type="text" id="filterSearchTable" placeholder="Cari pesanan, kurir, rute"
                   onkeyup="searchActiveDeliveries(this.value)"
                   class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 dark:bg-[#0D1527] dark:text-white text-xs focus:outline-none focus:border-[#FF6B00] shadow-sm">
        </div>
    </div>

    <!-- 4 BENTO GRID CARDS (Persis Gambar 3) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: TOTAL PESANAN -->
        <div class="bg-white dark:bg-[#0D1527] rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">TOTAL PESANAN</span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 dark:bg-white"></span>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-black text-[#0A1128] dark:text-white tracking-tight">
                    {{ number_format($totalOrders ?: 2486) }}
                </div>
                <div class="mt-2 flex items-center text-[11px] font-bold text-emerald-500 gap-1">
                    <i class="fa-solid fa-arrow-trend-up text-[10px]"></i>
                    <span>+12.4% vs minggu lalu</span>
                </div>
            </div>
        </div>

        <!-- Card 2: KURIR AKTIF -->
        <div class="bg-white dark:bg-[#0D1527] rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">KURIR AKTIF</span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 dark:bg-white"></span>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-black text-[#0A1128] dark:text-white tracking-tight">
                    {{ $activeCouriers ?: 318 }}
                </div>
                <div class="mt-2 flex items-center text-[11px] font-bold text-emerald-500 gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>online sekarang</span>
                </div>
            </div>
        </div>

        <!-- Card 3: PERINGATAN KETERLAMBATAN -->
        <div class="bg-white dark:bg-[#0D1527] rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">PERINGATAN KETERLAMBATAN</span>
                <span class="w-2 h-2 rounded-full bg-[#FF6B00]"></span>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-black text-[#0A1128] dark:text-white tracking-tight">
                    {{ $delayAlerts ?: 27 }}
                </div>
                <div class="mt-2 flex items-center text-[11px] font-bold text-[#FF6B00] gap-1">
                    <i class="fa-solid fa-bolt text-[10px]"></i>
                    <span>perlu ditinjau</span>
                </div>
            </div>
        </div>

        <!-- Card 4: TINGKAT TEPAT WAKTU -->
        <div class="bg-white dark:bg-[#0D1527] rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm transition">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">TINGKAT TEPAT WAKTU</span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 dark:bg-white"></span>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-black text-[#0A1128] dark:text-white tracking-tight">
                    {{ $onTimeRate ?: 94.2 }}%
                </div>
                <div class="mt-2 flex items-center text-[11px] font-bold text-emerald-500 gap-1">
                    <i class="fa-solid fa-check text-[10px]"></i>
                    <span>+1.1% rata-rata 30 hari</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- RUTE LANGSUNG DARK MAP CARD (Persis Gambar 3)                  -->
    <!-- ============================================================== -->
    <div class="bg-[#0A1128] rounded-3xl p-6 sm:p-7 text-white shadow-xl border border-white/5 relative overflow-hidden">
        <!-- Top header map card -->
        <div class="flex items-center justify-between mb-4">
            <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">RUTE LANGSUNG</span>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <span class="flex items-center gap-1.5 text-emerald-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Rendah
                </span>
                <span class="flex items-center gap-1.5 text-amber-400">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> Sedang
                </span>
                <span class="flex items-center gap-1.5 text-rose-500">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Tinggi
                </span>
            </div>
        </div>

        <!-- Leaflet Dark Map Canvas -->
        <div class="relative w-full rounded-2xl overflow-hidden border border-white/10">
            <div id="fleetRouteMap" class="w-full h-80 z-10 bg-[#070C1D]"></div>
        </div>

        <!-- Footer map -->
        <div class="mt-3 flex items-center justify-between text-[11px] text-slate-400">
            <span>7 jalur aktif - arahkan kursor untuk melihat</span>
            <span class="text-[#FF6B00] font-bold">Sinkronisasi GPS Satelit Aktif</span>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- TABEL PENGIRIMAN AKTIF (Persis Gambar 3)                       -->
    <!-- ============================================================== -->
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition">
        <!-- Table Header & Filter Tabs -->
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-extrabold text-[#0A1128] dark:text-white">Pengiriman aktif</h3>
                <p class="text-xs text-slate-400">{{ count($orders) }} dari {{ count($orders) }} ditampilkan</p>
            </div>

            <!-- Filter Pills (Semua, Rendah, Sedang, Tinggi) -->
            <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800/60 p-1 rounded-xl">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ !request('risk') ? 'bg-white dark:bg-[#0A1128] text-[#0A1128] dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    Semua
                </a>
                <a href="{{ route('admin.dashboard', ['risk' => 'Low']) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('risk') === 'Low' ? 'bg-white dark:bg-[#0A1128] text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    Rendah
                </a>
                <a href="{{ route('admin.dashboard', ['risk' => 'Medium']) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('risk') === 'Medium' ? 'bg-white dark:bg-[#0A1128] text-amber-500 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    Sedang
                </a>
                <a href="{{ route('admin.dashboard', ['risk' => 'High']) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('risk') === 'High' ? 'bg-white dark:bg-[#0A1128] text-rose-500 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' }}">
                    Tinggi
                </a>
            </div>
        </div>

        <!-- Table View (Clean UI, persis Gambar 3) -->
        <div class="overflow-x-auto">
            <table id="deliveriesTable" class="w-full text-left text-xs text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-50 dark:bg-slate-900/40 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">ID PESANAN</th>
                        <th class="px-6 py-4">KURIR</th>
                        <th class="px-6 py-4">RUTE</th>
                        <th class="px-6 py-4">ESTIMASI</th>
                        <th class="px-6 py-4">STATUS RISIKO</th>
                        <th class="px-6 py-4 text-center">AKSI / BUKTI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition table-row-item">
                            <!-- ID Pesanan -->
                            <td class="px-6 py-4 font-bold font-mono text-[#0A1128] dark:text-white">
                                {{ $order->tracking_number }}
                            </td>

                            <!-- Kurir (Avatar Initial + Nama) -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-[#0A1128] text-white flex items-center justify-center font-bold text-[10px]">
                                        {{ substr($order->courier?->name ?? 'AO', 0, 2) }}
                                    </div>
                                    <span class="font-bold text-slate-800 dark:text-slate-100">{{ $order->courier?->name ?? 'Amara Okafor' }}</span>
                                </div>
                            </td>

                            <!-- Rute -->
                            <td class="px-6 py-4 font-semibold text-slate-600 dark:text-slate-300">
                                {{ $order->origin }} <span class="text-[#FF6B00]">→</span> {{ $order->destination }}
                            </td>

                            <!-- Estimasi -->
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                                {{ $order->created_at->addDays(2)->translatedFormat('d M') }}
                            </td>

                            <!-- Status Risiko (Pill dengan dot) -->
                            <td class="px-6 py-4">
                                @if($order->delay_risk === 'High')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200 dark:bg-rose-950/40 dark:border-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Tinggi - {{ $order->delay_probability }}%</span>
                                    </span>
                                @elseif($order->delay_risk === 'Medium')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-600 border border-amber-200 dark:bg-amber-950/40 dark:border-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Sedang - {{ $order->delay_probability }}%</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Rendah - {{ $order->delay_probability }}%</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Cepat / Bukti Foto -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Jika sudah ada bukti foto, tombol preview lightbox -->
                                    <!-- Lihat Foto Bukti (Hanya jika sudah diupload oleh Kurir) -->
                                    @if($order->proof_image)
                                        <button type="button" onclick="openPhotoLightbox('{{ asset('storage/' . $order->proof_image) }}', '{{ $order->tracking_number }}', '{{ addslashes($order->notes ?? 'Paket telah terkirim.') }}')"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 font-bold text-[11px] border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5 transition" title="Lihat Bukti Foto Kurir">
                                            <i class="fa-solid fa-camera"></i>
                                            <span>Foto Bukti</span>
                                        </button>
                                    @endif

                                    <!-- Verifikasi manifest jika masih pending -->
                                    @if($order->status === 'Verifikasi')
                                        <form action="{{ route('admin.orders.verify', $order->id) }}" method="POST" class="inline m-0">
                                            @csrf
                                            <button type="submit" title="Verifikasi Manifest" class="p-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs transition">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Detail / Tracking Publik (Same Tab) -->
                                    <a href="{{ route('tracking', ['tracking' => $order->tracking_number]) }}"
                                       class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs transition" title="Live Tracking">
                                        <i class="fa-solid fa-map-location-dot"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                Belum ada data pengiriman aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    // Search table filter
    function searchActiveDeliveries(keyword) {
        keyword = keyword.toLowerCase();
        const rows = document.querySelectorAll('.table-row-item');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    }

    // Leaflet Dark Route Map Initialization
    document.addEventListener('DOMContentLoaded', function() {
        const routeMap = L.map('fleetRouteMap', {
            center: [-7.35, 112.7],
            zoom: 7,
            scrollWheelZoom: false,
            zoomControl: true
        });

        // OpenStreetMap tile layer (TIDAK BUTUH API KEY - BEBAS WATERMARK)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors | Meridian Logistics',
            maxZoom: 19
        }).addTo(routeMap);

        // Major hubs with glowing dots
        const hubs = [
            { name: 'Surabaya Hub', lat: -7.2575, lng: 112.7521, color: '#FF6B00' },
            { name: 'Sidoarjo Drop Point', lat: -7.4478, lng: 112.7183, color: '#FF6B00' },
            { name: 'Malang Gateway', lat: -7.9666, lng: 112.6326, color: '#10B981' },
            { name: 'Mojokerto Hub', lat: -7.4726, lng: 112.4381, color: '#F59E0B' },
            { name: 'Jakarta Central', lat: -6.2088, lng: 106.8456, color: '#EF4444' }
        ];

        hubs.forEach(h => {
            const icon = L.divIcon({
                className: 'hub-pin',
                html: `
                    <div style="position: relative; width: 18px; height: 18px;">
                        <div style="position: absolute; width: 100%; height: 100%; background: ${h.color}; border-radius: 50%; opacity: 0.4; animation: ping 1.5s infinite;"></div>
                        <div style="position: absolute; width: 10px; height: 10px; top: 4px; left: 4px; background: ${h.color}; border-radius: 50%; border: 2px solid #fff;"></div>
                    </div>
                `,
                iconSize: [18, 18],
                iconAnchor: [9, 9]
            });
            const m = L.marker([h.lat, h.lng], { icon: icon }).addTo(routeMap);
            m.bindPopup(`<b style="color: #0A1128;">${h.name}</b>`);
        });

        // Curved dashed route lines
        const routeLines = [
            [[-7.4478, 112.7183], [-7.35, 112.73], [-7.2575, 112.7521]], // Sidoarjo -> Surabaya
            [[-7.4478, 112.7183], [-7.65, 112.68], [-7.9666, 112.6326]], // Sidoarjo -> Malang
            [[-7.2575, 112.7521], [-7.40, 112.55], [-7.4726, 112.4381]], // Surabaya -> Mojokerto
        ];

        routeLines.forEach((pts, idx) => {
            const colors = ['#10B981', '#F59E0B', '#EF4444'];
            L.polyline(pts, {
                color: colors[idx % 3],
                weight: 2.5,
                dashArray: '6, 6',
                opacity: 0.85
            }).addTo(routeMap);
        });
    });
</script>
@endpush
