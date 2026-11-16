@extends('layouts.app')

@section('title', 'Dashboard Pelanggan - Meridian Logistics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- GREETING BANNER -->
    <div class="bg-gradient-to-r from-[#0A1128] via-[#141E3D] to-[#0A1128] rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6 border border-white/10">
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-[#FF6B00]/20 text-[#FF6B00] border border-[#FF6B00]/30 mb-2">
                PORTAL PELANGGAN
            </span>
            <h1 class="text-2xl sm:text-3xl font-black">Halo, {{ auth()->user()->name }}!</h1>
            <p class="text-slate-300 text-xs sm:text-sm mt-1">Pantau riwayat pengiriman paket dan cek status resi Anda secara langsung.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('customer.new-order') }}" class="px-5 py-2.5 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white font-extrabold text-xs shadow-lg shadow-[#FF6B00]/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Kirim Paket Baru (Order Pengiriman)</span>
            </a>
        </div>
    </div>

    <!-- QUICK STATS (3 BENTO CARDS) KHUSUS PELANGGAN -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-6 border border-slate-200/80 dark:border-white/10 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Paket Sedang Diantar</span>
                <p class="text-3xl font-black text-[#0A1128] dark:text-white mt-1">
                    {{ \App\Models\Order::where('customer_id', auth()->id())->where('status', 'Dalam Perjalanan')->count() }}
                </p>
                <span class="text-[11px] text-blue-500 font-bold">Dalam perjalanan aktif</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-6 border border-slate-200/80 dark:border-white/10 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Paket Selesai Terkirim</span>
                <p class="text-3xl font-black text-emerald-600 mt-1">
                    {{ \App\Models\Order::where('customer_id', auth()->id())->where('status', 'Terkirim')->count() }}
                </p>
                <span class="text-[11px] text-emerald-500 font-bold">Foto serah terima terverifikasi</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-6 border border-slate-200/80 dark:border-white/10 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Total Paket Saya</span>
                <p class="text-3xl font-black text-[#FF6B00] mt-1">
                    {{ \App\Models\Order::where('customer_id', auth()->id())->count() }} Paket
                </p>
                <span class="text-[11px] text-[#FF6B00] font-bold">Semua kiriman terdaftar</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-[#FF6B00] flex items-center justify-center text-xl">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
        </div>
    </div>

    <!-- SEKSI LACAK RESI KHUSUS DASHBOARD PELANGGAN (Requirement #13) -->
    <div id="lacakCustomerSection" class="bg-white dark:bg-[#0D1527] rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-white/10 shadow-lg space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/10 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-orange-500/10 text-[#FF6B00] flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-magnifying-glass-location"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-[#0A1128] dark:text-white">Pelacak Resi Mandiri Pelanggan</h2>
                    <p class="text-xs text-slate-400">Masukkan nomor resi pesanan Anda untuk memantau titik peta dan bukti foto serah terima.</p>
                </div>
            </div>
            <a href="{{ route('tracking') }}" class="text-xs font-bold text-[#FF6B00] hover:underline hidden sm:inline">
                Buka Halaman Penuh Pelacakan →
            </a>
        </div>

        <form action="{{ route('tracking') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-barcode absolute left-4 top-3.5 text-slate-400 text-sm"></i>
                <input type="text" name="tracking" placeholder="Masukkan nomor resi (cth: MRD-48209 / MRD-48207)" required
                       class="w-full pl-11 pr-4 py-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00]">
            </div>
            <button type="submit"
                    class="px-6 py-3 rounded-2xl bg-[#0A1128] dark:bg-[#FF6B00] text-white font-extrabold text-xs uppercase tracking-wider transition shadow-md flex items-center justify-center gap-2">
                <i class="fa-solid fa-satellite-dish"></i>
                <span>Lacak Sekarang</span>
            </button>
        </form>
    </div>

    <!-- RECENT SHIPMENTS TABLE -->
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl border border-slate-200/80 dark:border-white/10 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-[#0A1128] dark:text-white">Riwayat Pengiriman Terbaru</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Daftar paket aktif dan terkirim yang terhubung dengan akun Anda</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live Synced</span>
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-50 dark:bg-white/5 font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200 dark:border-white/10 text-[10px]">
                    <tr>
                        <th class="px-6 py-3.5">No. Resi</th>
                        <th class="px-6 py-3.5">Rute & Alamat</th>
                        <th class="px-6 py-3.5">Layanan & Berat</th>
                        <th class="px-6 py-3.5">Tarif & Pembayaran</th>
                        <th class="px-6 py-3.5">Kurir & Rating</th>
                        <th class="px-6 py-3.5">Status Pengiriman</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                    @forelse(\App\Models\Order::where('customer_id', auth()->id())->with(['courier', 'review'])->latest()->get() as $order)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition">
                            <td class="px-6 py-4 font-mono font-bold text-[#0A1128] dark:text-white">
                                {{ $order->tracking_number }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 dark:text-white">{{ $order->origin }} → {{ $order->destination }}</span>
                                <span class="block text-[11px] text-slate-400 truncate max-w-xs" title="{{ $order->full_address }}">
                                    {{ $order->full_address }}
                                </span>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold {{ $order->vehicle_type === 'pickup' ? 'bg-orange-500/10 text-[#FF6B00] border border-orange-500/20' : 'bg-blue-500/10 text-blue-500 border border-blue-500/20' }}">
                                        <i class="{{ $order->vehicle_icon }}"></i>
                                        <span>{{ $order->scope_label }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 dark:text-white">{{ $order->service_type }} ({{ $order->weight }} Kg)</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $order->package_detail ?? 'Paket Ekspedisi' }}</span>
                                <div class="flex items-center gap-1.5 mt-1 text-[10px] text-amber-600 dark:text-amber-400 font-bold" title="Estimasi Paket Sampai">
                                    <i class="fa-solid fa-clock-rotate-left text-[9px]"></i>
                                    <span>{{ $order->estimated_arrival }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-[#FF6B00] block">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold {{ str_contains($order->payment_method, 'QRIS') ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }}">
                                    <i class="{{ str_contains($order->payment_method, 'QRIS') ? 'fa-solid fa-qrcode' : 'fa-solid fa-money-bill-wave' }} text-[9px]"></i>
                                    <span>{{ $order->payment_method }}</span>
                                </span>
                            </td>
                            <!-- Kurir Ditugaskan & Rating Pelanggan -->
                            <td class="px-6 py-4">
                                @if($order->courier)
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $order->courier->avatar_url }}" alt="{{ $order->courier->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-white/10 shrink-0 shadow-sm">
                                        <div>
                                            <span class="font-extrabold text-slate-800 dark:text-white text-[11px] block">{{ $order->courier->name }}</span>
                                            @if($order->review)
                                                <div class="flex items-center gap-1 text-amber-400 text-[10px] mt-0.5" title="Ulasan: {{ $order->review->comment ?? 'Puas' }}">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fa-{{ $i <= $order->review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                                    @endfor
                                                    <span class="text-slate-400 font-bold ml-0.5 text-[9px]">({{ $order->review->rating }}/5)</span>
                                                </div>
                                            @else
                                                <button type="button"
                                                        onclick="openRatingModal('{{ $order->id }}', '{{ $order->tracking_number }}', '{{ addslashes($order->courier->name) }}', '{{ $order->courier->avatar_url }}')"
                                                        class="inline-flex items-center gap-1 text-[10px] text-amber-500 hover:text-amber-600 font-bold mt-0.5 hover:underline">
                                                    <i class="fa-solid fa-star text-[9px]"></i>
                                                    <span>Beri Rating</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Menunggu Penugasan Kurir</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($order->status === 'Terkirim')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">Terkirim</span>
                                @elseif($order->status === 'Dalam Perjalanan')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300">Dalam Perjalanan</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">Verifikasi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('tracking', ['tracking' => $order->tracking_number]) }}"
                                       class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-white/10 hover:bg-[#FF6B00] hover:text-white text-slate-700 dark:text-slate-200 font-bold text-[11px] transition inline-flex items-center gap-1.5 shadow-sm">
                                        <i class="fa-solid fa-map-location-dot text-[#FF6B00]"></i>
                                        <span>Lacak Peta</span>
                                    </a>

                                    @if($order->proof_image)
                                        <button type="button"
                                                onclick="openPhotoLightbox('{{ $order->proof_image_url }}', '{{ $order->tracking_number }}', '{{ addslashes($order->notes ?? 'Paket telah terkirim.') }}')"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold text-[11px] transition flex items-center gap-1"
                                                title="Lihat Foto Bukti Serah Terima">
                                            <i class="fa-solid fa-camera"></i>
                                            <span>Foto</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/5 mx-auto flex items-center justify-center mb-2">
                                    <i class="fa-solid fa-box-open text-slate-400 text-lg"></i>
                                </div>
                                <p class="font-bold text-xs text-slate-600 dark:text-slate-300">Belum ada data pengiriman atas akun Anda.</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Silakan gunakan tombol "+ Kirim Paket Baru" di atas untuk membuat order penjemputan paket pertama Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ============================================================== -->
<!-- MODAL: RATING KURIR OLEH PELANGGAN                             -->
<!-- ============================================================== -->
<div id="modalRatingKurir" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-[#101935] rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 dark:border-slate-800 text-center space-y-5 animate-in fade-in duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <span class="text-xs font-black uppercase tracking-wider text-[#FF6B00]">PENILAIAN LAYANAN KURIR</span>
            <button type="button" onclick="closeRatingModal()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white text-xl font-bold">&times;</button>
        </div>

        <!-- Kurir Info & Foto -->
        <div class="flex flex-col items-center">
            <img id="ratingCourierAvatar" src="" alt="Foto Kurir" class="w-20 h-20 rounded-full object-cover border-2 border-[#FF6B00] shadow-lg mb-2.5">
            <h3 id="ratingCourierName" class="text-base font-black text-[#0A1128] dark:text-white">Nama Kurir</h3>
            <p id="ratingResiBadge" class="text-xs font-mono font-bold text-slate-400">MRD-XXXXX</p>
        </div>

        <form id="ratingCourierForm" action="" method="POST" class="space-y-4 text-left">
            @csrf

            <!-- Bintang 1-5 -->
            <div class="text-center">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-2">Berapa bintang kepuasan pelayanan kurir?</label>
                <div class="flex items-center justify-center gap-2.5" id="starRatingGroup">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" onclick="setRatingStar({{ $i }})" class="star-btn text-3xl text-amber-400 hover:scale-125 transition-transform focus:outline-none" data-value="{{ $i }}">
                            <i class="fa-solid fa-star"></i>
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="selectedRatingValue" value="5">
                <p id="ratingLabelText" class="text-xs font-bold text-amber-500 mt-2">Sangat Memuaskan (5/5)</p>
            </div>

            <!-- Komentar / Ulasan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ulasan untuk Kurir (Opsional)</label>
                <textarea name="comment" rows="3" placeholder="Contoh: Kurir sangat ramah, paket sampai aman tanpa cacat, tepat waktu..."
                          class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs focus:outline-none focus:border-[#FF6B00]"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeRatingModal()"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300">Batal</button>
                <button type="submit"
                        class="flex-1 px-5 py-2.5 rounded-xl bg-[#FF6B00] hover:bg-[#E55F00] text-white text-xs font-black shadow-md shadow-[#FF6B00]/30 transition">
                    <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Penilaian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRatingModal(orderId, resi, courierName, courierAvatar) {
        document.getElementById('ratingCourierForm').action = '/customer/orders/' + orderId + '/rate';
        document.getElementById('ratingCourierName').textContent = courierName;
        document.getElementById('ratingResiBadge').textContent = 'Resi: ' + resi;
        document.getElementById('ratingCourierAvatar').src = courierAvatar;
        setRatingStar(5);
        document.getElementById('modalRatingKurir').classList.remove('hidden');
    }

    function closeRatingModal() {
        document.getElementById('modalRatingKurir').classList.add('hidden');
    }

    function setRatingStar(value) {
        document.getElementById('selectedRatingValue').value = value;
        const labels = {
            1: 'Kurang Puas (1/5)',
            2: 'Cukup (2/5)',
            3: 'Baik (3/5)',
            4: 'Memuaskan (4/5)',
            5: 'Sangat Memuaskan (5/5)'
        };
        document.getElementById('ratingLabelText').textContent = labels[value] || (value + '/5');

        document.querySelectorAll('#starRatingGroup .star-btn').forEach(btn => {
            const val = parseInt(btn.getAttribute('data-value'));
            const icon = btn.querySelector('i');
            if (val <= value) {
                icon.className = 'fa-solid fa-star';
            } else {
                icon.className = 'fa-regular fa-star text-slate-300 dark:text-slate-600';
            }
        });
    }
</script>
@endsection
