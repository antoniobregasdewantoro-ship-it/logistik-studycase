<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ManifestImport implements ToModel, WithHeadingRow
{
    /**
     * Memetakan data dari setiap baris file Excel ke dalam model Order
     */
    public function model(array $row): Model|array|null
    {
        // Abaikan baris jika kolom nomor_resi kosong
        if (empty($row['nomor_resi'])) {
            return null;
        }

        // Sanitasi nilai numerik berat dan tarif
        $berat = isset($row['berat']) ? (float) str_replace(',', '.', (string) $row['berat']) : 1.0;
        $tarif = isset($row['tarif']) ? (float) preg_replace('/[^0-9.]/', '', (string) $row['tarif']) : 0.0;
        $resi = trim((string) $row['nomor_resi']);

        // Jika nomor resi sudah ada di database, perbarui data agar terhindar dari error duplikasi
        $existingOrder = Order::where('tracking_number', $resi)->first();
        if ($existingOrder) {
            $existingOrder->update([
                'sender_name' => $row['nama_pengirim'] ?? $existingOrder->sender_name,
                'destination' => $row['tujuan'] ?? $existingOrder->destination,
                'weight' => $berat,
                'service_type' => ! empty($row['layanan']) ? $row['layanan'] : $existingOrder->service_type,
                'price' => $tarif > 0 ? $tarif : $existingOrder->price,
                'status' => 'Verifikasi',
            ]);

            return $existingOrder;
        }

        // Buat record pesanan / manifest baru dengan default status 'Verifikasi'
        return new Order([
            'tracking_number' => $resi,
            'sender_name' => $row['nama_pengirim'] ?? 'Pengirim',
            'recipient_name' => $row['nama_penerima'] ?? (($row['tujuan'] ?? 'Tujuan').' Receiver'),
            'origin' => $row['asal'] ?? 'Sidoarjo',
            'destination' => $row['tujuan'] ?? 'Surabaya',
            'weight' => $berat,
            'service_type' => ! empty($row['layanan']) ? $row['layanan'] : 'Reguler',
            'price' => $tarif,
            'status' => 'Verifikasi',
            'payment_method' => $row['metode_pembayaran'] ?? 'QRIS (Lunas)',
            'delay_risk' => 'Low',
            'delay_probability' => 15,
            'checkpoint' => 'Manifest diimpor dari file Excel. Menunggu verifikasi admin.',
        ]);
    }
}
