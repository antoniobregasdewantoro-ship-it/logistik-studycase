<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meridian - AI Logistics</title>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        scroll-behavior: smooth;
    }
    </style>
</head>

<body class="bg-[#FAFAFA] text-slate-800 antialiased overflow-x-hidden">

    <!-- Navbar -->
    <nav class="absolute w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-24">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3">
                    <div class="bg-[#0A1128] text-[#FF6B00] p-2.5 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-[#0A1128]">Meridian</span>
                </a>

                <!-- Menu Tengah (Sesuai Gambar) -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="/" class="text-slate-500 hover:text-slate-900 font-medium transition">Home</a>
                    <a href="#tentang" class="text-slate-500 hover:text-slate-900 font-medium transition">Tentang
                        Kami</a>
                    <a href="#layanan" class="text-slate-500 hover:text-slate-900 font-medium transition">Layanan</a>
                    <a href="#jaringan" class="text-slate-500 hover:text-slate-900 font-medium transition">Jaringan</a>
                    <a href="#bantuan" class="text-slate-500 hover:text-slate-900 font-medium transition">Bantuan</a>
                </div>

                <!-- Menu Kanan -->
                <div class="flex items-center space-x-6">
                    <a href="/login" class="text-[#0A1128] font-bold hover:text-slate-600 transition">Masuk</a>
                    <a href="/register"
                        class="bg-[#0A1128] text-white px-6 py-2.5 rounded-xl font-bold hover:bg-slate-800 transition shadow-md hover:shadow-lg">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Konten Dinamis -->
    <main>
        @yield('content')
    </main>

</body>

</html>