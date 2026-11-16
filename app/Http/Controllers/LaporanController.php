<?php

namespace App\Http\Controllers;

use App\Exports\BarangExport;
use App\Exports\TransaksiExport;
use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    // Method untuk export data barang / manifest pengiriman ke file Excel (.xlsx)
    public function exportExcelBarang()
    {
        return Excel::download(
            new BarangExport,
            'laporan-barang-'.date('Y-m-d').'.xlsx'
        );
    }

    // Method untuk export data transaksi & keuangan ke file Excel (.xlsx)
    public function exportExcelTransaksi()
    {
        return Excel::download(
            new TransaksiExport,
            'laporan-transaksi-'.date('Y-m-d').'.xlsx'
        );
    }

    // Method umum untuk export excel (mendukung route laporan.excel)
    public function exportExcel(Request $request)
    {
        return $this->exportExcelBarang();
    }

    // Method untuk cetak laporan PDF manifest pengiriman resmi (A4 Landscape)
    public function exportPdfManifest(Request $request): Response
    {
        $orders = Order::with(['customer', 'courier'])
            ->latest()
            ->get();

        $pdf = Pdf::loadView('laporan.pdf.manifest', compact('orders'))
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'laporan-manifest-'.date('Y-m-d').'.pdf';

        if ($request->query('action') === 'stream') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    // Method alias export PDF barang / manifest
    public function exportPdfBarang(Request $request): Response
    {
        return $this->exportPdfManifest($request);
    }

    // Method untuk cetak laporan PDF transaksi & performa armada kurir
    public function exportPdfTransaksi(Request $request): Response
    {
        $orders = Order::with(['customer', 'courier'])
            ->latest()
            ->get();
        $couriers = User::whereIn('role', ['courier', 'kurir'])->get();
        $branches = Branch::all();

        $pdf = Pdf::loadView('laporan.pdf.transaksi', compact('orders', 'couriers', 'branches'))
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'laporan-transaksi-'.date('Y-m-d').'.pdf';

        if ($request->query('action') === 'stream') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Upload File Laporan PDF Resmi untuk sebuah Pesanan / Manifest
     * Masuk dan tampil di tabel Daftar Manifest Pengiriman (Requirement #4)
     */
    public function uploadPdfReport(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:5120', // Maks 5MB PDF
            'report_notes' => 'nullable|string|max:255',
        ], [
            'pdf_file.required' => 'Wajib memilih file dokumen PDF.',
            'pdf_file.mimes' => 'File harus berformat PDF resmi (.pdf).',
            'pdf_file.max' => 'Ukuran file PDF maksimal 5MB.',
        ]);

        $order = Order::findOrFail($id);

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $fileName = 'laporan_manifest_'.$order->tracking_number.'_'.time().'.pdf';
            $path = $file->storeAs('reports', $fileName, 'public');

            $order->update([
                'pdf_report_path' => $path,
            ]);
        }

        return redirect()->back()->with('success', "Dokumen Laporan PDF untuk pesanan {$order->tracking_number} berhasil diupload dan ditampilkan di tabel manifest!");
    }
}
