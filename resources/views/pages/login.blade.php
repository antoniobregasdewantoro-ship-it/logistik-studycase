@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center pt-20 bg-[#FAFAFA]">
    <div
        class="bg-white p-8 rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)] w-full max-w-md border border-slate-100">

        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-[#0A1128]">Selamat Datang</h2>
            <p class="text-slate-500 mt-2">Silakan masuk ke akun Meridian Anda</p>
        </div>

        <!-- Peringatan jika email/password salah -->
        @if($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100 font-medium">
            {{ $errors->first() }}
        </div>
        @endif

        <!-- Form dikirim ke rute proses login (POST) -->
        <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
            @csrf
            <!-- Wajib ada di Laravel untuk keamanan form -->

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                <input type="email" name="email" required placeholder="nama@email.com"
                    class="w-full border border-slate-200 rounded-xl px-5 py-3 focus:outline-none focus:border-[#FF6B00] focus:ring-1 focus:ring-[#FF6B00] transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full border border-slate-200 rounded-xl px-5 py-3 focus:outline-none focus:border-[#FF6B00] focus:ring-1 focus:ring-[#FF6B00] transition">
            </div>

            <button type="submit"
                class="w-full bg-[#0A1128] text-white font-bold py-4 rounded-xl hover:bg-slate-800 transition mt-2">
                Masuk Sekarang
            </button>
        </form>

        <!-- Contekkan Akun untuk Testing -->
        <div class="mt-8 p-5 bg-slate-50 rounded-xl text-sm text-slate-600 border border-slate-100">
            <p class="font-bold mb-2 text-[#0A1128]">Akun Uji Coba (Password: password123)</p>
            <ul class="space-y-1">
                <li>🟢 <b>Admin:</b> admin@meridian.com</li>
                <li>🔵 <b>Kurir:</b> kurir@meridian.com</li>
                <li>🟠 <b>Customer:</b> customer@meridian.com</li>
            </ul>
        </div>

    </div>
</div>
@endsection