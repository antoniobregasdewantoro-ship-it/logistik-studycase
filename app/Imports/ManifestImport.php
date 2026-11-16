<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ManifestImport implements ToModel, WithHeadingRow
{
    /**
     * Memetakan data dari setiap baris file Excel ke dalam model Order.
     * Mendukung format Excel hasil Export (no_resi, pengirim, rute_tujuan, berat_kg, tarif_rp)
     * maupun format materi dosen (nomor_resi, nama_pengirim, tujuan, berat, layanan, tarif).
     */
    public function model(array $row): Model|array|null
    {
        // 1. Ekstraksi nomor resi dari berbagai variasi header
        $resi = trim((string) (
            $row['nomor_resi'] ??
            $row['no_resi'] ??
            $row['resi'] ??
            $row['tracking_number'] ??
            $row['no_tracking'] ??
            ''
        ));

        // 2. ID baris (jika file berasal dari export yang menyertakan kolom ID)
        $orderId = isset($row['id']) && is_numeric($row['id']) ? (int) $row['id'] : null;

        // 3. Nama Pengirim
        $senderName = trim((string) (
            $row['nama_pengirim'] ??
            $row['pengirim'] ??
            $row['sender_name'] ??
            $row['nama'] ??
            ''
        ));

        // 4. Nama Penerima
        $recipientName = trim((string) (
            $row['nama_penerima'] ??
            $row['penerima'] ??
            $row['recipient_name'] ??
            ''
        ));

        // 5. No. HP Penerima
        $recipientPhone = trim((string) (
            $row['no_telp_penerima'] ??
            $row['no_hp_penerima'] ??
            $row['telepon_penerima'] ??
            $row['phone'] ??
            $row['hp'] ??
            ''
        ));

        // 6. Rute Asal
        $origin = trim((string) (
            $row['rute_asal'] ??
            $row['asal'] ??
            $row['kota_asal'] ??
            $row['origin'] ??
            ''
        ));

        // 7. Rute Tujuan
        $destination = trim((string) (
            $row['rute_tujuan'] ??
            $row['tujuan'] ??
            $row['kota_tujuan'] ??
            $row['destination'] ??
            ''
        ));

        // 8. Alamat Lengkap
        $address = trim((string) (
            $row['alamat_lengkap'] ??
            $row['alamat'] ??
            $row['street_address'] ??
            ''
        ));

        // 9. Detail Barang
        $packageDetail = trim((string) (
            $row['detail_barang'] ??
            $row['detail_paket'] ??
            $row['package_detail'] ??
            ''
        ));

        // 10. Jenis Layanan (Reguler, Express, Kargo, Same Day)
        $serviceType = trim((string) (
            $row['layanan'] ??
            $row['jenis_layanan'] ??
            $row['service_type'] ??
            ''
        ));

        // 11. Berat Kargo (Kg)
        $rawWeight = $row['berat'] ?? $row['berat_kg'] ?? $row['weight'] ?? null;
        $weight = $this->parseWeight($rawWeight);

        // 12. Tarif / Harga Ongkir
        $rawPrice = $row['tarif'] ?? $row['tarif_rp'] ?? $row['harga'] ?? $row['price'] ?? null;
        $price = $this->parsePrice($rawPrice);

        // 13. Metode Pembayaran
        $paymentMethod = trim((string) (
            $row['metode_pembayaran'] ??
            $row['metode_bayar'] ??
            $row['payment_method'] ??
            ''
        ));

        // 14. Status Pengiriman
        $status = trim((string) ($row['status'] ?? ''));

        // 15. Catatan Tambahan
        $notes = trim((string) (
            $row['catatan'] ??
            $row['notes'] ??
            ''
        ));

        // Abaikan baris kosong di Excel (jika tidak ada resi, pengirim, tujuan, dan ID)
        if (empty($resi) && empty($senderName) && empty($destination) && empty($orderId)) {
            return null;
        }

        // Cek apakah data pesanan sudah ada di database (berdasarkan nomor resi atau ID)
        $existingOrder = null;
        if (! empty($resi)) {
            $existingOrder = Order::where('tracking_number', $resi)->first();
        }
        if (! $existingOrder && $orderId) {
            $existingOrder = Order::find($orderId);
        }

        // Jika data pesanan sudah ada, perbarui nilainya sesuai perubahan di file Excel
        if ($existingOrder) {
            $updateData = [];

            if (! empty($senderName) && $senderName !== '-') {
                $updateData['sender_name'] = $senderName;
            }
            if (! empty($recipientName) && $recipientName !== '-') {
                $updateData['recipient_name'] = $recipientName;
            }
            if (! empty($recipientPhone) && $recipientPhone !== '-') {
                $updateData['recipient_phone'] = $recipientPhone;
            }
            if (! empty($origin) && $origin !== '-') {
                $updateData['origin'] = $origin;
            }
            if (! empty($destination) && $destination !== '-') {
                $updateData['destination'] = $destination;
            }
            if (! empty($address) && $address !== '-') {
                $updateData['street_address'] = $address;
            }
            if (! empty($packageDetail) && $packageDetail !== '-') {
                $updateData['package_detail'] = $packageDetail;
            }
            if (! empty($serviceType) && $serviceType !== '-') {
                $updateData['service_type'] = $serviceType;
            }
            if ($weight > 0) {
                $updateData['weight'] = $weight;
            }
            if ($price > 0) {
                $updateData['price'] = $price;
            }
            if (! empty($paymentMethod) && $paymentMethod !== '-') {
                $updateData['payment_method'] = $paymentMethod;
            }
            if (! empty($status) && $status !== '-') {
                // Standarisasi status jika sesuai
                $validStatuses = ['Verifikasi', 'Dalam Perjalanan', 'Terkirim'];
                foreach ($validStatuses as $valid) {
                    if (strcasecmp($status, $valid) === 0) {
                        $status = $valid;
                        break;
                    }
                }
                $updateData['status'] = $status;
            }
            if (! empty($notes) && $notes !== '-') {
                $updateData['notes'] = $notes;
            }

            if (! empty($updateData)) {
                $existingOrder->update($updateData);
            }

            return $existingOrder;
        }

        // Jika baris baru tidak memiliki resi, generate nomor resi unik otomatis
        if (empty($resi)) {
            do {
                $resi = 'ORD-'.rand(10000, 99999);
            } while (Order::where('tracking_number', $resi)->exists());
        }

        // Hitung estimasi tarif otomatis jika kolom tarif di Excel kosong
        if ($price <= 0 && $weight > 0) {
            $rates = [
                'Reguler' => 9000,
                'Express' => 15000,
                'Kargo' => 6000,
                'Same Day' => 20000,
            ];
            $ratePerKg = $rates[$serviceType] ?? 9000;
            $price = $weight * $ratePerKg;
        }

        // Simpan baris baru ke database tabel orders
        return new Order([
            'tracking_number' => $resi,
            'sender_name' => ! empty($senderName) ? $senderName : 'Pengirim',
            'recipient_name' => ! empty($recipientName) && $recipientName !== '-' ? $recipientName : ((! empty($destination) ? $destination : 'Tujuan').' Receiver'),
            'recipient_phone' => ! empty($recipientPhone) && $recipientPhone !== '-' ? $recipientPhone : null,
            'origin' => ! empty($origin) ? $origin : 'Sidoarjo',
            'destination' => ! empty($destination) ? $destination : 'Surabaya',
            'street_address' => ! empty($address) && $address !== '-' ? $address : null,
            'package_detail' => ! empty($packageDetail) && $packageDetail !== '-' ? $packageDetail : null,
            'weight' => $weight > 0 ? $weight : 1.0,
            'service_type' => ! empty($serviceType) ? $serviceType : 'Reguler',
            'price' => $price > 0 ? $price : 9000.0,
            'status' => ! empty($status) && $status !== '-' ? $status : 'Verifikasi',
            'payment_method' => ! empty($paymentMethod) && $paymentMethod !== '-' ? $paymentMethod : 'QRIS (Lunas)',
            'delay_risk' => 'Low',
            'delay_probability' => 15,
            'checkpoint' => 'Manifest diimpor dari file Excel. Menunggu verifikasi admin.',
            'notes' => ! empty($notes) && $notes !== '-' ? $notes : null,
        ]);
    }

    /**
     * Konversi nilai berat ke format float
     */
    private function parseWeight(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $str = preg_replace('/[^\d.,]/', '', (string) $value);
        if (empty($str)) {
            return 0.0;
        }
        if (str_contains($str, '.') && str_contains($str, ',')) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } elseif (str_contains($str, ',')) {
            $str = str_replace(',', '.', $str);
        }

        return is_numeric($str) ? (float) $str : 0.0;
    }

    /**
     * Konversi nilai tarif ke format float numerik bersih
     */
    private function parsePrice(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $str = preg_replace('/[^\d.,]/', '', (string) $value);
        if (empty($str)) {
            return 0.0;
        }
        if (str_contains($str, '.') && str_contains($str, ',')) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } elseif (str_contains($str, '.')) {
            $parts = explode('.', $str);
            if (count($parts) > 2 || (count($parts) === 2 && strlen($parts[1]) === 3)) {
                $str = str_replace('.', '', $str);
            }
        } elseif (str_contains($str, ',')) {
            $parts = explode(',', $str);
            if (count($parts) > 2 || (count($parts) === 2 && strlen($parts[1]) === 3)) {
                $str = str_replace(',', '', $str);
            } else {
                $str = str_replace(',', '.', $str);
            }
        }

        return is_numeric($str) ? (float) $str : 0.0;
    }
}
