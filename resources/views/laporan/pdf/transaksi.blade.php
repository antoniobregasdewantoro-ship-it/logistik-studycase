<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi & Armada - Meridian Logistics</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 12mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        .kop-header {
            width: 100%;
            border-bottom: 3px double #0A1128;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #0A1128;
            letter-spacing: 1px;
        }
        .company-tagline {
            font-size: 10px;
            font-weight: bold;
            color: #FF6B00;
            text-transform: uppercase;
        }
        .report-title-box {
            text-align: center;
            margin-bottom: 14px;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0A1128;
            margin: 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 10px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #0A1128;
            font-weight: bold;
            text-transform: uppercase;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .signature-table td {
            border: none;
            vertical-align: top;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="kop-header">
        <table class="kop-table">
            <tr>
                <td style="width: 70px;">
                    <div style="width: 48px; height: 48px; background-color: #0A1128; color: #FF6B00; text-align: center; line-height: 48px; font-size: 24px; font-weight: bold; border-radius: 8px;">
                        P
                    </div>
                </td>
                <td>
                    <div class="company-title">POLITEKNIK NEGERI JEMBER</div>
                    <div class="company-tagline">Integrated Logistics, Real-time Tracking & Fleet Management</div>
                    <div style="font-size: 9px; color: #475569;">Gedung Menara Meridian Lt. 12, Jl. Embong Malang No. 88, Surabaya</div>
                </td>
                <td style="text-align: right; width: 180px; font-size: 9px; color: #64748b;">
                    <div><strong>NO:</strong> MRD/TRX/{{ date('Ymd') }}</div>
                    <div><strong>TANGGAL:</strong> {{ date('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title-box">
        <h2 class="report-title">Laporan Transaksi Finansial & Pengantaran Logistik</h2>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 90px;">No. Resi</th>
                <th>Pengirim</th>
                <th>Penerima & Alamat</th>
                <th style="width: 80px;">Layanan</th>
                <th style="width: 60px; text-align: right;">Berat</th>
                <th style="width: 80px; text-align: right;">Pendapatan</th>
                <th style="width: 80px; text-align: center;">Status</th>
                <th style="width: 90px;">Petugas Kurir</th>
            </tr>
        </thead>
        <tbody>
            @php $totalRevenue = 0; @endphp
            @foreach($orders as $o)
                @php $totalRevenue += $o->price; @endphp
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td style="font-weight: bold; font-family: monospace;">{{ $o->tracking_number }}</td>
                    <td>{{ $o->sender_name ?? 'Pelanggan' }}</td>
                    <td>
                        <strong>{{ $o->recipient_name }}</strong><br>
                        <span style="font-size: 9px; color: #64748b;">{{ $o->full_address }}</span>
                    </td>
                    <td>{{ $o->service_type }}</td>
                    <td style="text-align: right;">{{ $o->weight }} kg</td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($o->price, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $o->status }}</td>
                    <td>{{ $o->courier?->name ?? 'Belum Ditugaskan' }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="6" style="text-align: right; text-transform: uppercase;">Total Penerimaan Ekspedisi:</td>
                <td style="text-align: right; color: #0A1128;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td style="width: 60%;">
                <p>Dokumen rekapitulasi sah dari modul Laporan Keuangan & Operasional Meridian.</p>
            </td>
            <td style="width: 40%; text-align: right;">
                <div style="float: right; text-align: center; width: 200px;">
                    <p>Surabaya, {{ date('d F Y') }}</p>
                    <p><strong>Kepala Operasional Keuangan,</strong></p>
                    <div style="height: 50px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">Hendrawan Pradana, S.E., Ak.</div>
                    <div style="font-size: 9px; color: #64748b;">Finance & Logistics Controller</div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
