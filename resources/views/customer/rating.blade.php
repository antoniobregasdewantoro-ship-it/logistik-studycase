@extends('layouts.app')

@section('title', 'Beri Ulasan Pengiriman - Meridian')

@section('content')
<div class="max-w-xl mx-auto px-4 py-12">
    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm text-center">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl font-bold mx-auto mb-4">
            <i class="fa-solid fa-star"></i>
        </div>
        <h2 class="text-2xl font-extrabold text-[#0A1128]">Kepuasan Layanan Pengiriman</h2>
        <p class="text-xs text-slate-500 mt-1 mb-6">Beri rating kepuasan Anda terhadap pengantaran kurir dan armada Meridian</p>

        <form action="{{ route('customer.dashboard') }}" method="GET" class="space-y-5 text-left">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Resi Pengiriman</label>
                <input type="text" placeholder="MRD-XXXXX" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Penilaian Bintang (1 - 5 Bintang)</label>
                <div class="flex items-center justify-center gap-3 text-3xl text-amber-400 py-2">
                    <i class="fa-solid fa-star cursor-pointer hover:scale-125 transition"></i>
                    <i class="fa-solid fa-star cursor-pointer hover:scale-125 transition"></i>
                    <i class="fa-solid fa-star cursor-pointer hover:scale-125 transition"></i>
                    <i class="fa-solid fa-star cursor-pointer hover:scale-125 transition"></i>
                    <i class="fa-solid fa-star cursor-pointer hover:scale-125 transition"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Ulasan / Kritik & Saran</label>
                <textarea rows="3" placeholder="Ceritakan pengalaman Anda..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"></textarea>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-[#0A1128] hover:bg-slate-800 text-white font-bold text-xs transition shadow-md">
                Kirim Penilaian
            </button>
        </form>
    </div>
</div>
@endsection
