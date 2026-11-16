@extends('layouts.admin')

@section('title', 'Edit Konten Landing Page & Animasi Carousel')

@section('content')
<div class="space-y-6">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[11px] font-black tracking-widest uppercase text-[#FF6B00]">PENGATURAN KONTEN · MERIDIAN CMS</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-[11px] font-bold text-emerald-500">Live Sync</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A1128] dark:text-white tracking-tight">
                Edit Konten Landing Page
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola teks banner utama, animasi visual berganti otomatis, dan informasi armada di halaman depan publik.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}"
               class="px-4 py-2.5 rounded-2xl bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-white/10 text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-[#FF6B00] transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Lihat Landing Page</span>
            </a>
            <button type="button" onclick="submitLandingContentForm()"
                    class="px-5 py-2.5 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-[#FF6B00]/25">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    <!-- NOTIFICATION ALERTS -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm font-bold flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-xs sm:text-sm font-bold flex items-start gap-3 shadow-sm">
            <i class="fa-solid fa-circle-exclamation text-rose-500 text-base mt-0.5"></i>
            <div>
                <p class="font-extrabold mb-1">Gagal menyimpan konten:</p>
                <ul class="list-disc list-inside space-y-0.5 font-normal text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- MAIN FORM GRID -->
    <form id="landingContentForm" action="{{ route('admin.landing-content.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. HERO SECTION SETTINGS -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-6 border border-slate-100 dark:border-white/5 shadow-sm space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-white/5">
                <div class="w-10 h-10 rounded-2xl bg-orange-500/10 text-[#FF6B00] flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-[#0A1128] dark:text-white">Bagian Utama (Hero Banner)</h2>
                    <p class="text-xs text-slate-400">Judul besar, subjudul, dan lencana promosi di bagian paling atas.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2 space-y-1.5">
                    <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Teks Badge Promosi (Kecil di Atas Judul)
                    </label>
                    <input type="text" name="hero_badge" value="{{ old('hero_badge', $hero->badge_text ?? 'SISTEM LOGISTIK & PELACAKAN REAL-TIME TERINTEGRASI') }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-xs sm:text-sm font-bold text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00] focus:ring-2 focus:ring-[#FF6B00]/20 transition"
                           placeholder="Contoh: SISTEM LOGISTIK TERPERCAYA SEJAK 2024">
                </div>

                <div class="md:col-span-2 space-y-1.5">
                    <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Judul Utama (Headline Besar)
                    </label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $hero->title ?? 'Kirim Paket Cepat, Aman, dan Tepat Waktu.') }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-base sm:text-lg font-black text-slate-900 dark:text-white focus:outline-none focus:border-[#FF6B00] focus:ring-2 focus:ring-[#FF6B00]/20 transition"
                           placeholder="Kirim Paket Cepat, Aman, dan Tepat Waktu.">
                </div>

                <div class="md:col-span-2 space-y-1.5">
                    <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Deskripsi Penjelas (Subtitle)
                    </label>
                    <textarea name="hero_subtitle" rows="3" required
                              class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00] focus:ring-2 focus:ring-[#FF6B00]/20 transition"
                              placeholder="Layanan ekspedisi terpercaya ke seluruh pelosok Nusantara...">{{ old('hero_subtitle', $hero->subtitle ?? 'Layanan ekspedisi terpercaya ke seluruh pelosok Nusantara. Lacak keberadaan paket Anda secara live melalui peta atau cek kalkulasi estimasi tarif otomatis.') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. SHOWCASE ARMADA PENGIRIMAN -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-6 border border-slate-100 dark:border-white/5 shadow-sm space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-white/5">
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-images"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-[#0A1128] dark:text-white">Banner Informasi Armada Pengiriman</h2>
                    <p class="text-xs text-slate-400">Atur kartu informasi armada pengiriman yang tampil pada halaman depan.</p>
                </div>
            </div>

            <!-- Carousel Section Header Titles -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-2">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Judul Bagian Showcase</label>
                    <input type="text" name="carousel_title" value="{{ old('carousel_title', $carousel->title ?? 'Armada & Jaringan Pengiriman Modern') }}"
                           class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-xs sm:text-sm font-bold text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00]">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Subjudul Showcase</label>
                    <input type="text" name="carousel_subtitle" value="{{ old('carousel_subtitle', $carousel->subtitle ?? 'Teknologi otomasi logistik terdepan menghubungkan pulau-pulau di Indonesia.') }}"
                           class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-xs sm:text-sm text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00]">
                </div>
            </div>

            @php
                $slides = $carousel->extra_data ?? [];
                $defaults = [
                    [
                        'tag' => 'ARMADA CEPAT',
                        'title' => 'Armada Pickup & Kargo Antar Kota',
                        'desc' => 'Layanan Same-Day dan Next-Day dengan armada van dan sepeda motor berinsulasi tinggi.',
                        'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'tag' => 'LIVE TRACKING',
                        'title' => 'Pemantauan Satelit & OpenStreetMap',
                        'desc' => 'GPS real-time terhubung langsung ke ponsel kurir dan command center drop point.',
                        'image' => 'https://images.unsplash.com/photo-1519003722824-194d4455a60c?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'tag' => 'VERIFIKASI DIGITAL',
                        'title' => 'Bukti Foto Serah Terima & Barcode Resi',
                        'desc' => 'Penerima dapat langsung mengecek foto bukti pengiriman asli secara transparan.',
                        'image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1200&q=80',
                    ]
                ];
            @endphp

            <!-- 3 SLIDE CARDS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @for($i = 0; $i < 3; $i++)
                    @php
                        $slide = $slides[$i] ?? $defaults[$i];
                    @endphp
                    <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-3xl border border-slate-200 dark:border-white/10 space-y-4 relative group">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-white/10">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#0A1128] text-white">
                                SLIDE #{{ $i + 1 }}
                            </span>
                            <span class="text-[11px] font-bold text-slate-400">
                                <i class="fa-solid fa-play text-xs text-[#FF6B00]"></i> Frame {{ $i + 1 }}
                            </span>
                        </div>

                        <!-- Media Preview (Gambar / GIF / Video) -->
                        <div class="w-full h-36 rounded-2xl overflow-hidden bg-slate-200 dark:bg-slate-800 relative flex items-center justify-center">
                            @php
                                $imgSrc = $slide['image'];
                                if (str_contains($imgSrc, 'storage/')) {
                                    $imgSrc = asset(substr($imgSrc, strpos($imgSrc, 'storage/')));
                                }
                                $isVideo = preg_match('/\.(mp4|webm|ogg|mov)$/i', $imgSrc);
                            @endphp
                            <img id="preview_img_{{ $i }}" src="{{ $imgSrc }}" alt="Preview Slide {{ $i+1 }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 {{ $isVideo ? 'hidden' : '' }}"
                                 onerror="this.src='https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80'">
                            <video id="preview_video_{{ $i }}" src="{{ $isVideo ? $imgSrc : '' }}"
                                   autoplay loop muted playsinline
                                   class="w-full h-full object-cover {{ $isVideo ? '' : 'hidden' }}">
                            </video>
                            <span id="preview_badge_{{ $i }}" class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-bold">
                                {{ $isVideo ? 'Video Preview' : (str_ends_with(strtolower($imgSrc), '.gif') ? 'GIF Preview' : 'Live Preview') }}
                            </span>
                        </div>

                        <!-- Tag -->
                        <div class="space-y-1">
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Badge Tag</label>
                            <input type="text" name="slide_tag_{{ $i }}" value="{{ old("slide_tag_{$i}", $slide['tag'] ?? 'LOGISTIK') }}"
                                   class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-white/10 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00]">
                        </div>

                        <!-- Title -->
                        <div class="space-y-1">
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Judul Slide</label>
                            <input type="text" name="slide_title_{{ $i }}" value="{{ old("slide_title_{$i}", $slide['title'] ?? '') }}" required
                                   class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-white/10 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:border-[#FF6B00]">
                        </div>

                        <!-- Description -->
                        <div class="space-y-1">
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 dark:text-slate-400 tracking-wider">Deskripsi Singkat</label>
                            <textarea name="slide_desc_{{ $i }}" rows="2"
                                      class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-white/10 text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-[#FF6B00]">{{ old("slide_desc_{$i}", $slide['desc'] ?? '') }}</textarea>
                        </div>

                        <!-- Media File Upload: Gambar, GIF, Video (File .txt ditolak) -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 dark:text-slate-400 tracking-wider flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-photo-film text-[#FF6B00]"></i>
                                    <span>Upload Gambar, GIF, atau Video</span>
                                </span>
                                <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">Maks 20MB</span>
                            </label>
                            <input type="file" name="slide_file_{{ $i }}" id="slide_file_input_{{ $i }}"
                                   accept="image/jpeg,image/png,image/jpg,image/webp,image/gif,video/mp4,video/webm,video/ogg,video/quicktime,.gif,.mp4,.webm"
                                   onchange="previewSlideFile(this, {{ $i }})"
                                   class="w-full text-xs text-slate-600 dark:text-slate-300 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-[#FF6B00] file:text-white hover:file:bg-[#e55f00] cursor-pointer border border-dashed border-slate-300 dark:border-white/10 rounded-xl p-1 bg-white dark:bg-[#0D1527] transition">
                            <div id="file_badge_{{ $i }}" class="hidden text-[11px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1.5 rounded-xl border border-emerald-200 dark:border-emerald-500/20">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                <span id="file_name_{{ $i }}" class="truncate"></span>
                            </div>
                            <p class="text-[10px] text-slate-400">Hanya format Gambar (JPG, PNG, WEBP), GIF, atau Video (MP4, WEBM). File lain seperti .txt tidak diizinkan.</p>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- SAVE BUTTON BAR -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="reset" class="px-5 py-3 rounded-2xl bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 transition">
                Reset Ulang
            </button>
            <button type="submit"
                    class="px-8 py-3 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white text-xs font-black uppercase tracking-wider transition shadow-lg shadow-[#FF6B00]/30 flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Seluruh Konten</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewSlideFile(input, index) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const ext = file.name.split('.').pop().toLowerCase();

        // 1. Validasi Tipe File: Hanya Gambar, GIF, dan Video. File lain seperti .txt DITOLAK
        const allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'webm', 'ogg', 'mov'];
        const isImage = ['jpg', 'jpeg', 'png', 'webp'].includes(ext);
        const isGif = ext === 'gif';
        const isVideo = ['mp4', 'webm', 'ogg', 'mov'].includes(ext);

        if (!allowedExts.includes(ext)) {
            alert('Format file "' + ext + '" tidak diizinkan!\n\nAnda hanya dapat mengunggah file Gambar (JPG, PNG, WEBP), GIF animasi, atau Video (MP4, WEBM). File seperti .txt atau dokumen tidak dapat digunakan.');
            input.value = '';
            return;
        }

        // 2. Validasi Ukuran File (Maksimal 20MB)
        const maxBytes = 20 * 1024 * 1024;
        if (file.size > maxBytes) {
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            alert('Ukuran file melebihi batas maksimal 20MB (ukuran file Anda: ' + sizeInMB + ' MB). Silakan pilih file yang berukuran maksimal 20MB!');
            input.value = '';
            return;
        }

        const previewImg = document.getElementById('preview_img_' + index);
        const previewVideo = document.getElementById('preview_video_' + index);
        const previewBadge = document.getElementById('preview_badge_' + index);
        const fileBadge = document.getElementById('file_badge_' + index);
        const fileNameSpan = document.getElementById('file_name_' + index);

        if (fileBadge && fileNameSpan) {
            fileBadge.classList.remove('hidden');
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            fileNameSpan.textContent = file.name + ' (' + sizeInMB + ' MB)';
        }

        if (isVideo) {
            // Preview Video
            if (previewImg) previewImg.classList.add('hidden');
            if (previewVideo) {
                previewVideo.classList.remove('hidden');
                previewVideo.src = URL.createObjectURL(file);
                previewVideo.play().catch(() => {});
            }
            if (previewBadge) previewBadge.textContent = 'Video Preview (' + ext.toUpperCase() + ')';
        } else {
            // Preview Gambar atau GIF
            if (previewVideo) {
                previewVideo.pause();
                previewVideo.classList.add('hidden');
            }
            if (previewImg) {
                previewImg.classList.remove('hidden');
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
            if (previewBadge) previewBadge.textContent = isGif ? 'GIF Preview' : 'Live Preview (' + ext.toUpperCase() + ')';
        }
    }
}

function submitLandingContentForm() {
    const form = document.getElementById('landingContentForm');
    if (form) {
        if (form.reportValidity()) {
            form.submit();
        }
    }
}
</script>
@endpush
@endsection
