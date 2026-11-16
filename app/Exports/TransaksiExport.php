<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransaksiExport implements Export, FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    // Eager loading relasi pelanggan dan kurir sekaligus
    public function collection(): Enumerable
    {
        return Order::with(['customer', 'courier'])->latest()->get();
    }

    // Judul kolom file rekapitulasi transaksi & keuangan
    public function headings(): array
    {
        return [
            'ID',
            'No. Resi',
            'Pelanggan',
            'Nama Kurir',
            'Layanan Pengiriman',
            'Berat (Kg)',
            'Total Tarif (Rp)',
            'Metode Pembayaran',
            'Status Pembayaran',
            'Tgl Transaksi',
            'Status Pengiriman',
        ];
    }

    // Mapping tiap model transaksi menjadi satu baris Excel
    public function map(mixed $t): array
    {
        return [
            $t->id,
            $t->tracking_number,
            $t->customer?->name ?? $t->sender_name ?? '-',
            $t->courier?->name ?? 'Belum Ditugaskan',
            $t->service_type,
            $t->weight,
            $t->price,
            $t->payment_method ?? 'QRIS (Lunas)',
            'Lunas',
            $t->created_at ? $t->created_at->format('d/m/Y') : '-',
            $t->status,
        ];
    }

    // Mengatur lebar kolom agar rapi saat dibuka di Microsoft Excel
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 18,
            'C' => 22,
            'D' => 22,
            'E' => 18,
            'F' => 12,
            'G' => 18,
            'H' => 20,
            'I' => 18,
            'J' => 16,
            'K' => 18,
        ];
    }

    // Menebalkan huruf pada baris judul kolom pertama
    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
