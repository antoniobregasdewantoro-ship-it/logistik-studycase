<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersTemplateExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles, WithTitle
{
    /**
     * Judul sheet template.
     */
    public function title(): string
    {
        return 'Template Import Barang';
    }

    /**
     * Header kolom template — sesuai field yang bisa diimport.
     */
    public function headings(): array
    {
        return [
            'Nama Pengirim *',
            'Nama Penerima *',
            'No. HP Penerima',
            'Kota Asal *',
            'Kota Tujuan *',
            'Berat (Kg) *',
            'Layanan *',
            'Metode Bayar',
            'Detail Paket',
            'Catatan',
        ];
    }

    /**
     * Contoh data (2 baris) agar user tahu format pengisian.
     */
    public function array(): array
    {
        return [
            ['Budi Santoso', 'Rina Wati', '08123456789', 'Sidoarjo', 'Surabaya', '3.5', 'Reguler', 'QRIS (Lunas)', 'Sparepart Motor', 'Hati-hati mudah pecah'],
            ['Dewi Ayu', 'Ahmad Fauzi', '08567891234', 'Malang', 'Jakarta', '12.0', 'Express', 'COD', 'Elektronik TV 32 inch', 'Fragile — bungkus bubble wrap'],
        ];
    }

    /**
     * Lebar kolom proporsional.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 22,    // Pengirim
            'B' => 22,    // Penerima
            'C' => 18,    // HP
            'D' => 16,    // Asal
            'E' => 16,    // Tujuan
            'F' => 12,    // Berat
            'G' => 16,    // Layanan
            'H' => 18,    // Bayar
            'I' => 25,    // Detail
            'J' => 30,    // Catatan
        ];
    }

    /**
     * Styling header: bold, warna hijau tua, font putih.
     */
    public function styles(Worksheet $sheet): array
    {
        // Tambahkan keterangan (note) di atas sebagai panduan
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', '📋 TEMPLATE IMPORT BARANG — Isi minimal 10 baris mulai baris ke-3. Kolom bertanda (*) wajib diisi. Layanan: Reguler / Express / Kargo / Same Day');
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Header asli pindah ke baris 2 (karena baris 1 sudah dipakai keterangan)
        // Maatwebsite otomatis taruh headings di baris setelah data array, tapi kita pakai dari array
        // Kita styling baris 1 sebagai info dan baris 2 sebagai header

        return [
            // === Baris keterangan (baris 1) ===
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 10,
                    'color' => ['argb' => 'FF1E40AF'],
                    'name' => 'Calibri',
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFDBEAFE'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],

            // === Baris HEADER (baris 2 — auto dari headings) ===
            2 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                    'name' => 'Calibri',
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF166534'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF15803D'],
                    ],
                ],
            ],

            // === Contoh data (baris 3-4) ===
            'A3:J4' => [
                'font' => [
                    'size' => 10,
                    'color' => ['argb' => 'FF64748B'],
                    'italic' => true,
                    'name' => 'Calibri',
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF0FDF4'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE2E8F0'],
                    ],
                ],
            ],
        ];
    }
}
