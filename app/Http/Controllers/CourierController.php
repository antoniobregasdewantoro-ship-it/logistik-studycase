<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourierController extends Controller
{
    /**
     * Web Dashboard Khusus Kurir "Meridian Kurir" (Persis Desain Figma Gambar 2)
     */
    public function tasks(Request $request): View
    {
        $user = Auth::user();

        // Cari tugas untuk kurir yang login, atau semua order jika sedang demo/testing
        $query = Order::query();

        if ($user && $user->role !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('courier_id', $user->id)
                    ->orWhereNull('courier_id');
            });
        }

        if ($request->filled('tab')) {
            if ($request->tab === 'in_transit') {
                $query->where('status', 'Dalam Perjalanan');
            } elseif ($request->tab === 'delivered') {
                $query->where('status', 'Terkirim');
            } elseif ($request->tab === 'pending') {
                $query->where('status', 'Verifikasi');
            }
        }

        $tasks = $query->latest()->get();

        $totalCount = Order::count();
        $deliveredCount = Order::where('status', 'Terkirim')->count();
        $inTransitCount = Order::where('status', 'Dalam Perjalanan')->count();

        // Selected task untuk panel navigasi kanan (default: tugas pertama yang masih jalan)
        $selectedTask = null;
        if ($request->filled('selected')) {
            $selectedTask = Order::find($request->selected);
        }
        if (! $selectedTask) {
            $selectedTask = $tasks->firstWhere('status', 'Dalam Perjalanan') ?: $tasks->first();
        }

        $stats = [
            'total' => $totalCount,
            'delivered' => $deliveredCount,
            'in_transit' => $inTransitCount,
            'pending' => max(0, $totalCount - $deliveredCount - $inTransitCount),
        ];

        $isOnline = ($user->courier_status ?? 'online') === 'online';

        return view('courier.tasks', compact(
            'tasks',
            'totalCount',
            'deliveredCount',
            'inTransitCount',
            'stats',
            'selectedTask',
            'isOnline'
        ));
    }

    /**
     * Matikan / Nyalakan Aplikasi Kurir (Gojek-style Toggle Online / Offline)
     */
    public function toggleStatus(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $newStatus = ($user->courier_status ?? 'online') === 'online' ? 'offline' : 'online';
            $user->update(['courier_status' => $newStatus]);

            $msg = $newStatus === 'online'
                ? 'Aplikasi Kurir AKTIF (Online). Anda siap menerima dan mengantar manifes paket hari ini!'
                : 'Aplikasi Kurir DIMATIKAN (Offline / Istirahat). Manifes pengantaran disembunyikan sementara.';

            return redirect()->back()->with('success', $msg);
        }

        return redirect()->back();
    }

    /**
     * Update Status Pengiriman & Titik Checkpoint GPS
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Verifikasi,Dalam Perjalanan,Terkirim',
            'checkpoint' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $validated['status'],
            'checkpoint' => $validated['checkpoint'],
            'latitude' => $validated['latitude'] ?? $order->latitude,
            'longitude' => $validated['longitude'] ?? $order->longitude,
        ]);

        return redirect()->back()->with('success', "Status pengantaran {$order->tracking_number} berhasil diubah menjadi {$validated['status']}.");
    }

    /**
     * Upload Bukti Foto Pengiriman oleh Kurir (Validasi Ketat: max 2MB, jpeg/png/jpg)
     */
    public function uploadProof(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB
            'notes' => 'nullable|string|max:300',
        ], [
            'proof_image.required' => 'Wajib memilih atau mengambil foto bukti serah terima.',
            'proof_image.image' => 'File harus berupa gambar.',
            'proof_image.mimes' => 'Format file yang diizinkan hanya JPG, JPEG, atau PNG.',
            'proof_image.max' => 'Ukuran foto maksimal 2MB (2048 KB). Silakan kompres atau perkecil resolusi foto Anda.',
        ]);

        $order = Order::findOrFail($id);

        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $fileName = 'courier_proof_'.$order->tracking_number.'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('proofs', $fileName, 'public');

            $order->update([
                'proof_image' => $path,
                'status' => 'Terkirim',
                'checkpoint' => 'Paket telah berhasil diserahkan ke penerima: '.($order->recipient_name ?? 'Pelanggan'),
                'notes' => $request->notes ?? $order->notes,
            ]);
        }

        return redirect()->back()->with('success', "Bukti foto pengiriman {$order->tracking_number} berhasil disimpan!");
    }
}
