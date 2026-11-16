<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Meridian Logistics - Solusi Pengiriman & Pelacakan Cerdas')</title>

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
                        navy: {
                            DEFAULT: '#0A1128',
                            900: '#070C1D',
                            800: '#0A1128',
                            700: '#141E3D',
                        },
                        brand: {
                            navy: '#0A1128',
                            dark: '#070C1D',
                            card: '#0D1527',
                            orange: '#FF6B00',
                            orangeHover: '#E55F00',
                        },
                        safety: {
                            DEFAULT: '#FF6B00',
                            hover: '#E55F00',
                            light: '#FFF0E6',
                        },
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
            scroll-behavior: smooth;
            transition: background-color 0.2s, color 0.2s;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F8FAFC] dark:bg-[#070C1D] text-slate-800 dark:text-slate-100 antialiased overflow-x-hidden min-h-screen flex flex-col justify-between">

    <!-- NAVBAR UTAMA -->
    <header class="w-full bg-white/90 dark:bg-[#0A1128]/90 backdrop-blur-md border-b border-slate-200 dark:border-white/10 sticky top-0 z-50 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="bg-[#0A1128] dark:bg-white/10 text-[#FF6B00] w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg shadow-[#0A1128]/20 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-cube text-xl"></i>
                    </div>
                    <div>
                        <span class="text-2xl font-black tracking-tight text-[#0A1128] dark:text-white">MERIDIAN</span>
                     
                    </div>
                </a>

                <!-- Nav Menu Tengah -->
                <nav class="hidden md:flex space-x-8 items-center text-sm font-semibold">
                    <a href="{{ route('home') }}" class="text-slate-600 dark:text-slate-300 hover:text-[#FF6B00] transition">Beranda</a>
                    <a href="{{ route('home') }}#kalkulator" class="text-slate-600 dark:text-slate-300 hover:text-[#FF6B00] transition">Kalkulator Ongkir</a>
                    <a href="{{ route('home') }}#showcase" class="text-slate-600 dark:text-slate-300 hover:text-[#FF6B00] transition">Armada Modern</a>
                    <a href="{{ route('home') }}#cabang" class="text-slate-600 dark:text-slate-300 hover:text-[#FF6B00] transition">Jaringan Hub</a>
                </nav>

                <!-- Menu Aksi Kanan -->
                <div class="flex items-center space-x-3">
                    <!-- Dark / Light Theme Toggle -->
                    <button type="button" onclick="toggleTheme()" class="p-2.5 rounded-2xl bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 hover:text-[#FF6B00] text-sm transition" title="Ganti Tema">
                        <i class="fa-solid fa-sun text-amber-500 dark:hidden"></i>
                        <i class="fa-solid fa-moon text-sky-300 hidden dark:inline"></i>
                    </button>

                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="bg-[#0A1128] dark:bg-white text-white dark:text-[#0A1128] px-4 sm:px-5 py-2.5 rounded-2xl font-bold text-xs sm:text-sm hover:opacity-90 transition shadow-md flex items-center gap-2">
                                <i class="fa-solid fa-gauge-high text-[#FF6B00]"></i>
                                <span>Dashboard Admin</span>
                            </a>
                        @elseif(in_array(auth()->user()->role, ['courier', 'kurir']))
                            <a href="{{ route('courier.tasks') }}"
                               class="bg-[#FF6B00] text-white px-4 sm:px-5 py-2.5 rounded-2xl font-bold text-xs sm:text-sm hover:bg-[#E55F00] transition shadow-md flex items-center gap-2">
                                <i class="fa-solid fa-truck-fast"></i>
                                <span>Portal Kurir</span>
                            </a>
                        @else
                            <a href="{{ route('customer.dashboard') }}"
                               class="bg-[#0A1128] dark:bg-white text-white dark:text-[#0A1128] px-4 sm:px-5 py-2.5 rounded-2xl font-bold text-xs sm:text-sm hover:opacity-90 transition shadow-md">
                                Akun Saya
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline m-0">
                            @csrf
                            <button type="submit" title="Keluar" class="p-2.5 rounded-2xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition border border-slate-200 dark:border-white/10">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-[#0A1128] dark:text-white font-bold text-xs sm:text-sm px-3 sm:px-4 py-2.5 hover:text-[#FF6B00] transition flex items-center gap-2">
                            <i class="fa-regular fa-circle-user"></i>
                            <span class="hidden sm:inline">Masuk</span> Portal
                        </a>
                        <a href="{{ route('login') }}"
                           class="bg-[#0A1128] dark:bg-[#FF6B00] text-white px-4 sm:px-5 py-2.5 rounded-2xl font-bold text-xs sm:text-sm hover:opacity-90 transition shadow-md hover:shadow-lg flex items-center gap-2">
                            <span>Mulai</span>
                            <i class="fa-solid fa-arrow-right text-xs text-[#FF6B00] dark:text-white"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- KONTEN DINAMIS -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- GLOBAL PHOTO LIGHTBOX MODAL (Customer Delivery Proof Preview) -->
    <div id="globalPhotoLightbox" onclick="if(event.target === this) closePhotoLightbox()" class="fixed inset-0 z-[99999] bg-black/85 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-200 dark:border-white/10 animate-in fade-in zoom-in duration-200 relative z-[100000]">
            <div class="p-4 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-camera"></i>
                    </span>
                    <div>
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Bukti Foto Serah Terima (Terkirim)</h4>
                        <p id="lightboxResi" class="text-[11px] text-[#FF6B00] font-mono font-bold"></p>
                    </div>
                </div>
                <button type="button" onclick="closePhotoLightbox()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-white/10 text-slate-500 hover:text-rose-500 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="p-4 bg-slate-950 flex items-center justify-center min-h-[300px]">
                <img id="lightboxImage" src="" alt="Bukti Foto Pengiriman" class="max-h-[70vh] w-auto max-w-full rounded-2xl object-contain shadow-md">
            </div>
            <div class="p-4 border-t border-slate-100 dark:border-white/10 bg-slate-50 dark:bg-white/5 flex items-center justify-between">
                <p id="lightboxNotes" class="text-xs text-slate-500 dark:text-slate-400 italic truncate max-w-[280px]"></p>
                <button type="button" onclick="closePhotoLightbox()" class="px-4 py-2 rounded-xl bg-[#0A1128] dark:bg-white/10 text-white text-xs font-bold hover:bg-slate-800 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- FOOTER RESMI MERIDIAN -->
    <footer class="bg-[#0A1128] text-white pt-16 pb-12 border-t border-white/10 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-white/10">
                <!-- Kolom 1: Brand Info -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center gap-3">
                        <div class="bg-[#FF6B00] text-white p-2.5 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-cube text-lg"></i>
                        </div>
                        <span class="text-2xl font-black text-white">MERIDIAN</span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Platform manajemen logistik cerdas generasi baru dengan pelacakan live OpenStreetMap, kalkulasi tarif real-time, dan otomasi manifest terpadu di seluruh Indonesia.
                    </p>
                </div>

                <!-- Kolom 2: Layanan -->
                <div>
                    <h4 class="text-sm font-bold tracking-wider uppercase text-white mb-4">Layanan Pengiriman</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="#tarif" class="hover:text-[#FF6B00] transition">Meridian Reguler (Door to Door)</a></li>
                        <li><a href="#tarif" class="hover:text-[#FF6B00] transition">Meridian Express (Next Day)</a></li>
                        <li><a href="#tarif" class="hover:text-[#FF6B00] transition">Meridian Kargo (Muatan Berat)</a></li>
                        <li><a href="#tarif" class="hover:text-[#FF6B00] transition">Meridian Same Day (Antar Kota)</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Navigasi Cepat -->
                <div>
                    <h4 class="text-sm font-bold tracking-wider uppercase text-white mb-4">Navigasi Sistem</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ route('home') }}" class="hover:text-[#FF6B00] transition">Beranda Ekspedisi</a></li>
                        <li><a href="{{ route('home') }}#kalkulator" class="hover:text-[#FF6B00] transition">Kalkulator Tarif Dinamis</a></li>
                        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-[#FF6B00] transition">Command Center Admin</a></li>
                        <li><a href="{{ route('courier.tasks') }}" class="hover:text-[#FF6B00] transition">Portal Kurir Lapangan</a></li>
                    </ul>
                </div>

                <!-- Kolom 4: Hubungi Kami -->
                <div>
                    <h4 class="text-sm font-bold tracking-wider uppercase text-white mb-4">Pusat Bantuan</h4>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot text-[#FF6B00] mt-1"></i>
                            <span>Hub Utama: Surabaya & Jakarta Logistics Park</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-phone text-[#FF6B00]"></i>
                            <span>(031) 8899-7700 / 0812-3456-7890</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope text-[#FF6B00]"></i>
                            <span>support@meridianlogistics.id</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>© {{ date('Y') }} Meridian Logistics System. All rights reserved.</p>
                <div class="flex items-center space-x-6">
                    <span class="text-emerald-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Semua Node Jaringan Aktif
                    </span>
                    <span>Kebijakan Privasi</span>
                    <span>Syarat & Ketentuan</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Leaflet.js JS -->
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
