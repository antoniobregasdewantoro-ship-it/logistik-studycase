<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Meridian') - Logistics Command Platform</title>

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
                            surface: '#F8FAFC',
                            surfaceDark: '#0B132B',
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

    <!-- Chart.js 4.4+ (Slide 6, 8, 10) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.2s, color 0.2s;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F8FAFC] dark:bg-[#070C1D] text-slate-800 dark:text-slate-100 antialiased min-h-screen flex flex-col lg:flex-row">

    <!-- ============================================================== -->
    <!-- SIDEBAR PERSIS FIGMA (Dark Navy #0A1128) - SCROLLABLE & RESPONSIVE -->
    <!-- ============================================================== -->
    <aside id="sidebar" class="w-full lg:w-64 bg-[#0A1128] text-white flex-shrink-0 flex flex-col justify-between z-30 transition-all duration-300 h-auto lg:h-screen lg:fixed lg:top-0 lg:bottom-0 overflow-y-auto">
        <div>
            <!-- LOGO HEADER (Rounded Orange Box + Meridian) -->
            <div class="px-6 py-6 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#FF6B00] flex items-center justify-center text-white shadow-md shadow-[#FF6B00]/40">
                        <i class="fa-solid fa-cube text-base"></i>
                    </div>
                    <span class="text-xl font-black tracking-tight text-white">Meridian</span>
                </a>
                <button id="closeSidebarBtn" class="lg:hidden text-slate-400 hover:text-white p-2">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- NAVIGATION ITEMS (Persis Foto Referensi) -->
            <nav class="px-3 py-2 space-y-1">
                <!-- 1. Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                    <i class="fa-regular fa-compass w-5 text-center text-sm"></i>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Kelola Pesanan / Manifes -->
                <a href="{{ route('admin.orders') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.orders*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                    <i class="fa-solid fa-boxes-packing w-5 text-center text-sm"></i>
                    <span>Kelola Pesanan</span>
                </a>

                <!-- 4. Laporan Pengiriman & Rekap Cetak (Satu Menu Terpadu) -->
                <a href="{{ route('admin.laporan') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.laporan*') ? 'bg-[#FF6B00] text-white shadow-md shadow-[#FF6B00]/30' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                    <i class="fa-solid fa-file-invoice w-5 text-center text-sm"></i>
                    <span>Laporan Pengiriman</span>
                </a>

                <!-- 5. Kelola Kurir & Akun Pengguna -->
                <a href="{{ route('admin.users') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.users*') ? 'bg-[#FF6B00] text-white shadow-md shadow-[#FF6B00]/30' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                    <i class="fa-solid fa-users-gear w-5 text-center text-sm"></i>
                    <span>Kelola Kurir & Akun</span>
                </a>

                <!-- 6. Riwayat Login 3 Role (Admin, Kurir, Pelanggan) -->
                <a href="{{ route('admin.login-history') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.login-history*') ? 'bg-[#FF6B00] text-white shadow-md shadow-[#FF6B00]/30' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                    <i class="fa-solid fa-clock-rotate-left w-5 text-center text-sm"></i>
                    <span>Riwayat Login (3 Role)</span>
                </a>

                <!-- 7. Edit Konten Landing Page (Khusus Admin) -->
                <a href="{{ route('admin.landing-content') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.landing-content*') ? 'bg-[#FF6B00] text-white' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                    <i class="fa-solid fa-pen-nib w-5 text-center text-sm"></i>
                    <span>Edit Konten Landing</span>
                </a>

                <!-- Halaman Depan Publik (Same Tab) -->
                <a href="{{ route('home') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all">
                    <i class="fa-solid fa-house w-5 text-center text-sm"></i>
                    <span>Halaman utama</span>
                </a>
            </nav>
        </div>

        <!-- PROFILE & SETTINGS (Persis Figma Gambar 1 & 3) -->
        <div class="p-4 border-t border-white/10 bg-[#070C1D] mt-auto">
            <!-- Dark / Light Mode Toggle Button -->
            <div class="flex items-center justify-between px-2 py-2 mb-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Mode Tema</span>
                <button type="button" onclick="toggleTheme()" id="themeToggleBtn"
                        class="p-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white text-xs flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-sun text-amber-400 dark:hidden"></i>
                    <i class="fa-solid fa-moon text-blue-300 hidden dark:inline"></i>
                    <span id="themeToggleText" class="text-[10px] font-bold">Terang</span>
                </button>
            </div>

            <!-- Profile Info: NK / Nadia Kessler -->
            <div class="flex items-center gap-3 p-2 rounded-xl bg-white/5 mb-3">
                <div class="w-9 h-9 rounded-full bg-slate-800 border border-white/20 flex items-center justify-center text-white text-xs font-black">
                    {{ substr(auth()->user()->name ?? 'Nadia Kessler', 0, 2) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Nadia Kessler' }}</p>
                    <p class="text-[10px] text-slate-400 truncate">Direktur Operasional</p>
                </div>
            </div>

            <!-- Logout Responsive Button -->
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600/80 hover:bg-rose-600 border border-rose-500/30 transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ============================================================== -->
    <!-- MAIN CONTENT AREA                                              -->
    <!-- ============================================================== -->
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

        <!-- Mobile Top Nav Bar -->
        <header class="lg:hidden bg-[#0A1128] text-white px-5 py-4 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#FF6B00] flex items-center justify-center text-white font-bold">
                    <i class="fa-solid fa-cube text-xs"></i>
                </div>
                <span class="font-extrabold text-base">Meridian</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleTheme()" class="p-2 text-slate-300">
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>
                <button id="openSidebarBtn" class="p-2 text-slate-300 hover:text-white">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>
        </header>

        <!-- FLASH NOTIFICATIONS -->
        <div class="px-6 sm:px-8 pt-4">
            @if(session('success'))
                <div class="flex items-center justify-between p-4 mb-3 text-xs font-bold text-emerald-800 dark:text-emerald-200 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-sm"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center justify-between p-4 mb-3 text-xs font-bold text-rose-800 dark:text-rose-200 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-sm"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500">&times;</button>
                </div>
            @endif
        </div>

        <!-- MAIN YIELD VIEW -->
        <main class="flex-1 px-6 sm:px-8 py-6">
            @yield('content')
        </main>
    </div>

    <!-- ============================================================== -->
    <!-- GLOBAL REUSABLE LIGHTBOX: LIHAT BUKTI FOTO PENGIRIMAN         -->
    <!-- ============================================================== -->
    <div id="globalPhotoLightbox" onclick="if(event.target === this) closePhotoLightbox()" class="fixed inset-0 bg-black/85 backdrop-blur-md z-[99999] flex items-center justify-center p-4 hidden">
        <div class="bg-white dark:bg-[#101935] rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 relative z-[100000]">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-[#0A1128] dark:text-white">Bukti Foto Serah Terima Resmi</h4>
                        <p id="lightboxResi" class="text-[10px] font-mono font-bold text-[#FF6B00]">ORD-XXXXX</p>
                    </div>
                </div>
                <button type="button" onclick="closePhotoLightbox()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white text-xl font-bold">&times;</button>
            </div>

            <div class="rounded-2xl overflow-hidden bg-slate-950 flex items-center justify-center max-h-96 border border-slate-200 dark:border-slate-800">
                <img id="lightboxImage" src="" alt="Bukti Foto Pengiriman" class="w-full h-full object-contain max-h-96">
            </div>

            <div class="mt-4 p-3 rounded-xl bg-slate-50 dark:bg-[#141E3D] border border-slate-100 dark:border-slate-800 text-xs">
                <span class="text-slate-400 font-bold block mb-0.5">Catatan Kurir / Penerima:</span>
                <p id="lightboxNotes" class="text-slate-700 dark:text-slate-300 font-medium">Telah diterima dengan baik di alamat tujuan.</p>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="button" onclick="closePhotoLightbox()"
                        class="px-5 py-2.5 rounded-xl bg-[#0A1128] dark:bg-[#FF6B00] text-white font-bold text-xs transition">
                    Tutup Tampilan
                </button>
            </div>
        </div>
    </div>

    <!-- Leaflet.js JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Sidebar Mobile
        const sidebar = document.getElementById('sidebar');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');

        if (openBtn) openBtn.addEventListener('click', () => sidebar.classList.remove('-translate-x-full'));
        if (closeBtn) closeBtn.addEventListener('click', () => sidebar.classList.add('-translate-x-full'));

        // Theme Toggle (Dark / Light)
        function applyInitialTheme() {
            const saved = localStorage.getItem('meridian_theme');
            const isDark = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
                const txt = document.getElementById('themeToggleText');
                if (txt) txt.textContent = 'Gelap';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                const txt = document.getElementById('themeToggleText');
                if (txt) txt.textContent = 'Terang';
            }
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                localStorage.setItem('meridian_theme', 'light');
                const txt = document.getElementById('themeToggleText');
                if (txt) txt.textContent = 'Terang';
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
                localStorage.setItem('meridian_theme', 'dark');
                const txt = document.getElementById('themeToggleText');
                if (txt) txt.textContent = 'Gelap';
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
