@extends('layouts.admin')

@section('title', 'Kelola Kurir & Akun Pengguna')
@section('header_title', 'Manajemen Akun')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTION -->
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-6 border border-slate-200/80 dark:border-white/5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[11px] font-black tracking-widest uppercase text-[#FF6B00]">PENGATURAN AKSES & ARMADA</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-[11px] font-bold text-emerald-500">Database RBAC</span>
            </div>
            <h1 class="text-2xl font-black text-[#0A1128] dark:text-white tracking-tight">
                Kelola Kurir & Akun Pengguna
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Tambahkan akun kurir armada baru, kelola profil pelanggan, dan pantau status aktif seluruh pengguna sistem.
            </p>
        </div>

        <button type="button" onclick="openCreateUserModal()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-[#FF6B00] hover:bg-[#E55F00] text-white text-xs font-bold shadow-md shadow-[#FF6B00]/25 transition">
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>Tambah Kurir / Akun Baru</span>
        </button>
    </div>

    <!-- 4 BENTO SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Pengguna -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Seluruh Akun</p>
                <h3 class="text-xl font-black text-[#0A1128] dark:text-white mt-1">{{ $totalUsers }} User</h3>
                <span class="text-[10px] font-bold text-slate-400 mt-1 inline-block">Terdaftar di Sistem</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- 2. Kurir Armada -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Kurir Armada</p>
                <h3 class="text-xl font-black text-[#FF6B00] mt-1">{{ $totalCouriers }} Kurir</h3>
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mt-1 inline-block">
                    <i class="fa-solid fa-circle text-[8px] mr-1"></i> {{ $activeCouriers }} Aktif Bertugas
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-[#FF6B00] flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-motorcycle"></i>
            </div>
        </div>

        <!-- 3. Pelanggan -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Pelanggan / Customer</p>
                <h3 class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $totalCustomers }} Akun</h3>
                <span class="text-[10px] font-bold text-slate-400 mt-1 inline-block">Klien Ekspedisi</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-user-tag"></i>
            </div>
        </div>

        <!-- 4. Administrator -->
        <div class="bg-white dark:bg-[#0D1527] rounded-3xl p-5 border border-slate-200/80 dark:border-white/5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Administrator</p>
                <h3 class="text-xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $totalUsers - $totalCouriers - $totalCustomers }} Admin</h3>
                <span class="text-[10px] font-bold text-slate-400 mt-1 inline-block">Super Admin Ops</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>
    </div>

    <!-- SEARCH & FILTER FORM -->
    <div class="bg-white dark:bg-[#0D1527] rounded-2xl p-4 border border-slate-200/80 dark:border-white/5 shadow-sm">
        <form method="GET" action="{{ route('admin.users') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Search Keyword -->
            <div class="relative sm:col-span-2">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau nomor HP kurir/pengguna..."
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:outline-none focus:border-[#FF6B00]">
            </div>

            <!-- Role Filter -->
            <div class="flex items-center gap-2">
                <select name="role" onchange="this.form.submit()"
                        class="flex-1 px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-white/10 dark:bg-[#0D1527] dark:text-white focus:outline-none focus:border-[#FF6B00]">
                    <option value="">-- Semua Hak Akses --</option>
                    <option value="courier" {{ request('role') === 'courier' ? 'selected' : '' }}>Kurir Armada</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Pelanggan (Customer)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-[#0A1128] dark:bg-white/10 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users') }}" class="px-3 py-2 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- USERS TABLE -->
    <div class="bg-white dark:bg-[#0D1527] rounded-3xl border border-slate-200/80 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-white/5 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-white/5">
                    <tr>
                        <th class="px-6 py-4">Nama Pengguna & Email</th>
                        <th class="px-6 py-4">Hak Akses (Role)</th>
                        <th class="px-6 py-4">Nomor HP / Kontak</th>
                        <th class="px-6 py-4">Status Kurir</th>
                        <th class="px-6 py-4">Terdaftar Sejak</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition">
                            <!-- User Details with Avatar Photo -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-white/10 shrink-0 shadow-sm">
                                    <div>
                                        <span class="font-extrabold text-[#0A1128] dark:text-white text-xs block">
                                            {{ $user->name }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono">
                                            {{ $user->email }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="px-6 py-4">
                                @if(in_array($user->role, ['courier', 'kurir']))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-orange-100 text-[#FF6B00] dark:bg-orange-950/40 dark:text-orange-400 border border-orange-200 dark:border-orange-900/50">
                                        <i class="fa-solid fa-motorcycle text-xs"></i>
                                        <span>Kurir Armada</span>
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-[#0A1128] text-white border border-slate-700">
                                        <i class="fa-solid fa-user-shield text-xs text-[#FF6B00]"></i>
                                        <span>Administrator</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        <i class="fa-solid fa-user text-xs"></i>
                                        <span>Pelanggan</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Telepon -->
                            <td class="px-6 py-4 font-mono text-xs">
                                {{ $user->phone ?: '-' }}
                            </td>

                            <!-- Status Kurir -->
                            <td class="px-6 py-4">
                                @if(in_array($user->role, ['courier', 'kurir']))
                                    @if($user->courier_status === 'active')
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Sedang Bertugas</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400">
                                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                            <span>Istirahat / Nonaktif</span>
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-600 italic">Standar</span>
                                @endif
                            </td>

                            <!-- Tanggal -->
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Edit Button -->
                                    <button type="button" onclick="openEditUserModal({{ json_encode($user) }})"
                                            title="Edit Profil & Hak Akses"
                                            class="p-2 rounded-xl bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 text-slate-700 dark:text-slate-200 text-xs transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button (Except current logged in user) -->
                                    @if(auth()->id() !== $user->id)
                                        <button type="button" onclick="openDeleteUserModal('{{ $user->id }}', '{{ addslashes($user->name) }}')"
                                                title="Hapus Akun"
                                                class="p-2 rounded-xl bg-slate-100 dark:bg-white/10 hover:bg-rose-100 dark:hover:bg-rose-950/50 text-slate-400 hover:text-rose-600 text-xs transition">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data pengguna yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ============================================================== -->
<!-- MODAL: TAMBAH KURIR / PENGGUNA BARU                            -->
<!-- ============================================================== -->
<div id="modalCreateUser" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-[#101935] rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-orange-50 dark:bg-orange-950/40 text-[#FF6B00] flex items-center justify-center font-bold">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#0A1128] dark:text-white">Tambah Kurir / Akun Baru</h3>
                    <p class="text-xs text-slate-400">Daftarkan personel kurir atau pengguna ke dalam sistem Meridian.</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateUserModal()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Role Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Akun (Hak Akses) *</label>
                <select name="role" id="createUserRole" required onchange="toggleCourierStatusInput(this.value, 'createStatusDiv')"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                    <option value="courier" selected>🛵 Kurir Armada (Delivery Officer)</option>
                    <option value="customer">👤 Pelanggan (Customer)</option>
                    <option value="admin">👑 Administrator (Super Admin)</option>
                </select>
            </div>

            <!-- Foto Avatar / Kurir -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Foto Profil / Kurir (Maks 2MB)</label>
                <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#FF6B00] file:text-white file:cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl p-1 bg-white dark:bg-[#141E3D]">
                <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maksimal ukuran 2MB.</p>
            </div>

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso (Kurir Van)"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs focus:outline-none focus:border-[#FF6B00]">
            </div>

            <!-- Email & Nomor HP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Akun *</label>
                    <input type="email" name="email" required placeholder="budi@meridian.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs focus:outline-none focus:border-[#FF6B00]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" placeholder="081234567890"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs focus:outline-none focus:border-[#FF6B00]">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Password Awal *</label>
                <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter..."
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs focus:outline-none focus:border-[#FF6B00]">
            </div>

            <!-- Status Bertugas (Khusus Kurir) -->
            <div id="createStatusDiv">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Kesiapan Bertugas</label>
                <select name="courier_status"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                    <option value="active" selected>Aktif / Siap Bertugas</option>
                    <option value="inactive">Nonaktif / Libur</option>
                </select>
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeCreateUserModal()"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition">Batal</button>
                <button type="reset"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-white/10 dark:hover:bg-white/15 text-slate-700 dark:text-slate-300 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span>Reset</span>
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#FF6B00] hover:bg-[#E55F00] text-white text-xs font-bold transition shadow-md">
                    Simpan Akun Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: EDIT AKUN PENGGUNA                                      -->
<!-- ============================================================== -->
<div id="modalEditUser" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-[#101935] rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#0A1128] dark:text-white">Edit Akun Pengguna</h3>
                    <p id="editUserSubheader" class="text-xs text-slate-400">Ubah data identitas, nomor HP, atau role akun.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditUserModal()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white text-xl font-bold">&times;</button>
        </div>

        <form id="editUserForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Role Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tipe Akun (Hak Akses) *</label>
                <select name="role" id="editUserRole" required onchange="toggleCourierStatusInput(this.value, 'editStatusDiv')"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                    <option value="courier">🛵 Kurir Armada</option>
                    <option value="customer">👤 Pelanggan (Customer)</option>
                    <option value="admin">👑 Administrator (Super Admin)</option>
                </select>
            </div>

            <!-- Ganti Foto Avatar / Kurir -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Foto Profil / Kurir (Maks 2MB)</label>
                <input type="file" name="avatar" id="editUserAvatar" accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#FF6B00] file:text-white file:cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl p-1 bg-white dark:bg-[#141E3D]">
                <p class="text-[10px] text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengubah foto profil.</p>
            </div>

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap *</label>
                <input type="text" id="editUserName" name="name" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs focus:outline-none focus:border-[#FF6B00]">
            </div>

            <!-- Email & Nomor HP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Akun *</label>
                    <input type="email" id="editUserEmail" name="email" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs focus:outline-none focus:border-[#FF6B00]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" id="editUserPhone" name="phone"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs focus:outline-none focus:border-[#FF6B00]">
                </div>
            </div>

            <!-- Password Baru (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" id="editUserPassword" minlength="6" placeholder="Masukkan password baru..."
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs focus:outline-none focus:border-[#FF6B00]">
            </div>

            <!-- Status Bertugas (Khusus Kurir) -->
            <div id="editStatusDiv">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Kesiapan Bertugas</label>
                <select name="courier_status" id="editUserCourierStatus"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#141E3D] dark:text-white text-xs font-bold focus:outline-none focus:border-[#FF6B00] bg-white">
                    <option value="active">Aktif / Siap Bertugas</option>
                    <option value="inactive">Nonaktif / Libur</option>
                </select>
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeEditUserModal()"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition">Batal</button>
                <button type="button" onclick="resetEditUserForm()"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-white/10 dark:hover:bg-white/15 text-slate-700 dark:text-slate-300 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span>Reset</span>
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: KONFIRMASI HAPUS PENGGUNA MODERN                        -->
<!-- ============================================================== -->
<div id="modalConfirmDeleteUser" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-[#101935] rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 dark:border-white/10 text-center transform transition-all">
        <!-- Warning Icon -->
        <div class="w-16 h-16 rounded-3xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-2xl font-bold mx-auto mb-4 border border-rose-200 dark:border-rose-500/20 shadow-inner">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <h3 class="text-lg font-black text-[#0A1128] dark:text-white">
            Konfirmasi Hapus Akun
        </h3>

        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
            Apakah Anda yakin ingin menghapus akun pengguna <span id="deleteUserNameDisplay" class="font-bold text-[#FF6B00]">Nama Pengguna</span>? Tindakan ini tidak dapat dibatalkan.
        </p>

        <!-- Aksi Tombol -->
        <div class="mt-6 flex items-center justify-center gap-3">
            <button type="button" onclick="closeDeleteUserModal()"
                    class="flex-1 py-3 px-4 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition">
                Batal
            </button>
            <form id="deleteUserForm" action="" method="POST" class="flex-1 m-0">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black uppercase tracking-wider transition shadow-lg shadow-rose-600/30 flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Ya, Hapus</span>
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentEditingUser = null;

    function openCreateUserModal() {
        document.getElementById('modalCreateUser').classList.remove('hidden');
    }
    function closeCreateUserModal() {
        document.getElementById('modalCreateUser').classList.add('hidden');
    }

    function openEditUserModal(user) {
        currentEditingUser = user;
        document.getElementById('editUserForm').action = '/admin/users/' + user.id;
        document.getElementById('editUserName').value = user.name || '';
        document.getElementById('editUserEmail').value = user.email || '';
        document.getElementById('editUserPhone').value = user.phone || '';
        document.getElementById('editUserRole').value = user.role || 'customer';
        document.getElementById('editUserCourierStatus').value = user.courier_status || 'active';
        toggleCourierStatusInput(user.role, 'editStatusDiv');
        document.getElementById('modalEditUser').classList.remove('hidden');
    }
    function closeEditUserModal() {
        document.getElementById('modalEditUser').classList.add('hidden');
    }

    // Tombol Reset pada Modal Edit Akun: Mengosongkan seluruh teks isian di dalam kotak formulir
    function resetEditUserForm() {
        const nameInput = document.getElementById('editUserName');
        if (nameInput) nameInput.value = '';

        const emailInput = document.getElementById('editUserEmail');
        if (emailInput) emailInput.value = '';

        const phoneInput = document.getElementById('editUserPhone');
        if (phoneInput) phoneInput.value = '';

        const passInput = document.getElementById('editUserPassword');
        if (passInput) passInput.value = '';

        const avatarInput = document.getElementById('editUserAvatar');
        if (avatarInput) avatarInput.value = '';

        if (nameInput) nameInput.focus();
    }

    // Modal Konfirmasi Hapus Akun Modern
    function openDeleteUserModal(userId, userName) {
        document.getElementById('deleteUserNameDisplay').textContent = userName;
        document.getElementById('deleteUserForm').action = '/admin/users/' + userId;
        document.getElementById('modalConfirmDeleteUser').classList.remove('hidden');
    }

    function closeDeleteUserModal() {
        document.getElementById('modalConfirmDeleteUser').classList.add('hidden');
    }

    function toggleCourierStatusInput(role, targetDivId) {
        const div = document.getElementById(targetDivId);
        if (role === 'courier' || role === 'kurir') {
            div.classList.remove('hidden');
        } else {
            div.classList.add('hidden');
        }
    }
</script>
@endpush
