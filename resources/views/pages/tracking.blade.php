@extends('layouts.app')

@section('title', 'Lacak Paket & Cek Tarif - Meridian Logistics')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 space-y-10">

    <!-- PAGE HEADER -->
    <div class="text-center max-w-2xl mx-auto space-y-2">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-orange-50 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-800/40 text-[#FF6B00] text-xs font-black uppercase tracking-widest shadow-sm">
            <i class="fa-solid fa-satellite-dish"></i>
            <span>Live Satellite Tracking System</span>
        </span>
        <h1 class="text-3xl sm:text-5xl font-black text-[#0A1128] dark:text-white tracking-tight">
            Lacak Resi Pengiriman
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
            Ketahui lokasi terkini armada kurir secara real-time, status manifest, dan foto bukti serah terima penerima.
        </p>
    </div>

    <!-- SEARCH BAR BOX -->
    <div class="bg-white dark:bg-[#0D1527] p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-100 dark:border-white/10 max-w-3xl mx-auto">
        <form action="{{ route('tracking') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-barcode absolute left-4 top-4 text-slate-400 text-lg"></i>
                <input type="text" name="tracking" value="{{ request('tracking', $trackedOrder?->tracking_number) }}"
                       required placeholder="Masukkan nomor resi resmi (cth: MRD-48209 / MRD-48207)"
                       class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-sm font-bold text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00] focus:ring-2 focus:ring-[#FF6B00]/20">
            </div>
            <button type="submit"
                    class="px-8 py-3.5 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-[#FF6B00]/30 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Cari Paket</span>
            </button>
        </form>

        <!-- Contoh Resi Cepat / Resi Milik Pelanggan -->
        <div class="mt-3.5 flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
            @if(auth()->check() && auth()->user()->role === 'customer')
                <span class="font-bold text-[#FF6B00]"><i class="fa-solid fa-box-archive mr-1"></i> Resi Paket Anda:</span>
                @forelse(\App\Models\Order::where('customer_id', auth()->id())->latest()->take(3)->get() as $myOrder)
                    <a href="{{ route('tracking', ['tracking' => $myOrder->tracking_number]) }}" class="px-2.5 py-1 rounded-lg bg-orange-50 dark:bg-orange-950/40 hover:bg-[#FF6B00] hover:text-white text-[#FF6B00] font-mono font-bold transition border border-orange-200 dark:border-orange-500/20">
                        {{ $myOrder->tracking_number }} ({{ $myOrder->status }})
                    </a>
                @empty
                    <span class="text-slate-400 italic">Belum ada paket kiriman yang dibuat.</span>
                @endforelse
            @else
                <span class="font-bold">Contoh nomor resi:</span>
                <a href="{{ route('tracking', ['tracking' => 'MRD-48209']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/10 hover:bg-orange-100 hover:text-[#FF6B00] text-slate-700 dark:text-slate-300 font-mono font-bold transition">MRD-48209 (Terkirim)</a>
                <a href="{{ route('tracking', ['tracking' => 'MRD-48207']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/10 hover:bg-orange-100 hover:text-[#FF6B00] text-slate-700 dark:text-slate-300 font-mono font-bold transition">MRD-48207 (Diantar)</a>
                <a href="{{ route('tracking', ['tracking' => 'MRD-48208']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/10 hover:bg-orange-100 hover:text-[#FF6B00] text-slate-700 dark:text-slate-300 font-mono font-bold transition">MRD-48208</a>
            @endif
        </div>
    </div>

    <!-- HASIL PELACAKAN RESI -->
    @if(request()->filled('tracking'))
        @if($trackedOrder)
            <div class="bg-white dark:bg-[#0D1527] p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-100 dark:border-white/10 space-y-8 animate-in fade-in duration-300">

                <!-- Header Info Resi -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200/80 dark:border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-[#0A1128] dark:bg-white/10 text-[#FF6B00] flex items-center justify-center text-2xl font-bold shadow-md">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Nomor Resi Resmi Terdaftar</span>
                            <h2 class="text-2xl font-black font-mono text-[#0A1128] dark:text-white tracking-tight">{{ $trackedOrder->tracking_number }}</h2>
                        </div>
                    </div>
                    <div>
                        @if($trackedOrder->status === 'Terkirim')
                            <span class="px-4 py-2 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> Paket Berhasil Terkirim
                            </span>
                        @elseif($trackedOrder->status === 'Dalam Perjalanan')
                            <span class="px-4 py-2 rounded-full text-xs font-black bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 border border-sky-300 dark:border-sky-700 flex items-center gap-2">
                                <i class="fa-solid fa-truck-fast text-sky-600"></i> Sedang Dalam Pengantaran
                            </span>
                        @else
                            <span class="px-4 py-2 rounded-full text-xs font-black bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700 flex items-center gap-2">
                                <i class="fa-solid fa-clock text-amber-600"></i> Menunggu Verifikasi Manifest
                            </span>
                        @endif
                    </div>
                </div>

                <!-- PROGRESS STEPPER BAR -->
                <div class="py-4">
                    <div class="relative flex justify-between items-center max-w-xl mx-auto">
                        <div class="absolute top-1/2 left-0 right-0 h-1 bg-slate-200 dark:bg-white/10 -translate-y-1/2 z-0"></div>
                        <div class="absolute top-1/2 left-0 h-1 bg-[#FF6B00] -translate-y-1/2 z-0 transition-all duration-500"
                             style="width: {{ $trackedOrder->status === 'Terkirim' ? '100%' : ($trackedOrder->status === 'Dalam Perjalanan' ? '66%' : '33%') }};"></div>

                        <div class="flex flex-col items-center gap-2 z-10">
                            <div class="w-10 h-10 rounded-full bg-[#FF6B00] text-white flex items-center justify-center font-bold text-xs shadow-md">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </div>
                            <span class="text-xs font-bold text-[#0A1128] dark:text-white">Manifest Masuk</span>
                        </div>

                        <div class="flex flex-col items-center gap-2 z-10">
                            <div class="w-10 h-10 rounded-full {{ in_array($trackedOrder->status, ['Dalam Perjalanan', 'Terkirim']) ? 'bg-[#FF6B00] text-white shadow-md' : 'bg-slate-200 dark:bg-white/10 text-slate-500' }} flex items-center justify-center font-bold text-xs">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <span class="text-xs font-bold {{ in_array($trackedOrder->status, ['Dalam Perjalanan', 'Terkirim']) ? 'text-[#0A1128] dark:text-white' : 'text-slate-400' }}">Pengantaran Rute</span>
                        </div>

                        <div class="flex flex-col items-center gap-2 z-10">
                            <div class="w-10 h-10 rounded-full {{ $trackedOrder->status === 'Terkirim' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-200 dark:bg-white/10 text-slate-500' }} flex items-center justify-center font-bold text-xs">
                                <i class="fa-solid fa-check-double"></i>
                            </div>
                            <span class="text-xs font-bold {{ $trackedOrder->status === 'Terkirim' ? 'text-emerald-600 dark:text-emerald-400 font-extrabold' : 'text-slate-400' }}">Terkirim Sukses</span>
                        </div>
                    </div>
                </div>

                <!-- BUKTI FOTO SERAH TERIMA PAKET (JIKA TERKIRIM) -->
                @if($trackedOrder->status === 'Terkirim' || $trackedOrder->proof_image)
                    <div class="p-6 rounded-3xl bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 shadow-sm space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-emerald-200/60 dark:border-emerald-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold shadow-md shadow-emerald-600/30">
                                    <i class="fa-solid fa-camera"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-emerald-950 dark:text-emerald-200">
                                        Bukti Foto Serah Terima (Terkirim)
                                    </h3>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-400">
                                        Foto serah terima fisik paket yang diambil oleh kurir resmi di lokasi penerima.
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-200/70 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Verifikasi GPS Otomatis</span>
                            </span>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            @php
                                $proofUrl = $trackedOrder->proof_image_url ?? 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=80';
                            @endphp
                            <div onclick="openPhotoLightbox('{{ $proofUrl }}', '{{ $trackedOrder->tracking_number }}', '{{ addslashes($trackedOrder->notes ?? 'Paket telah terkirim dan diterima langsung.') }}')"
                                 class="relative w-full sm:w-56 h-40 rounded-2xl overflow-hidden cursor-pointer group shadow-lg border-2 border-emerald-300 dark:border-emerald-700/60 flex-shrink-0">
                                <img src="{{ $proofUrl }}" alt="Bukti Foto Serah Terima" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-2 backdrop-blur-[2px]">
                                    <i class="fa-solid fa-magnifying-glass-plus text-base"></i>
                                    <span>Klik Perbesar</span>
                                </div>
                                <span class="absolute bottom-2 left-2 px-2.5 py-1 rounded-lg bg-black/70 text-white text-[10px] font-bold">
                                    Bukti Serah Terima
                                </span>
                            </div>

                            <div class="space-y-3 flex-1 text-xs">
                                <div class="p-4 rounded-2xl bg-white dark:bg-[#0D1527] border border-emerald-200/60 dark:border-emerald-800/40">
                                    <p class="font-bold text-slate-700 dark:text-slate-300">Catatan dari Petugas Kurir:</p>
                                    <p class="text-slate-600 dark:text-slate-400 mt-1 italic">
                                        "{{ $trackedOrder->notes ?? 'Paket telah diserahterimakan langsung kepada penerima dalam kondisi tersegel rapi dan aman.' }}"
                                    </p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-emerald-900 dark:text-emerald-300">
                                    <p><i class="fa-solid fa-user-check mr-1.5 text-emerald-600"></i> Penerima: <strong>{{ $trackedOrder->recipient_name }}</strong></p>
                                    <p><i class="fa-solid fa-calendar-check mr-1.5 text-emerald-600"></i> Waktu: <strong>{{ $trackedOrder->updated_at->translatedFormat('d F Y, H:i') }} WIB</strong></p>
                                    <p class="sm:col-span-2"><i class="fa-solid fa-location-dot mr-1.5 text-emerald-600"></i> Alamat: <strong>{{ $trackedOrder->full_address }}</strong></p>
                                </div>
                                <button type="button"
                                        onclick="openPhotoLightbox('{{ $proofUrl }}', '{{ $trackedOrder->tracking_number }}', '{{ addslashes($trackedOrder->notes ?? 'Paket telah terkirim.') }}')"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition">
                                    <i class="fa-solid fa-expand"></i>
                                    <span>Buka Foto Resolusi Penuh</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- PETA LIVE TRACKING LEAFLET (BEBAS WATERMARK / TANPA API KEY REQUIRED) -->
                <div class="rounded-3xl border border-slate-200 dark:border-white/10 overflow-hidden shadow-lg">
                    <div class="bg-slate-50 dark:bg-white/5 px-5 py-3.5 border-b border-slate-200 dark:border-white/10 flex items-center justify-between text-xs">
                        <span class="font-bold text-[#0A1128] dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-[#FF6B00]"></i>
                            <span>Posisi Titik Armada di Peta OpenStreetMap Terbuka</span>
                        </span>
                        <span class="text-slate-500 dark:text-slate-400 font-mono text-[11px]">{{ $trackedOrder->latitude }}, {{ $trackedOrder->longitude }}</span>
                    </div>
                    <div id="dedicatedTrackingMap" class="w-full h-80 z-10"></div>
                </div>

                <!-- DETAIL SPESIFIKASI PENGIRIMAN & DOKUMEN -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 space-y-1">
                        <span class="text-slate-400 font-bold block">PENGIRIM</span>
                        <p class="font-extrabold text-[#0A1128] dark:text-white text-sm">{{ $trackedOrder->sender_name }}</p>
                        <p class="text-slate-500">Asal: {{ $trackedOrder->origin }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 space-y-1">
                        <span class="text-slate-400 font-bold block">PENERIMA & ALAMAT LENGKAP</span>
                        <p class="font-extrabold text-[#0A1128] dark:text-white text-sm">{{ $trackedOrder->recipient_name }}</p>
                        <p class="text-slate-500 line-clamp-2" title="{{ $trackedOrder->full_address }}">{{ $trackedOrder->full_address }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 space-y-1">
                        <span class="text-slate-400 font-bold block">LAYANAN & ONGKIR</span>
                        <p class="font-extrabold text-[#FF6B00] text-sm">Rp {{ number_format($trackedOrder->price, 0, ',', '.') }}</p>
                        <p class="text-slate-500">{{ $trackedOrder->service_type }} • {{ $trackedOrder->weight }} kg</p>
                        <div class="mt-1.5 pt-1.5 border-t border-slate-200/60 dark:border-white/10 flex items-center gap-1.5 text-[11px] text-amber-600 dark:text-amber-400 font-bold" title="Estimasi Paket Sampai">
                            <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                            <span>{{ $trackedOrder->estimated_arrival }}</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 flex items-center justify-between">
                        <div>
                            <span class="text-slate-400 font-bold block mb-1">LABEL DIGITAL</span>
                            <p class="font-bold text-[#0A1128] dark:text-white">Barcode Resi</p>
                            @if($trackedOrder->pdf_report_path)
                                <a href="{{ $trackedOrder->pdf_report_url }}" class="text-[10px] text-rose-500 font-bold hover:underline block mt-1" download>
                                    <i class="fa-solid fa-file-pdf"></i> Unduh PDF
                                </a>
                            @endif
                        </div>
                        <img src="{{ $trackedOrder->barcode_url }}" alt="Barcode Resi" class="w-24 h-12 object-contain rounded-xl border border-slate-200 dark:border-white/20 p-1 bg-white">
                    </div>
                </div>

            </div>
        @else
            <div class="bg-white dark:bg-[#0D1527] p-8 rounded-3xl shadow-xl border border-rose-200 dark:border-rose-900/50 text-center max-w-xl mx-auto space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-500 mx-auto flex items-center justify-center text-2xl font-bold">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-lg font-black text-[#0A1128] dark:text-white">
                    {{ $trackingError ?? 'Nomor Resi "' . request('tracking') . '" Tidak Ditemukan' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    @if(auth()->check() && auth()->user()->role === 'customer')
                        Demi privasi dan keamanan data ekspedisi, Anda hanya dapat melacak paket kiriman milik Anda sendiri. Silakan pantau seluruh riwayat paket Anda melalui <b>Dashboard Pelanggan</b>.
                    @else
                        Silakan periksa kembali nomor resi yang Anda masukkan (contoh: MRD-48209) atau hubungi pusat bantuan kami.
                    @endif
                </p>
                @if(auth()->check() && auth()->user()->role === 'customer')
                    <div class="pt-2">
                        <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0A1128] dark:bg-[#FF6B00] text-white text-xs font-bold transition">
                            <i class="fa-solid fa-arrow-left"></i>
                            <span>Kembali ke Dashboard Paket Saya</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif
    @endif

</div>
@endsection

@push('scripts')
<script>
    @if(isset($trackedOrder) && $trackedOrder->latitude && $trackedOrder->longitude)
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $trackedOrder->latitude }};
        const lng = {{ $trackedOrder->longitude }};
        const resi = "{{ $trackedOrder->tracking_number }}";
        const checkpoint = "{{ $trackedOrder->checkpoint }}";
        const status = "{{ $trackedOrder->status }}";
        const isLuarKota = "{{ $trackedOrder->vehicle_type }}" === "pickup";
        const vehicleIcon = isLuarKota ? "fa-solid fa-truck-pickup" : "fa-solid fa-bicycle";
        const vehicleLabel = isLuarKota ? "Mobil Pickup (Luar Kota)" : "Sepeda (Di Kecamatan)";

        const map = L.map('dedicatedTrackingMap', {
            center: [lat, lng],
            zoom: 13,
            scrollWheelZoom: false
        });

        // OpenStreetMap standard tile (TIDAK BUTUH API KEY - BEBAS WATERMARK)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors | Meridian Logistics',
            maxZoom: 19
        }).addTo(map);

        const customPin = L.divIcon({
            className: 'tracking-marker',
            html: `
                <div style="background-color: #FF6B00; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; border: 3px solid white; box-shadow: 0 4px 14px rgba(255,107,0,0.6);">
                    <i class="${vehicleIcon}" style="font-size: 18px;"></i>
                </div>
            `,
            iconSize: [42, 42],
            iconAnchor: [21, 21],
            popupAnchor: [0, -21]
        });

        const marker = L.marker([lat, lng], { icon: customPin }).addTo(map);
        marker.bindPopup(`
            <div style="font-size: 12px; font-family: inherit;">
                <b style="color: #0A1128; font-family: monospace;">${resi}</b><br>
                <span style="color: #FF6B00; font-weight: bold;">${vehicleLabel}</span><br>
                <span style="color: #10b981; font-weight: bold;">Status: ${status}</span><br>
                <span style="color: #64748b;">${checkpoint}</span>
            </div>
        `).openPopup();
    });
    @endif
</script>
@endpush
