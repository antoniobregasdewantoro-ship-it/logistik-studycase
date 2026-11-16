<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Manifest Pengiriman - Meridian Logistics</title>
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
        /* KOP SURAT FORMAL */
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
        .company-address {
            font-size: 9px;
            color: #475569;
            margin-top: 3px;
        }
        /* JUDUL LAPORAN */
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
        .report-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        /* TABEL DATA */
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
            text-align: left;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-delivered { background-color: #d1fae5; color: #065f46; }
        .badge-transit { background-color: #dbeafe; color: #1e40af; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        /* TANDA TANGAN & CAP */
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
        .signature-box {
            text-align: center;
            width: 220px;
        }
        .stamp-box {
            height: 60px;
            margin: 6px 0;
            color: #cbd5e1;
            font-style: italic;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0A1128;
        }
        .signature-role {
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT FORMAL -->
    <div class="kop-header">
        <table class="kop-table">
            <tr>
                <td style="width: 70px;">
                    <div style="width: 48px; height: 48px; background-color: #0A1128; color: #FF6B00; text-align: center; line-height: 48px; font-size: 24px; font-weight: bold; border-radius: 8px;">
                        M
                    </div>
                </td>
                <td>
                    <div class="company-title">PT MERIDIAN LOGISTIK INDONESIA</div>
                    <div class="company-tagline">Integrated Logistics, Real-time Tracking & Fleet Management</div>
                    <div class="company-address">
                        Gedung Menara Meridian Lt. 12, Jl. Embong Malang No. 88, Surabaya • Telp: (031) 8899-7700 • Email: ops@meridianlogistics.id
                    </div>
                </td>
                <td style="text-align: right; width: 180px;">
                    <div style="font-size: 9px; color: #64748b;">
                        <div><strong>NO. DOKUMEN:</strong> MRD/MF/{{ date('Y/m/d') }}</div>
                        <div><strong>TANGGAL CETAK:</strong> {{ date('d/m/Y H:i') }} WIB</div>
                        <div><strong>STATUS:</strong> RESMI / TERARSIP</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- JUDUL LAPORAN -->
    <div class="report-title-box">
        <h2 class="report-title">Laporan Rekapitulasi Manifest & Pengiriman Armada</h2>
        <div class="report-subtitle">Daftar Manifest Aktif dan Terverifikasi Seluruh Kantor Cabang & Hub Logistik</div>
    </div>

    <!-- TABEL DATA -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 85px;">No. Resi</th>
                <th style="width: 95px;">Pengirim</th>
                <th style="width: 100px;">Penerima</th>
                <th>Alamat Lengkap Tujuan</th>
                <th style="width: 70px;">Rute</th>
                <th style="width: 50px; text-align: right;">Berat</th>
                <th style="width: 75px; text-align: right;">Tarif Ongkir</th>
                <th style="width: 80px; text-align: center;">Status</th>
                <th style="width: 85px;">Kurir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $o)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td style="font-weight: bold; font-family: monospace;">{{ $o->tracking_number }}</td>
                    <td>{{ $o->sender_name ?? 'Pelanggan Umum' }}</td>
                    <td>
                        <strong>{{ $o->recipient_name ?? '-' }}</strong><br>
                        <span style="font-size: 8.5px; color: #64748b;">{{ $o->recipient_phone ?? '-' }}</span>
                    </td>
                    <td>
                        {{ $o->full_address ?? $o->destination }}
                    </td>
                    <td>{{ $o->origin }} → {{ $o->destination }}</td>
                    <td style="text-align: right;">{{ $o->weight }} kg</td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($o->price, 0, ',', '.') }}</td>
                    <td style="text-align: center;">
                        @if($o->status === 'Terkirim')
                            <span class="badge badge-delivered">Terkirim</span>
                        @elseif($o->status === 'Dalam Perjalanan')
                            <span class="badge badge-transit">Diantar</span>
                        @else
                            <span class="badge badge-pending">Verifikasi</span>
                        @endif
                    </td>
                    <td>{{ $o->courier?->name ?? 'Belum Ditugaskan' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #94a3b8; padding: 20px;">
                        Belum ada data manifest pengiriman yang tercatat dalam sistem.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN FORMAL & CAP STEMPEL -->
    <table class="signature-table">
        <tr>
            <td style="width: 60%;">
                <div style="font-size: 9px; color: #64748b;">
                    <p><strong>Catatan Dokumen:</strong></p>
                    <p>1. Dokumen ini adalah arsip manifest resmi yang dicetak secara otomatis dari sistem ERP Meridian.</p>
                    <p>2. Bukti serah terima dan foto dokumentasi digital tersimpan secara sah pada server basis data.</p>
                </div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="signature-box" style="float: right;">
                    <p>Surabaya, {{ date('d F Y') }}</p>
                    <p><strong>Mengetahui / Mengesahkan,</strong></p>
                    <div class="stamp-box">
                        <span style="border: 2px solid #0A1128; padding: 4px 12px; border-radius: 4px; color: #0A1128; font-size: 8px; font-weight: bold; text-transform: uppercase;">
                            STEMPEL TERVERIFIKASI SISTEM
                        </span>
                    </div>
                    <div class="signature-name">Nadia Kessler, S.T., M.M.</div>
                    <div class="signature-role">Direktur Operasional Logistik</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
