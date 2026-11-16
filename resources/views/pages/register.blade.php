@extends('layouts.app')

@section('title', 'Pendaftaran Akun Baru - Meridian Logistics')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6">
    <div class="bg-white dark:bg-[#0D1527] p-8 sm:p-10 rounded-3xl shadow-2xl w-full max-w-lg border border-slate-100 dark:border-white/10 transition-colors">

        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-[#FF6B00] text-white flex items-center justify-center text-xl mx-auto mb-3 shadow-lg shadow-[#FF6B00]/30">
                <i class="fa-solid fa-user-plus text-lg"></i>
            </div>
            <h2 class="text-2xl font-black text-[#0A1128] dark:text-white">Daftar Akun Meridian</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Bergabung dengan ekosistem logistik pintar terpadu</p>
        </div>

        <!-- Tombol Daftar dengan Google -->
        <div class="mb-5">
            <a href="{{ route('auth.google') }}"
               class="w-full py-3 px-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20 text-slate-700 dark:text-white font-bold text-xs flex items-center justify-center gap-3 transition shadow-sm hover:shadow group">
                <svg class="w-4 h-4 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.9c2.27-2.09 3.64-5.17 3.64-9.15z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.9-3.05c-1.08.72-2.45 1.16-4.03 1.16-3.1 0-5.74-2.1-6.68-4.94H1.26v3.15C3.3 21.36 7.36 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.32 14.26c-.24-.73-.38-1.5-.38-2.26s.14-1.53.38-2.26V6.59H1.26C.46 8.2 0 10.04 0 12s.46 3.8 1.26 5.41l4.06-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.3 2.64 1.26 6.59l4.06 3.15c.94-2.84 3.58-4.99 6.68-4.99z"/>
                </svg>
                <span>Daftar Cepat dengan Google</span>
            </a>

            <!-- Tombol Pilih Akun Google Lain -->
            <button type="button" onclick="openGoogleChooserModal()"
                    class="mt-2.5 w-full text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 hover:text-[#FF6B00] transition flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-users-viewfinder text-[11px] text-[#FF6B00]"></i>
                <span>Pilih / Ganti Akun Google</span>
            </button>

            <div class="relative flex py-4 items-center">
                <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
                <span class="flex-shrink mx-3 text-[10px] font-bold text-slate-400 uppercase">atau lengkapi formulir</span>
                <div class="flex-grow border-t border-slate-200 dark:border-white/10"></div>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs mb-5">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Pilihan Role Akun -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Daftar Sebagai *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="customer" {{ old('role', 'customer') === 'customer' ? 'checked' : '' }} class="peer hidden">
                        <div class="p-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 peer-checked:border-[#FF6B00] peer-checked:bg-[#FF6B00]/10 text-center transition">
                            <i class="fa-solid fa-user text-slate-500 peer-checked:text-[#FF6B00] mb-1 block"></i>
                            <span class="text-xs font-extrabold text-slate-800 dark:text-white block">Pelanggan</span>
                            <span class="text-[10px] text-slate-400">Kirim & Lacak</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="courier" {{ old('role') === 'courier' ? 'checked' : '' }} class="peer hidden">
                        <div class="p-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 peer-checked:border-[#FF6B00] peer-checked:bg-[#FF6B00]/10 text-center transition">
                            <i class="fa-solid fa-motorcycle text-slate-500 peer-checked:text-[#FF6B00] mb-1 block"></i>
                            <span class="text-xs font-extrabold text-slate-800 dark:text-white block">Petugas Kurir</span>
                            <span class="text-[10px] text-slate-400">Antar & Manifes</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08123456789"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kata Sandi *</label>
                    <input type="password" name="password" required placeholder="Min. 6 karakter"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ulangi Kata Sandi *</label>
                    <input type="password" name="password_confirmation" required placeholder="Konfirmasi sandi"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3.5 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white font-black text-xs uppercase tracking-wider transition shadow-lg shadow-[#FF6B00]/30 mt-3 flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Selesaikan Pendaftaran</span>
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
            <span>Sudah memiliki akun?</span>
            <a href="{{ route('login') }}" class="font-extrabold text-[#FF6B00] hover:underline ml-1">
                Masuk ke Portal
            </a>
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
            <h3 class="text-base font-black text-[#0A1128] dark:text-white">Daftar dengan Akun Google</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pilih atau masukkan email Google Anda untuk verifikasi instan</p>
        </div>

        <!-- List Akun -->
        <div class="p-6 space-y-3 max-h-[60vh] overflow-y-auto">
            @php
                $gUser = \App\Models\User::where('email', 'google.user@meridian.com')->first();
            @endphp

            @if($gUser)
            <!-- 1. Akun Google Default -->
            <a href="{{ route('auth.google', ['email' => $gUser->email]) }}"
               class="flex items-center gap-3.5 p-3.5 rounded-2xl border border-slate-200 dark:border-white/10 hover:border-[#FF6B00] dark:hover:border-[#FF6B00] hover:bg-slate-50 dark:hover:bg-white/5 transition group">
                <img src="{{ $gUser->avatar_url }}"
                     class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0" alt="Avatar">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs font-black text-slate-800 dark:text-white truncate group-hover:text-[#FF6B00] transition">
                            {{ $gUser->name }}
                        </h4>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/10 text-emerald-500">
                            {{ $gUser->role }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono truncate">
                        {{ $gUser->email }}
                    </p>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-400 group-hover:text-[#FF6B00] transition-all"></i>
            </a>
            @endif

            <!-- 2. Form Masuk dengan Akun Google Kustom -->
            <div class="pt-2">
                <form action="{{ route('auth.google') }}" method="GET" class="space-y-2.5">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">
                        Masukkan Email Google Anda:
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="email" name="email" required placeholder="nama.anda@gmail.com"
                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                    <div class="relative">
                        <i class="fa-regular fa-user absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="text" name="name" placeholder="Nama Lengkap Anda"
                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Daftar Sebagai:</label>
                        <select name="role" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
                            <option value="customer">Pelanggan (Kirim & Kelola Paket)</option>
                            <option value="courier">Kurir Armada (Pengantaran)</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-[#0A1128] dark:bg-[#FF6B00] text-white font-extrabold text-xs hover:bg-[#FF6B00] transition flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.9c2.27-2.09 3.64-5.17 3.64-9.15z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.9-3.05c-1.08.72-2.45 1.16-4.03 1.16-3.1 0-5.74-2.1-6.68-4.94H1.26v3.15C3.3 21.36 7.36 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.32 14.26c-.24-.73-.38-1.5-.38-2.26s.14-1.53.38-2.26V6.59H1.26C.46 8.2 0 10.04 0 12s.46 3.8 1.26 5.41l4.06-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.36 0 3.3 2.64 1.26 6.59l4.06 3.15c.94-2.84 3.58-4.99 6.68-4.99z"/>
                        </svg>
                        <span>Daftar Cepat dengan Google</span>
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
@endsection
