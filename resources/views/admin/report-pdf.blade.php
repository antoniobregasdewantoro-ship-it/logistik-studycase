<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Ringkasan Armada & Pesanan - Meridian Logistics</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0A1128;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #0A1128;
            letter-spacing: 0.5px;
        }
        .brand-subtitle {
            font-size: 10px;
            font-weight: bold;
            color: #FF6B00;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-meta {
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }

        /* Summary Cards Table */
        .summary-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            border-radius: 6px;
            text-align: center;
        }
        .summary-label {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
        }
        .summary-val {
            font-size: 14px;
            font-weight: bold;
            color: #0A1128;
            margin-top: 2px;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #0A1128;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #0A1128;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-delivered { background-color: #dcfce7; color: #166534; }
        .badge-transit { background-color: #dbeafe; color: #1e40af; }
        .badge-verif { background-color: #fef3c7; color: #92400e; }
        .badge-high { background-color: #ffe4e6; color: #9f1239; }

        /* Signatures */
        .sign-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .sign-box {
            width: 30%;
            text-align: center;
        }
        .sign-line {
            margin-top: 50px;
            border-top: 1px solid #64748b;
            font-weight: bold;
            color: #0A1128;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- HEADER LAPORAN -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="brand-title">MERIDIAN LOGISTICS</div>
                <div class="brand-subtitle">Laporan Resmi Ringkasan Armada & Pesanan Manifest</div>
                <div style="font-size: 9px; color: #64748b; margin-top: 4px;">
                    Pusat Operasional: Surabaya Logistics Park & Jakarta Distribution Gateway
                </div>
            </td>
            <td class="report-meta" style="width: 40%;">
                <div><b>Tanggal Cetak:</b> {{ $stats['generated_at'] }}</div>
                <div><b>Pencetak:</b> {{ auth()->user()->name ?? 'Administrator' }}</div>
                <div><b>Status Dokumen:</b> <span style="color: #166534; font-weight: bold;">Terverifikasi Sah</span></div>
            </td>
        </tr>
    </table>

    <!-- RINGKASAN EKSEKUTIF (SUMMARY CARDS) -->
    <table class="summary-table">
        <tr>
            <td class="summary-card" style="width: 25%;">
                <div class="summary-label">Total Pesanan</div>
                <div class="summary-val">{{ $stats['total_orders'] }} Resi</div>
            </td>
            <td class="summary-card" style="width: 25%;">
                <div class="summary-label">Total Nilai Manifest</div>
                <div class="summary-val" style="color: #FF6B00;">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
            </td>
            <td class="summary-card" style="width: 25%;">
                <div class="summary-label">Armada Kurir Aktif</div>
                <div class="summary-val">{{ $stats['active_couriers'] }} Personel</div>
            </td>
            <td class="summary-card" style="width: 25%;">
                <div class="summary-label">Jaringan Hub Cabang</div>
                <div class="summary-val">{{ $stats['total_branches'] }} Kota</div>
            </td>
        </tr>
    </table>

    <!-- RINCIAN TABEL MANIFEST -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 14%;">No. Resi</th>
                <th style="width: 15%;">Pengirim & Penerima</th>
                <th style="width: 14%;">Rute Pengiriman</th>
                <th style="width: 10%;">Berat & Layanan</th>
                <th style="width: 11%;">Tarif Ongkir</th>
                <th style="width: 12%;">Kurir Armada</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Risiko Delay</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr>
                    <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold; color: #0A1128;">
                        {{ $order->tracking_number }}
                    </td>
                    <td>
                        <b>{{ $order->sender_name ?? 'Pelanggan' }}</b><br>
                        <span style="color: #64748b; font-size: 9px;">Ke: {{ $order->recipient_name ?? '-' }}</span>
                    </td>
                    <td>
                        <b>{{ $order->origin }}</b> &rarr; <b>{{ $order->destination }}</b>
                    </td>
                    <td>
                        {{ $order->weight }} Kg<br>
                        <span style="color: #64748b; font-size: 9px;">{{ $order->service_type }}</span>
                    </td>
                    <td style="font-weight: bold;">
                        Rp {{ number_format($order->price, 0, ',', '.') }}
                    </td>
                    <td>
                        {{ $order->courier?->name ?? 'Belum Ditugaskan' }}
                    </td>
                    <td>
                        @if($order->status === 'Terkirim')
                            <span class="badge badge-delivered">Terkirim</span>
                        @elseif($order->status === 'Dalam Perjalanan')
                            <span class="badge badge-transit">Jalan</span>
                        @else
                            <span class="badge badge-verif">Verifikasi</span>
                        @endif
                    </td>
                    <td>
                        @if($order->delay_risk === 'High')
                            <span class="badge badge-high">Tinggi ({{ $order->delay_probability }}%)</span>
                        @else
                            <span style="color: #166534; font-size: 9px; font-weight: bold;">Aman ({{ $order->delay_risk }})</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #94a3b8;">
                        Belum ada data manifest pesanan yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN TANDA TANGAN -->
    <table class="sign-table">
        <tr>
            <td class="sign-box">
                <div style="font-size: 9px; color: #64748b;">Dibuat Oleh Dispatcher,</div>
                <div class="sign-line">{{ auth()->user()->name ?? 'Administrator' }}</div>
                <div style="font-size: 8px; color: #64748b;">Divisi Operasional & Manifest</div>
            </td>
            <td style="width: 40%;"></td>
            <td class="sign-box">
                <div style="font-size: 9px; color: #64748b;">Mengetahui & Menyetujui,</div>
                <div class="sign-line">Head of Fleet & Logistics</div>
                <div style="font-size: 8px; color: #64748b;">PT Meridian Ekspres Indonesia</div>
            </td>
        </tr>
    </table>

</body>
</html>
