<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    /**
     * Ambil seluruh data pesanan beserta relasi customer & courier.
     */
    public function collection(): Enumerable
    {
        return Order::with(['customer', 'courier'])->latest()->get();
    }

    /**
     * Judul sheet Excel.
     */
    public function title(): string
    {
        return 'Manifest Pengiriman';
    }

    /**
     * Baris judul kolom (header) pada baris pertama Excel.
     */
    public function headings(): array
    {
        return [
            'No',
            'No. Resi',
            'Tanggal Dibuat',
            'Pengirim',
            'Penerima',
            'No. HP Penerima',
            'Asal',
            'Tujuan',
            'Berat (Kg)',
            'Layanan',
            'Tarif (Rp)',
            'Metode Bayar',
            'Kurir',
            'Status',
            'Checkpoint',
            'Risiko Delay',
            'Catatan',
        ];
    }

    /**
     * Mapping setiap baris data Order ke kolom Excel.
     */
    public function map($order): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $order->tracking_number,
            $order->created_at->format('d/m/Y H:i'),
            $order->sender_name ?? '-',
            $order->recipient_name ?? '-',
            $order->recipient_phone ?? '-',
            $order->origin,
            $order->destination,
            number_format($order->weight, 2, ',', '.'),
            $order->service_type,
            number_format($order->price, 0, ',', '.'),
            $order->payment_method ?? 'QRIS',
            $order->courier?->name ?? 'Belum Ditugaskan',
            $order->status,
            $order->checkpoint ?? '-',
            $order->delay_risk.' ('.$order->delay_probability.'%)',
            $order->notes ?? '-',
        ];
    }

    /**
     * Lebar kolom agar rapi dan proporsional (WithColumnWidths).
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,     // No
            'B' => 16,    // No. Resi
            'C' => 18,    // Tanggal
            'D' => 20,    // Pengirim
            'E' => 20,    // Penerima
            'F' => 16,    // No. HP
            'G' => 15,    // Asal
            'H' => 15,    // Tujuan
            'I' => 12,    // Berat
            'J' => 14,    // Layanan
            'K' => 16,    // Tarif
            'L' => 18,    // Metode Bayar
            'M' => 22,    // Kurir
            'N' => 18,    // Status
            'O' => 35,    // Checkpoint
            'P' => 18,    // Risiko
            'Q' => 25,    // Catatan
        ];
    }

    /**
     * Styling header: bold, background warna biru tua, font putih, dan border (WithStyles).
     */
    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'Q';

        return [
            // === Styling baris HEADER (baris 1) ===
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                    'name' => 'Calibri',
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0A1128'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF334155'],
                    ],
                ],
            ],

            // === Styling seluruh area data (baris 2 sampai akhir) ===
            "A2:{$lastCol}{$lastRow}" => [
                'font' => [
                    'size' => 10,
                    'name' => 'Calibri',
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE2E8F0'],
                    ],
                ],
            ],

            // === Kolom Nomor center ===
            "A2:A{$lastRow}" => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            // === Kolom Tarif rata kanan ===
            "K2:K{$lastRow}" => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
        ];
    }
}
