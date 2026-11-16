@extends('layouts.app')

@section('title', 'Masuk Portal - Meridian Logistics')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6">
    <div class="bg-white dark:bg-[#0D1527] p-8 sm:p-10 rounded-3xl shadow-2xl w-full max-w-md border border-slate-100 dark:border-white/10 transition-colors">

        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-[#0A1128] dark:bg-[#FF6B00] text-[#FF6B00] dark:text-white flex items-center justify-center text-xl mx-auto mb-3 shadow-lg">
                <i class="fa-solid fa-cube text-xl"></i>
            </div>
            <h2 class="text-2xl font-black text-[#0A1128] dark:text-white">Portal Akses Meridian</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masuk untuk mengelola manifest, armada, atau pelacakan</p>
        </div>

        <!-- Tombol Masuk dengan Google -->
        <div class="mb-5">
            <a href="{{ route('auth.google') }}"
               class="w-full py-3 px-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20 text-slate-700 dark:text-white font-bold text-xs flex items-center justify-center gap-3 transition shadow-sm hover:shadow group">
                <svg class="w-4 h-4 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.9c2.27-2.09 3.64-5.17 3.64-9.15z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.9-3.05c-1.08.72-2.45 1.16-4.03 1.16-3.1 0-5.74-2.1-6.68-4.94H1.26v3.15C3.3 21.36 7.36 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.32 14.26c-.24-.73-.38-1.5-.38-2.26s.14-1.53.38-2.26V6.59H1.26C.46 8.2 0 10.04 0 12s.46 3.8 1.26 5.41l4.06-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.3 2.64 1.26 6.59l4.06 3.15c.94-2.84 3.58-4.99 6.68-4.99z"/>
                </svg>
                <span>Masuk dengan Google</span>
            </a>

            <!-- Tombol Pilih Akun Google Lain -->
            <button type="button" onclick="openGoogleChooserModal()"
                    class="mt-2.5 w-full text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 hover:text-[#FF6B00] transition flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-users-viewfinder text-[11px] text-[#FF6B00]"></i>
                <span>Pilih / Ganti Akun Google</span>
            </button>

            <div class="relative flex py-4 items-center">
                <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
                <span class="flex-shrink mx-3 text-[10px] font-bold text-slate-400 uppercase">atau email</span>
                <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
            </div>
        </div>

        <!-- Peringatan jika Akun Terkunci (Lockout 60 Detik) -->
        @if(session('login_locked'))
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs mb-6">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <i class="fa-solid fa-lock text-rose-600 dark:text-rose-400 text-sm"></i>
                    <span>AKUN DIKUNCI SEMENTARA</span>
                </div>
                <p>{{ $errors->first() }}</p>
                <div class="mt-2.5 p-2 bg-rose-100 dark:bg-rose-900/50 rounded-xl text-center font-bold text-rose-900 dark:text-rose-200">
                    Waktu tunggu: <span id="countdownTimer" class="font-mono text-sm text-rose-600 dark:text-rose-400">{{ session('lockout_seconds', 60) }}</span> detik
                </div>
            </div>
        @elseif($errors->any())
            <!-- Peringatan Percobaan Gagal (1, 2, 3) -->
            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs font-semibold mb-6 flex items-start gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400 text-sm mt-0.5"></i>
                <div class="flex-1">
                    {{ $errors->first() }}
                </div>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Email</label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-4 top-3.5 text-slate-400 text-xs"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@meridian.com"
                           class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-400 text-xs"></i>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                </div>
            </div>

            <button type="submit" id="loginBtn"
                    class="w-full py-3.5 rounded-2xl bg-[#0A1128] dark:bg-[#FF6B00] text-white font-extrabold text-xs hover:bg-slate-800 dark:hover:bg-[#E55F00] transition shadow-md mt-2 flex items-center justify-center gap-2">
                <span>Masuk Sekarang</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </button>
        </form>

        <!-- Link ke Pendaftaran -->
        <div class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
            <span>Belum memiliki akun?</span>
            <a href="{{ route('register') }}" class="font-extrabold text-[#FF6B00] hover:underline ml-1">
                Daftar Akun Baru
            </a>
        </div>

        <!-- Akun Uji Coba -->
        <div class="mt-6 p-4 bg-slate-50 dark:bg-white/5 rounded-2xl text-xs text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-white/5">
            <p class="font-bold mb-2 text-[#0A1128] dark:text-white flex items-center gap-1.5">
                <i class="fa-solid fa-key text-[#FF6B00]"></i>
                <span>Akun Testing (Password: password)</span>
            </p>
            <ul class="space-y-1 font-mono text-[11px]">
                <li><span class="text-emerald-500 font-bold">Admin:</span> admin@meridian.com</li>
                <li><span class="text-blue-500 font-bold">Kurir:</span> kurir@meridian.com</li>
                <li><span class="text-amber-500 font-bold">Customer:</span> customer@meridian.com</li>
            </ul>
        </div>

    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: GOOGLE ACCOUNT CHOOSER (SINGLE SIGN-ON POPUP)           -->
<!-- ============================================================== -->
<div id="modalGoogleChooser" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl max-w-md w-full overflow-hidden shadow-2xl border border-slate-200 dark:border-white/10 animate-in fade-in zoom-in duration-200">
        <!-- Header Modal -->
        <div class="p-6 pb-4 border-b border-slate-100 dark:border-white/10 text-center relative">
            <button type="button" onclick="closeGoogleChooserModal()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 dark:bg-white/10 text-slate-500 hover:text-rose-500 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
            <div class="w-10 h-10 mx-auto mb-2 flex items-center justify-center">
                <svg class="w-8 h-8" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.9c2.27-2.09 3.64-5.17 3.64-9.15z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.9-3.05c-1.08.72-2.45 1.16-4.03 1.16-3.1 0-5.74-2.1-6.68-4.94H1.26v3.15C3.3 21.36 7.36 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.32 14.26c-.24-.73-.38-1.5-.38-2.26s.14-1.53.38-2.26V6.59H1.26C.46 8.2 0 10.04 0 12s.46 3.8 1.26 5.41l4.06-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.3 2.64 1.26 6.59l4.06 3.15c.94-2.84 3.58-4.99 6.68-4.99z"/>
                </svg>
            </div>
            <h3 class="text-base font-black text-[#0A1128] dark:text-white">Pilih Akun Google</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">untuk melanjutkan ke Meridian Logistics System</p>
        </div>

        <!-- List Akun -->
        <div class="p-6 space-y-3 max-h-[60vh] overflow-y-auto">
            @php
                $gUser = \App\Models\User::where('email', 'google.user@meridian.com')->first();
                $custUser = \App\Models\User::where('role', 'customer')->where('email', '!=', 'google.user@meridian.com')->first();
            @endphp

            <!-- 1. Akun Google Terdaftar / Kurir -->
            <a href="{{ route('auth.google', ['email' => $gUser ? $gUser->email : 'google.user@meridian.com']) }}"
               class="flex items-center gap-3.5 p-3.5 rounded-2xl border border-slate-200 dark:border-white/10 hover:border-[#FF6B00] dark:hover:border-[#FF6B00] hover:bg-slate-50 dark:hover:bg-white/5 transition group">
                <img src="{{ $gUser ? $gUser->avatar_url : 'https://ui-avatars.com/api/?name=Google+User&background=0A1128&color=FF6B00' }}"
                     class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0" alt="Avatar">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs font-black text-slate-800 dark:text-white truncate group-hover:text-[#FF6B00] transition">
                            {{ $gUser ? $gUser->name : 'Pengguna Google Terverifikasi' }}
                        </h4>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ ($gUser && $gUser->role === 'courier') ? 'bg-blue-500/10 text-blue-500' : 'bg-emerald-500/10 text-emerald-500' }}">
                            {{ ($gUser && $gUser->role === 'courier') ? 'Kurir' : (($gUser && $gUser->role === 'admin') ? 'Admin' : 'Customer') }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono truncate">
                        {{ $gUser ? $gUser->email : 'google.user@meridian.com' }}
                    </p>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400 group-hover:text-[#FF6B00] group-hover:translate-x-0.5 transition-all"></i>
            </a>

            @if($custUser)
            <!-- 2. Akun Pelanggan -->
            <a href="{{ route('auth.google', ['email' => $custUser->email]) }}"
               class="flex items-center gap-3.5 p-3.5 rounded-2xl border border-slate-200 dark:border-white/10 hover:border-[#FF6B00] dark:hover:border-[#FF6B00] hover:bg-slate-50 dark:hover:bg-white/5 transition group">
                <img src="{{ $custUser->avatar_url }}"
                     class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0" alt="Avatar">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs font-black text-slate-800 dark:text-white truncate group-hover:text-[#FF6B00] transition">
                            {{ $custUser->name }}
                        </h4>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/10 text-amber-500">
                            Pelanggan
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono truncate">
                        {{ $custUser->email }}
                    </p>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400 group-hover:text-[#FF6B00] group-hover:translate-x-0.5 transition-all"></i>
            </a>
            @endif

            <!-- 3. Form Masuk dengan Akun Google Kustom -->
            <div class="pt-3 border-t border-slate-100 dark:border-white/10">
                <form action="{{ route('auth.google') }}" method="GET" class="space-y-2.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">
                        Gunakan Akun Google Lain / Baru:
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="email" name="email" required placeholder="nama.anda@gmail.com"
                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                    <div class="relative">
                        <i class="fa-regular fa-user absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="text" name="name" placeholder="Nama Lengkap (Opsional)"
                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-[#0A1128] dark:bg-[#FF6B00] text-white font-extrabold text-xs hover:bg-[#FF6B00] transition flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.9c2.27-2.09 3.64-5.17 3.64-9.15z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.9-3.05c-1.08.72-2.45 1.16-4.03 1.16-3.1 0-5.74-2.1-6.68-4.94H1.26v3.15C3.3 21.36 7.36 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.32 14.26c-.24-.73-.38-1.5-.38-2.26s.14-1.53.38-2.26V6.59H1.26C.46 8.2 0 10.04 0 12s.46 3.8 1.26 5.41l4.06-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.3 2.64 1.26 6.59l4.06 3.15c.94-2.84 3.58-4.99 6.68-4.99z"/>
                        </svg>
                        <span>Lanjutkan dengan Akun Ini</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="p-4 bg-slate-50 dark:bg-white/5 border-t border-slate-100 dark:border-white/10 text-center">
            <button type="button" onclick="closeGoogleChooserModal()" class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function openGoogleChooserModal() {
        const modal = document.getElementById('modalGoogleChooser');
        if (modal) modal.classList.remove('hidden');
    }

    function closeGoogleChooserModal() {
        const modal = document.getElementById('modalGoogleChooser');
        if (modal) modal.classList.add('hidden');
    }
</script>

@if(session('login_locked'))
<script>
    let secondsLeft = {{ session('lockout_seconds', 60) }};
    const timerEl = document.getElementById('countdownTimer');
    const btnEl = document.getElementById('loginBtn');

    if (btnEl) {
        btnEl.disabled = true;
        btnEl.classList.add('opacity-50', 'cursor-not-allowed');
    }

    const interval = setInterval(() => {
        secondsLeft--;
        if (timerEl) timerEl.textContent = secondsLeft;
        if (secondsLeft <= 0) {
            clearInterval(interval);
            if (btnEl) {
                btnEl.disabled = false;
                btnEl.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            window.location.reload();
        }
    }, 1000);
</script>
@endif
@endsection
