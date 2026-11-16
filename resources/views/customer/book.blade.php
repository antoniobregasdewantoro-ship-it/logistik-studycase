@extends('layouts.app')

@section('title', 'Pesan Pengiriman Baru - Meridian')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3 pb-6 border-b border-slate-100 mb-6">
            <div class="w-12 h-12 rounded-2xl bg-[#0A1128] text-[#FF6B00] flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-box"></i>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-[#0A1128]">Booking Pickup Pengiriman</h2>
                <p class="text-xs text-slate-500">Isi detail paket Anda, kurir Meridian akan menjemput ke alamat Anda</p>
            </div>
        </div>

        <form action="{{ route('home') }}#tarif" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pengirim</label>
                    <input type="text" value="{{ auth()->user()->name }}" readonly class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penerima *</label>
                    <input type="text" placeholder="Nama penerima paket" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kota Asal (Penjemputan)</label>
                    <input type="text" name="origin" value="Sidoarjo" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kota Tujuan *</label>
                    <input type="text" name="destination" placeholder="Contoh: Surabaya" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Estimasi Berat (Kg) *</label>
                    <input type="number" name="weight" step="0.1" min="0.1" value="2.0" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan Layanan</label>
                    <select name="service" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                        <option value="Reguler">Reguler</option>
                        <option value="Express">Express</option>
                        <option value="Kargo">Kargo</option>
                        <option value="Same Day">Same Day</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-[#FF6B00] hover:bg-[#E55F00] text-white font-bold text-xs shadow-md transition">
                    Cek Tarif & Konfirmasi Pickup &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
