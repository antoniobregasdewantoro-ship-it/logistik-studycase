<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Meridian Kurir') - Portal Operasional Kurir</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#0A1128',
                            dark: '#070C1D',
                            card: '#0D1527',
                            orange: '#FF6B00',
                            orangeHover: '#E55F00',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.2s, color 0.2s;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F8FAFC] dark:bg-[#070C1D] text-slate-800 dark:text-slate-100 antialiased min-h-screen flex flex-col">

    <!-- TOP NAVBAR WEB KURIR (Persis Gambar 2) -->
    <header class="bg-[#0A1128] text-white px-6 sm:px-8 py-3 sticky top-0 z-[1000] shadow-md border-b border-white/10">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('courier.tasks') }}" class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-[#FF6B00] flex items-center justify-center text-white font-bold shadow-md shadow-[#FF6B00]/40">
                    <i class="fa-solid fa-cube text-xs"></i>
                </div>
                <span class="text-lg font-black tracking-tight text-white">Meridian <span class="font-normal text-slate-300">Kurir</span></span>
            </a>

            <!-- Right Controls (Tampilan admin, Sedang bertugas, Avatar AO) -->
            <div class="flex items-center gap-4">
                <!-- Theme toggle -->
                <button type="button" onclick="toggleTheme()" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 text-xs transition">
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>

                <!-- Tampilan admin link -->
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-slate-300 hover:text-white transition hidden sm:inline">
                    Tampilan admin
                </a>

                <!-- Sedang bertugas badge -->
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sedang bertugas</span>
                </span>

                <!-- Avatar AO: Amara Okafor -->
                <div class="flex items-center gap-2.5 pl-2 border-l border-white/10">
                    <div class="w-8 h-8 rounded-full bg-[#FF6B00] text-white flex items-center justify-center font-bold text-xs">
                        {{ substr(auth()->user()->name ?? 'Amara Okafor', 0, 2) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-xs font-bold text-white leading-tight">{{ auth()->user()->name ?? 'Amara Okafor' }}</p>
                        <p class="text-[10px] text-slate-400">Van 14 • Zona Utara</p>
                    </div>
                </div>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" title="Keluar" class="p-2 rounded-xl bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 text-xs transition">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- FLASH NOTIFICATIONS -->
    <div class="max-w-7xl mx-auto w-full px-6 pt-4">
        @if(session('success'))
            <div class="p-3.5 mb-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 mb-2 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-2xl text-xs font-bold shadow-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- MAIN DASHBOARD CONTENT -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 py-3 sm:py-4">
        @yield('content')
    </main>

    <!-- GLOBAL PHOTO LIGHTBOX MODAL (Customer & Courier & Admin Preview) -->
    <div id="globalPhotoLightbox" onclick="if(event.target === this) closePhotoLightbox()" class="fixed inset-0 z-[99999] bg-black/85 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200 dark:border-white/10 animate-in fade-in zoom-in duration-200 relative z-[100000]">
            <!-- Header Modal -->
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-base font-bold">
                        <i class="fa-solid fa-camera"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Foto Bukti Serah Terima</h4>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                <i class="fa-solid fa-circle-check text-[9px] mr-0.5"></i> Terverifikasi
                            </span>
                        </div>
                        <p id="lightboxResi" class="text-xs text-[#FF6B00] font-mono font-bold mt-0.5"></p>
                    </div>
                </div>
                <button type="button" onclick="closePhotoLightbox()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-white/10 text-slate-500 hover:text-rose-500 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Image Viewport -->
            <div class="p-4 bg-slate-950 flex items-center justify-center min-h-[300px] max-h-[60vh] overflow-hidden">
                <img id="lightboxImage" src="" alt="Bukti Foto Pengiriman" class="max-h-[58vh] w-auto max-w-full rounded-2xl object-contain shadow-2xl transition duration-300">
            </div>

            <!-- Footer Details & Actions -->
            <div class="p-4 sm:p-5 border-t border-slate-100 dark:border-white/10 bg-slate-50 dark:bg-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-quote-left text-slate-300 dark:text-slate-600 text-xs mt-1 shrink-0"></i>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 block tracking-wider">Catatan Pengantaran:</span>
                        <p id="lightboxNotes" class="text-xs text-slate-600 dark:text-slate-300 italic max-w-xs"></p>
                    </div>
                </div>
                <button type="button" onclick="closePhotoLightbox()" class="px-5 py-2.5 rounded-2xl bg-[#0A1128] dark:bg-white text-white dark:text-[#0A1128] text-xs font-bold hover:bg-slate-800 transition shadow">
                    Tutup Tampilan
                </button>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        function applyInitialTheme() {
            const saved = localStorage.getItem('meridian_theme');
            const isDark = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        }
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                localStorage.setItem('meridian_theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
                localStorage.setItem('meridian_theme', 'dark');
            }
        }
        applyInitialTheme();

        // Photo Lightbox Trigger
        function openPhotoLightbox(url, resi, notes) {
            document.getElementById('lightboxImage').src = url;
            document.getElementById('lightboxResi').textContent = resi || 'ORD-48213';
            document.getElementById('lightboxNotes').textContent = notes || 'Paket telah terkirim dan diverifikasi di lokasi tujuan.';
            document.getElementById('globalPhotoLightbox').classList.remove('hidden');
        }
        function closePhotoLightbox() {
            document.getElementById('globalPhotoLightbox').classList.add('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
