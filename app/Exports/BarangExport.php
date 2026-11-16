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

class BarangExport implements Export, FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    // Menentukan data yang diexport dengan eager loading relasi untuk mencegah N+1 query
    public function collection(): Enumerable
    {
        return Order::with(['customer', 'courier'])->latest()->get();
    }

    // Judul kolom pada baris pertama di file Excel
    public function headings(): array
    {
        return [
            'ID',
            'No. Resi',
            'Pengirim',
            'Penerima',
            'No. Telp Penerima',
            'Rute Asal',
            'Rute Tujuan',
            'Alamat Lengkap',
            'Detail Barang',
            'Layanan',
            'Berat (Kg)',
            'Tarif (Rp)',
            'Metode Pembayaran',
            'Kurir',
            'Status',
            'Tanggal',
        ];
    }

    // Satu model diubah menjadi satu baris file Excel
    public function map(mixed $barang): array
    {
        return [
            $barang->id,
            $barang->tracking_number,
            $barang->sender_name ?? '-',
            $barang->recipient_name ?? '-',
            $barang->recipient_phone ?? '-',
            $barang->origin,
            $barang->destination,
            $barang->full_address ?? '-',
            $barang->package_detail ?? 'Paket Standar',
            $barang->service_type,
            $barang->weight,
            $barang->price,
            $barang->payment_method ?? 'QRIS (Lunas)',
            $barang->courier?->name ?? 'Belum Ditugaskan',
            $barang->status,
            $barang->created_at ? $barang->created_at->format('d/m/Y') : '-',
        ];
    }

    // Mengatur lebar kolom agar rapi saat dibuka di Microsoft Excel
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 18,
            'C' => 20,
            'D' => 20,
            'E' => 18,
            'F' => 15,
            'G' => 15,
            'H' => 30,
            'I' => 25,
            'J' => 15,
            'K' => 12,
            'L' => 16,
            'M' => 18,
            'N' => 22,
            'O' => 18,
            'P' => 15,
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
