<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Exports\OrdersTemplateExport;
use App\Imports\OrdersImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelController extends Controller
{
    /**
     * Export seluruh data pesanan ke file Excel (.xlsx) dengan formatting rapi.
     */
    public function exportOrders(): BinaryFileResponse
    {
        $fileName = 'Meridian_Manifest_Pesanan_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new OrdersExport, $fileName);
    }

    /**
     * Download template Excel kosong untuk import barang.
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $fileName = 'Template_Import_Barang_Meridian.xlsx';

        return Excel::download(new OrdersTemplateExport, $fileName);
    }

    /**
     * Import data pesanan dari file Excel template.
     * Tampilkan ringkasan jumlah barang berhasil dan baris yang dilewati.
     */
    public function importOrders(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'excel_file.required' => 'Pilih file Excel (.xlsx) yang ingin diimport.',
            'excel_file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new OrdersImport;

        try {
            Excel::import($import, $request->file('excel_file'));
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris '.$failure->row().': '.implode(', ', $failure->errors());
            }

            return redirect()->back()->with('error',
                'Gagal import — terdapat kesalahan validasi data: '.implode(' | ', array_slice($errorMessages, 0, 5))
            );
        }

        // Bangun pesan ringkasan
        $summary = "✅ Import selesai! {$import->importedCount} barang berhasil diimport.";

        if ($import->skippedCount > 0) {
            $summary .= " ⚠️ {$import->skippedCount} baris dilewati.";
            if (! empty($import->skippedRows)) {
                $summary .= ' Detail: '.implode(' | ', array_slice($import->skippedRows, 0, 5));
            }
        }

        return redirect()->back()->with('success', $summary);
    }
}
