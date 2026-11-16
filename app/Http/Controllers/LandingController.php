<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LandingContent;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Halaman Utama: Landing Page Dinamis + Kalkulator Tarif Ongkir Cerdas
     */
    public function index(Request $request): mixed
    {
        // Jika ada query tracking dari link lama, alihkan ke pelacakan pelanggan
        if ($request->filled('tracking')) {
            return redirect()->route('tracking', ['tracking' => $request->tracking]);
        }

        $jumlahKota = Branch::distinct('city')->count() ?: 13;
        $totalKurir = User::whereIn('role', ['courier', 'kurir'])->count() ?: 24;
        $branches = Branch::all();

        // Konten Landing Dinamis dari Admin
        $heroContent = LandingContent::where('section_key', 'hero')->first();
        $carouselContent = LandingContent::where('section_key', 'carousel')->first();

        // Kalkulator Ongkir Default
        $weight = (float) $request->input('weight', 2.0);
        $service = $request->input('service', 'Reguler');
        $origin = $request->input('origin', 'Sidoarjo');
        $destination = $request->input('destination', 'Surabaya');

        $rates = [
            'Reguler' => 9000,
            'Express' => 15000,
            'Kargo' => 6000,
            'Same Day' => 20000,
        ];
        $baseRate = $rates[$service] ?? 9000;
        $totalPrice = $weight * $baseRate;

        $tariffResult = [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'service' => $service,
            'rate_per_kg' => $baseRate,
            'total_price' => $totalPrice,
        ];

        return view('pages.landing', compact(
            'jumlahKota',
            'totalKurir',
            'branches',
            'tariffResult',
            'heroContent',
            'carouselContent'
        ));
    }

    /**
     * Halaman Khusus Pelacakan Resi & Cek Tarif Terpadu (Khusus Pelanggan / Customer)
     */
    public function trackingPage(Request $request): mixed
    {
        // Proteksi: Pelacakan resi eksklusif untuk pelanggan / pengguna terdaftar
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Pelacakan resi eksklusif untuk pelanggan terdaftar. Silakan masuk ke akun pelanggan Anda.');
        }

        $branches = Branch::all();
        $trackedOrder = null;
        $trackingError = null;

        if ($request->filled('tracking')) {
            $trackingQuery = strtoupper(trim($request->tracking));
            $foundOrder = Order::with(['courier', 'customer'])
                ->where('tracking_number', $trackingQuery)
                ->first();

            if ($foundOrder) {
                // Sesuai aturan: Pelanggan hanya bisa melihat & melacak paket sendiri, tidak bisa cek resi orang lain
                if (auth()->check() && auth()->user()->role === 'customer') {
                    $isOwner = ($foundOrder->customer_id === auth()->id()) ||
                        (strtolower(trim($foundOrder->sender_name)) === strtolower(trim(auth()->user()->name))) ||
                        ($foundOrder->customer_id === null && $foundOrder->recipient_phone === auth()->user()->phone);

                    if (! $isOwner) {
                        $trackedOrder = null;
                        $trackingError = 'Akses Terbatas: Nomor resi ini bukan milik akun Anda. Anda hanya dapat melacak paket kiriman Anda sendiri demi menjaga privasi & keamanan.';
                    } else {
                        $trackedOrder = $foundOrder;
                    }
                } else {
                    $trackedOrder = $foundOrder;
                }
            } else {
                $trackingError = 'Nomor resi "'.$trackingQuery.'" tidak ditemukan di database Meridian Logistics.';
            }
        }

        return view('pages.tracking', compact(
            'branches',
            'trackedOrder',
            'trackingError'
        ));
    }

    /**
     * Endpoint API / AJAX Kalkulator Ongkir Dinamis
     */
    public function calculateTariffApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'weight' => 'required|numeric|min:0.1',
            'service' => 'required|string',
            'origin' => 'nullable|string',
            'destination' => 'nullable|string',
        ]);

        $rates = [
            'Reguler' => 9000,
            'Express' => 15000,
            'Kargo' => 6000,
            'Same Day' => 20000,
        ];

        $service = $validated['service'];
        $baseRate = $rates[$service] ?? 9000;
        $totalPrice = (float) $validated['weight'] * $baseRate;

        return response()->json([
            'status' => 'success',
            'weight' => (float) $validated['weight'],
            'service' => $service,
            'rate_per_kg' => $baseRate,
            'total_price' => $totalPrice,
            'formatted_price' => 'Rp '.number_format($totalPrice, 0, ',', '.'),
        ]);
    }

    /**
     * Endpoint API Live Tracking Resi
     */
    public function trackApi(string $tracking_number): JsonResponse
    {
        $order = Order::with('courier')
            ->where('tracking_number', strtoupper(trim($tracking_number)))
            ->first();

        if (! $order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor resi tidak ditemukan di sistem Meridian.',
            ], 404);
        }

        // Proteksi jika diakses oleh pelanggan: hanya boleh melacak paket sendiri
        if (auth()->check() && auth()->user()->role === 'customer') {
            $isOwner = ($order->customer_id === auth()->id()) ||
                (strtolower(trim($order->sender_name)) === strtolower(trim(auth()->user()->name))) ||
                ($order->customer_id === null && $order->recipient_phone === auth()->user()->phone);

            if (! $isOwner) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses ditolak. Pelanggan hanya dapat melacak nomor resi paket kiriman milik sendiri.',
                ], 403);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'tracking_number' => $order->tracking_number,
                'sender' => $order->sender_name,
                'recipient' => $order->recipient_name,
                'origin' => $order->origin,
                'destination' => $order->destination,
                'service_type' => $order->service_type,
                'weight' => $order->weight,
                'status' => $order->status,
                'checkpoint' => $order->checkpoint,
                'latitude' => $order->latitude,
                'longitude' => $order->longitude,
                'delay_risk' => $order->delay_risk,
                'proof_image' => $order->proof_image ? asset('storage/'.$order->proof_image) : null,
                'courier_name' => $order->courier?->name ?? 'Armada Meridian',
                'qr_code_url' => $order->qr_code_url,
                'updated_at' => $order->updated_at->format('d M Y H:i'),
            ],
        ]);
    }
}
