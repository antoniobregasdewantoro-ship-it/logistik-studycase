<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Resi Pengiriman - {{ $order->tracking_number }}</title>
    <style>
        @page {
            size: 100mm 150mm;
            margin: 3mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #000;
            line-height: 1.25;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .outer-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }
        .border-bottom {
            border-bottom: 1.5px solid #000;
        }
        .border-right {
            border-right: 1.5px solid #000;
        }
        .bg-black {
            background-color: #000;
            color: #fff;
        }
        .bg-gray {
            background-color: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .uppercase {
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    @php
        $isCod = str_contains(strtoupper($order->payment_method ?? ''), 'COD');
        $paymentLabel = $isCod ? 'COD (BAYAR DI TEMPAT)' : 'NON-COD (LUNAS)';
        $paymentAmount = $isCod ? 'Rp ' . number_format($order->price, 0, ',', '.') : 'Rp 0';
        $senderPhone = $order->customer?->phone ?? '083112233445';
    @endphp

    <table class="outer-table" cellpadding="0" cellspacing="0">
        <!-- HEADER BRAND & SERVICE -->
        <tr class="border-bottom">
            <td style="padding: 6px; width: 65%; vertical-align: middle;" class="border-right">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 32px; vertical-align: middle;">
                            <div style="background-color: #0A1128; color: #FF6B00; width: 28px; height: 28px; text-align: center; line-height: 28px; font-weight: bold; font-size: 14px; border-radius: 4px;">
                                M
                            </div>
                        </td>
                        <td style="vertical-align: middle; padding-left: 6px;">
                            <div style="font-size: 13px; font-weight: bold; color: #0A1128;">
                                MERIDIAN <span style="color: #FF6B00;">EXPRESS</span>
                            </div>
                            <div style="font-size: 7.5px; color: #444; font-weight: bold; text-transform: uppercase;">
                                LOGISTICS & CARGO DELIVERY
                            </div>
                            <div style="font-size: 7px; color: #666;">
                                DROP POINT: {{ strtoupper($order->origin ?? 'HUB SURABAYA') }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 35%; vertical-align: top; text-align: center;">
                <div class="bg-black" style="padding: 5px; font-size: 11px; font-weight: bold; letter-spacing: 1px;">
                    {{ strtoupper($order->service_type ?? 'REGULER') }}
                </div>
                <div style="padding: 4px; font-size: 8px; font-weight: bold; background-color: {{ $isCod ? '#ffebee' : '#f5f5f5' }};">
                    {{ $paymentLabel }}<br>
                    @if($isCod)
                        <span style="font-size: 9.5px; color: #d32f2f;">{{ $paymentAmount }}</span>
                    @else
                        <span style="font-size: 7px; color: #2e7d32;">{{ $order->payment_method ?? 'QRIS' }}</span>
                    @endif
                </div>
            </td>
        </tr>

        <!-- ROUTING STRIP -->
        <tr class="border-bottom bg-gray">
            <td colspan="2" style="padding: 4px 6px; font-size: 9px; font-weight: bold;">
                <table style="width: 100%;">
                    <tr>
                        <td>
                            RUTE: {{ strtoupper($order->origin ?? 'SBY') }} ➔ <span style="font-size: 11px; font-weight: bold;">{{ strtoupper($order->destination ?? 'SUB') }}</span>
                        </td>
                        <td class="text-right" style="font-size: 8px;">
                            {{ $order->vehicle_type === 'pickup' ? 'LUAR KOTA (PICKUP)' : 'DALAM KOTA (SEPEDA)' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- BARCODE & RESI NUMBER -->
        <tr class="border-bottom">
            <td colspan="2" style="padding: 6px; text-align: center;">
                <img src="{{ $order->barcode_url }}" alt="Barcode" style="height: 38px; max-width: 90%;">
                <div style="font-family: monospace; font-size: 15px; font-weight: bold; letter-spacing: 2px; margin-top: 2px;">
                    {{ $order->tracking_number }}
                </div>
                <div style="font-size: 7.5px; color: #666;">
                    No. Resi Pengiriman Resmi • Kode ID: {{ $order->kode_barang }}
                </div>
            </td>
        </tr>

        <!-- PENERIMA -->
        <tr class="border-bottom">
            <td colspan="2" style="padding: 6px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="font-size: 8px; font-weight: bold; background: #000; color: #fff; padding: 1px 4px; width: 100px;">
                            KEPADA (PENERIMA):
                        </td>
                        <td class="text-right" style="font-size: 8px; font-weight: bold;">
                            Kota: {{ strtoupper($order->destination) }}
                        </td>
                    </tr>
                </table>
                <div style="font-size: 13px; font-weight: bold; margin-top: 3px;">
                    {{ $order->recipient_name }}
                    <span style="font-size: 11px; font-family: monospace; float: right;">{{ $order->recipient_phone ?? '-' }}</span>
                </div>
                <div style="font-size: 9.5px; margin-top: 3px; font-weight: bold;">
                    {{ $order->street_address ?? $order->destination }}
                </div>
                <div style="font-size: 8.5px; color: #333; margin-top: 1px;">
                    @if($order->rt_rw) RT/RW {{ $order->rt_rw }}, @endif
                    @if($order->village) Kel. {{ $order->village }}, @endif
                    @if($order->district) Kec. {{ $order->district }}, @endif
                    {{ $order->destination }} @if($order->postal_code) - {{ $order->postal_code }} @endif
                </div>
                <div style="margin-top: 4px; border: 1px solid #000; padding: 2px 4px; font-size: 9px; font-weight: bold; display: inline-block; background-color: #f5f5f5;">
                    KEC: {{ strtoupper($order->district ?? $order->destination) }} | KOTA: {{ strtoupper($order->destination) }}
                </div>
            </td>
        </tr>

        <!-- PENGIRIM -->
        <tr class="border-bottom" style="background-color: #fafafa;">
            <td colspan="2" style="padding: 5px 6px;">
                <div style="font-size: 7.5px; font-weight: bold; color: #444; text-transform: uppercase;">
                    DARI (PENGIRIM):
                </div>
                <div style="font-size: 10.5px; font-weight: bold; margin-top: 1px;">
                    {{ $order->sender_name ?? $order->customer?->name ?? 'Pelanggan Meridian' }}
                    <span style="font-size: 9.5px; font-family: monospace; float: right;">{{ $senderPhone }}</span>
                </div>
                <div style="font-size: 8px; color: #555; margin-top: 1px;">
                    Cabang Asal: {{ $order->origin ?? 'Pusat Logistik' }} • ID Cust #{{ $order->customer_id ?? 'GUEST' }}
                </div>
            </td>
        </tr>

        <!-- RINCIAN PAKET -->
        <tr class="border-bottom">
            <td colspan="2" style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse; font-size: 8.5px;">
                    <tr>
                        <td style="padding: 4px; width: 25%; border-right: 1px solid #000;" class="text-center">
                            <span style="font-size: 7px; color: #666; display: block;">BERAT</span>
                            <span class="bold">{{ number_format($order->weight, 2, ',', '.') }} Kg</span>
                        </td>
                        <td style="padding: 4px; width: 25%; border-right: 1px solid #000;" class="text-center">
                            <span style="font-size: 7px; color: #666; display: block;">ONGKIR</span>
                            <span class="bold">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                        </td>
                        <td style="padding: 4px; width: 25%; border-right: 1px solid #000;" class="text-center">
                            <span style="font-size: 7px; color: #666; display: block;">BIAYA COD</span>
                            <span class="bold">{{ $isCod ? $paymentAmount : 'Rp 0' }}</span>
                        </td>
                        <td style="padding: 4px; width: 25%;" class="text-center">
                            <span style="font-size: 7px; color: #666; display: block;">WAKTU</span>
                            <span style="font-size: 7.5px;">{{ $order->created_at ? $order->created_at->format('d/m/Y') : date('d/m/Y') }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- ISI & CATATAN -->
        <tr class="border-bottom">
            <td colspan="2" style="padding: 5px 6px; font-size: 8px;">
                <div>
                    <span class="bold">ISI BARANG:</span> {{ $order->package_detail ?? 'Paket Ekspedisi & Logistik' }}
                    <span style="float: right;">Est: {{ $order->estimated_arrival }}</span>
                </div>
                @if($order->notes)
                    <div style="color: #b71c1c; font-weight: bold; margin-top: 2px;">
                        * Catatan: {{ $order->notes }}
                    </div>
                @endif
            </td>
        </tr>

        <!-- INSTRUKSI KURIR -->
        <tr class="border-bottom" style="font-size: 7.5px; line-height: 1.2;">
            <td colspan="2" style="padding: 4px 6px;">
                <div class="bold" style="text-transform: uppercase;">INSTRUKSI OPERASIONAL KURIR:</div>
                <div>1. @if($isCod) WAJIB TAGIH COD {{ $paymentAmount }} sebelum paket diserahkan. @else PAKET LUNAS (NON-COD). DILARANG MENAGIH BIAYA KE PENERIMA. @endif</div>
                <div>2. Wajib ambil foto serah terima fisik paket (POD) di lokasi penerima.</div>
            </td>
        </tr>

        <!-- CUT LINE -->
        <tr style="background-color: #f9f9f9; text-align: center; font-size: 7px; color: #777;">
            <td colspan="2" style="padding: 2px; border-bottom: 1px dashed #000;">
                ✂ - - - - - - - - - - - - GUNTING DI SINI (BUKTI SERAH TERIMA) - - - - - - - - - - - -
            </td>
        </tr>

        <!-- BUKTI SERAH TERIMA KURIR / PARAF -->
        <tr>
            <td colspan="2" style="padding: 5px 6px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="vertical-align: top; font-size: 7.5px;">
                            <div class="bold" style="font-size: 8.5px;">BUKTI SERAH TERIMA (POD)</div>
                            <div>No. Resi: <b>{{ $order->tracking_number }}</b></div>
                            <div>Penerima: <b>{{ $order->recipient_name }}</b></div>
                            <div style="font-size: 7px; color: #666; margin-top: 2px;">
                                Paket diterima dalam kondisi baik & segel utuh.
                            </div>
                        </td>
                        <td style="width: 100px; text-align: center; border: 1px solid #000; padding: 3px; font-size: 7px;">
                            <div class="bold">Tanda Tangan Penerima</div>
                            <div style="height: 24px; border-bottom: 1px dotted #999;"></div>
                            <div style="color: #777; margin-top: 1px;">( ......................... )</div>
                            <div style="color: #999; font-size: 6px;">Tgl: ... / ... / 2026</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div style="margin-top: 3px; font-size: 6.5px; color: #666; text-align: center;">
        Meridian Express Logistics System • Cetak Resi Resmi • Call Center: 0800-1-MERIDIAN
    </div>
</body>
</html>
