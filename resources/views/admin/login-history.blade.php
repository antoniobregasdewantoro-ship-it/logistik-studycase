@extends('layouts.admin')

@section('title', 'Riwayat Login Pengguna')
@section('header_title', 'Riwayat Login (3 Role)')

@section('content')
<div class="space-y-6">

    <!-- HEADER TITLE & INFO -->
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-6 border border-slate-200/80 dark:border-white/5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[11px] font-black tracking-widest uppercase text-[#FF6B00]">AUDIT LOG KEAMANAN & AKSES</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[11px] font-bold text-emerald-500">Live Monitored</span>
            </div>
            <h1 class="text-2xl font-black text-[#0A1128] dark:text-white tracking-tight">
                Riwayat Login Sistem (3 Role)
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Pemantauan komprehensif seluruh aktivitas login ke sistem untuk Administrator, Kurir Armada, dan Pelanggan.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3.5 py-2 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-slate-300 text-xs font-bold border border-slate-200 dark:border-white/10 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-[#FF6B00]"></i>
                <span>Enkripsi Sesi Aktif</span>
            </span>
        </div>
    </div>

    <!-- 4 BENTO SUMMARY STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Login -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Sesi Login</p>
                <h3 class="text-2xl font-black text-[#0A1128] dark:text-white mt-1">{{ number_format($totalLogins, 0, ',', '.') }}</h3>
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-0.5 inline-block">
                    Semua Role Terdaftar
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-800 dark:text-white flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- 2. Admin Logins -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Login Administrator</p>
                <h3 class="text-2xl font-black text-rose-600 mt-1">{{ number_format($adminLogins, 0, ',', '.') }}</h3>
                <span class="text-[10px] font-bold text-rose-500 mt-0.5 inline-block">
                    <i class="fa-solid fa-shield-cat mr-1"></i> Akses Super Admin
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>

        <!-- 3. Courier Logins -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Login Kurir Armada</p>
                <h3 class="text-2xl font-black text-[#FF6B00] mt-1">{{ number_format($courierLogins, 0, ',', '.') }}</h3>
                <span class="text-[10px] font-bold text-amber-500 mt-0.5 inline-block">
                    <i class="fa-solid fa-motorcycle mr-1"></i> Petugas Lapangan
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-[#FF6B00] flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-route"></i>
            </div>
        </div>

        <!-- 4. Customer Logins -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Login Pelanggan</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($customerLogins, 0, ',', '.') }}</h3>
                <span class="text-[10px] font-bold text-emerald-500 mt-0.5 inline-block">
                    <i class="fa-solid fa-circle-check mr-1"></i> Pelanggan Aktif
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>
    </div>

    <!-- FILTER & PENCARIAN -->
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm">
        <form method="GET" action="{{ route('admin.login-history') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama pengguna, email, IP address, atau browser..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-white/10 dark:bg-[#141E3D] text-xs font-bold focus:outline-none focus:border-[#FF6B00]">
            </div>

            <div class="w-full sm:w-56">
                <select name="role" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-white/10 dark:bg-[#141E3D] text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                    <option value="">-- Semua Role (3 Role) --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="courier" {{ request('role') === 'courier' ? 'selected' : '' }}>Kurir Armada</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Pelanggan</option>
                </select>
            </div>

            <button type="submit"
                    class="w-full sm:w-auto px-6 py-2.5 rounded-2xl bg-[#0A1128] dark:bg-[#FF6B00] text-white text-xs font-bold hover:bg-slate-800 transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.login-history') }}"
                   class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 text-center transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- TABEL RIWAYAT LOGIN -->
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl border border-slate-200/80 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-200">
                <thead class="bg-slate-50 dark:bg-white/5 font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200 dark:border-white/10 text-[10px]">
                    <tr>
                        <th class="px-6 py-4">Waktu Login</th>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Hak Akses (Role)</th>
                        <th class="px-6 py-4">Alamat IP</th>
                        <th class="px-6 py-4">Perangkat & Browser</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                    @forelse($histories as $h)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition">
                            <!-- Waktu Login -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-extrabold text-[#0A1128] dark:text-white block font-mono">
                                    {{ $h->login_at ? $h->login_at->format('d M Y, H:i:s') : $h->created_at->format('d M Y, H:i:s') }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $h->login_at ? $h->login_at->diffForHumans() : $h->created_at->diffForHumans() }}
                                </span>
                            </td>

                            <!-- Pengguna -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-white/10 text-[#0A1128] dark:text-white font-extrabold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($h->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-white block">{{ $h->name }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono">{{ $h->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($h->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800/40">
                                        <i class="fa-solid fa-crown text-[9px]"></i>
                                        <span>Administrator</span>
                                    </span>
                                @elseif($h->role === 'courier' || $h->role === 'kurir')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-orange-100 text-[#FF6B00] dark:bg-orange-950/60 dark:text-orange-300 border border-orange-200 dark:border-orange-800/40">
                                        <i class="fa-solid fa-motorcycle text-[9px]"></i>
                                        <span>Kurir Armada</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40">
                                        <i class="fa-solid fa-user text-[9px]"></i>
                                        <span>Pelanggan</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Alamat IP -->
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-slate-600 dark:text-slate-300 text-xs">
                                <i class="fa-solid fa-network-wired text-slate-400 mr-1 text-[11px]"></i>
                                <span>{{ $h->ip_address ?: '127.0.0.1' }}</span>
                            </td>

                            <!-- Perangkat / Browser -->
                            <td class="px-6 py-4">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block truncate max-w-xs" title="{{ $h->user_agent }}">
                                        {{ $h->device ?: 'Browser' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono truncate max-w-xs block" title="{{ $h->user_agent }}">
                                        {{ Str::limit($h->user_agent, 45) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40">
                                    <i class="fa-solid fa-circle-check text-emerald-500 text-[10px]"></i>
                                    <span>{{ $h->status ?? 'Berhasil' }}</span>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left text-3xl mb-2 text-slate-300 dark:text-slate-700 block"></i>
                                <span class="font-bold text-sm block">Belum ada catatan riwayat login.</span>
                                <span class="text-xs">Aktivitas login baru dari 3 role akan otomatis tercatat di sini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($histories->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-white/5">
                {{ $histories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
