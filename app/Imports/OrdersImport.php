<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OrdersImport implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    /**
     * Track statistik import untuk ringkasan.
     */
    public int $importedCount = 0;

    public int $skippedCount = 0;

    public array $skippedRows = [];

    /**
     * Baris header berada di baris ke-2 (baris 1 = keterangan info).
     */
    public function headingRow(): int
    {
        return 2;
    }

    /**
     * Validasi setiap baris data sebelum import.
     */
    public function rules(): array
    {
        return [
            'nama_pengirim' => 'required|string|max:150',
            'nama_penerima' => 'required|string|max:150',
            'no_hp_penerima' => 'nullable|string|max:30',
            'kota_asal' => 'required|string|max:100',
            'kota_tujuan' => 'required|string|max:100',
            'berat_kg' => 'required|numeric|min:0.1',
            'layanan' => 'required|string|in:Reguler,Express,Kargo,Same Day',
            'metode_bayar' => 'nullable|string|max:50',
            'detail_paket' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:500',
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia.
     */
    public function customValidationMessages(): array
    {
        return [
            'nama_pengirim.required' => 'Nama Pengirim wajib diisi.',
            'nama_penerima.required' => 'Nama Penerima wajib diisi.',
            'kota_asal.required' => 'Kota Asal wajib diisi.',
            'kota_tujuan.required' => 'Kota Tujuan wajib diisi.',
            'berat_kg.required' => 'Berat (Kg) wajib diisi.',
            'berat_kg.numeric' => 'Berat harus berupa angka.',
            'berat_kg.min' => 'Berat minimal 0.1 Kg.',
            'layanan.required' => 'Layanan wajib diisi.',
            'layanan.in' => 'Layanan harus salah satu: Reguler, Express, Kargo, Same Day.',
        ];
    }

    /**
     * Proses import data dari collection baris Excel.
     */
    public function collection(Collection $rows)
    {
        // Harga per Kg berdasarkan layanan
        $rates = [
            'Reguler' => 9000,
            'Express' => 15000,
            'Kargo' => 6000,
            'Same Day' => 20000,
        ];

        // GPS default berdasarkan kota
        $coords = [
            'jakarta' => ['lat' => -6.2088, 'lng' => 106.8456],
            'surabaya' => ['lat' => -7.2575, 'lng' => 112.7521],
            'sidoarjo' => ['lat' => -7.4478, 'lng' => 112.7183],
            'malang' => ['lat' => -7.9666, 'lng' => 112.6326],
            'mojokerto' => ['lat' => -7.4726, 'lng' => 112.4381],
            'bandung' => ['lat' => -6.9175, 'lng' => 107.6191],
            'semarang' => ['lat' => -6.9667, 'lng' => 110.4167],
            'yogyakarta' => ['lat' => -7.7956, 'lng' => 110.3695],
            'medan' => ['lat' => 3.5952, 'lng' => 98.6722],
            'denpasar' => ['lat' => -8.6705, 'lng' => 115.2126],
            'makassar' => ['lat' => -5.1477, 'lng' => 119.4327],
        ];

        $defaultCourier = User::whereIn('role', ['courier', 'kurir'])->first();

        foreach ($rows as $index => $row) {
            try {
                // Lewati baris contoh (yang italic di template)
                $senderName = trim($row['nama_pengirim'] ?? '');
                if (empty($senderName)) {
                    $this->skippedCount++;
                    $this->skippedRows[] = 'Baris '.($index + 3).': Nama Pengirim kosong';

                    continue;
                }

                $weight = (float) ($row['berat_kg'] ?? 0);
                $service = trim($row['layanan'] ?? 'Reguler');
                $origin = trim($row['kota_asal'] ?? 'Sidoarjo');

                // Hitung tarif
                $ratePerKg = $rates[$service] ?? 9000;
                $price = $weight * $ratePerKg;

                // Cari koordinat GPS
                $cityLower = strtolower($origin);
                $gps = ['lat' => -7.2575, 'lng' => 112.7521];
                foreach ($coords as $key => $val) {
                    if (str_contains($cityLower, $key)) {
                        $gps = $val;
                        break;
                    }
                }

                // Generate resi unik
                do {
                    $resi = 'ORD-'.rand(10000, 99999);
                } while (Order::where('tracking_number', $resi)->exists());

                // Hitung risiko delay
                $delayRisk = 'Low';
                $delayProb = rand(8, 20);
                if ($weight > 20 || $service === 'Kargo') {
                    $delayRisk = 'Medium';
                    $delayProb = rand(30, 48);
                }

                Order::create([
                    'tracking_number' => $resi,
                    'customer_id' => auth()->id(),
                    'courier_id' => $defaultCourier?->id,
                    'sender_name' => $senderName,
                    'recipient_name' => trim($row['nama_penerima'] ?? '-'),
                    'recipient_phone' => trim($row['no_hp_penerima'] ?? ''),
                    'origin' => $origin,
                    'destination' => trim($row['kota_tujuan'] ?? ''),
                    'weight' => $weight,
                    'service_type' => $service,
                    'pickup_time' => now(),
                    'price' => $price,
                    'payment_method' => trim($row['metode_bayar'] ?? 'QRIS (Lunas)'),
                    'delay_risk' => $delayRisk,
                    'delay_probability' => $delayProb,
                    'status' => 'Verifikasi',
                    'latitude' => $gps['lat'],
                    'longitude' => $gps['lng'],
                    'checkpoint' => 'Drop Point '.$origin.' (Import Excel — Menunggu Verifikasi)',
                    'package_detail' => trim($row['detail_paket'] ?? ''),
                    'notes' => trim($row['catatan'] ?? ''),
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->skippedRows[] = 'Baris '.($index + 3).': '.$e->getMessage();
            }
        }
    }
}
