<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Tampilan Cetak Resi Pengiriman Model Shopee (Thermal 100x150 mm / A6)
     */
    public function printResi(string $identifier): View
    {
        $order = Order::with(['courier', 'customer'])
            ->where('tracking_number', strtoupper(trim($identifier)))
            ->orWhere('id', is_numeric($identifier) ? (int) $identifier : 0)
            ->firstOrFail();

        return view('orders.print-resi', compact('order'));
    }

    /**
     * Download Berkas PDF Label Resi Pengiriman Resmi (Ukuran Thermal Label)
     */
    public function downloadResiPdf(string $identifier): Response
    {
        $order = Order::with(['courier', 'customer'])
            ->where('tracking_number', strtoupper(trim($identifier)))
            ->orWhere('id', is_numeric($identifier) ? (int) $identifier : 0)
            ->firstOrFail();

        // Ukuran 100mm x 150mm dalam points (1mm = 2.83465 pt) => 283.46 pt x 425.20 pt
        $pdf = Pdf::loadView('orders.pdf-resi', compact('order'))
            ->setPaper([0, 0, 283.46, 425.20], 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'Resi-'.$order->tracking_number.'.pdf';

        return $pdf->stream($filename);
    }
}
