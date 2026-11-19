<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Resi Pengiriman - {{ $order->tracking_number }} - Meridian Express</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Tailwind CSS (for toolbar and wrapper) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }

        /* PRINT STYLES UNTUK THERMAL PRINTER (100x150 mm / A6) */
        @media print {
            @page {
                size: 100mm 150mm;
                margin: 0;
            }
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .print-area {
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
                justify-content: center !important;
                align-items: flex-start !important;
            }
            .shopee-resi-container {
                border: 2px solid #000 !important;
                width: 100% !important;
                max-width: 100mm !important;
                box-shadow: none !important;
                margin: 0 auto !important;
            }
        }
    </style>
</head>
<body class="min-h-screen py-6 px-4">

    <!-- TOP TOOLBAR (HIDDEN IN PRINT) -->
    <div class="no-print max-w-xl mx-auto mb-6 bg-white rounded-2xl p-4 shadow-lg border border-slate-200">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('tracking', ['tracking' => $order->tracking_number]) }}"
                   class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition" title="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-sm font-extrabold text-[#0A1128]">Label Resi Pengiriman</h1>
                    <p class="text-[11px] text-slate-500 font-mono">{{ $order->tracking_number }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('resi.pdf', $order->tracking_number) }}" target="_blank"
                   class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-pdf text-rose-500"></i>
                    <span>PDF</span>
                </a>
                <button type="button" onclick="window.print()"
                        class="px-5 py-2 rounded-xl bg-[#0A1128] hover:bg-[#FF6B00] text-white font-extrabold text-xs shadow-md shadow-[#0A1128]/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-print text-[#FF6B00]"></i>
                    <span>Cetak Sekarang</span>
                </button>
            </div>
        </div>

        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
            <span><i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> Cocok untuk Printer Thermal 100x150 mm / A6 / Kertas Biasa</span>
            <span class="font-bold text-[#FF6B00]">Tekan Ctrl + P untuk Mencetak</span>
        </div>
    </div>

    <!-- PRINTABLE SHOPEE-STYLE RESI AREA -->
    <div class="print-area flex justify-center items-start">
        <div class="bg-white shadow-2xl rounded-sm">
            @include('partials.resi-thermal', ['order' => $order])
        </div>
    </div>

    <script>
        // Otomatis buka dialog print jika terdapat parameter ?autoprint=1 di URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint') === '1') {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            });
        }
    </script>
</body>
</html>
