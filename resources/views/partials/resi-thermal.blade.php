@php
$isCod = str_contains(strtoupper($order->payment_method ?? ''), 'COD');
$paymentLabel = $isCod ? 'COD (BAYAR DI TEMPAT)' : 'NON-COD (LUNAS)';
$paymentAmount = $isCod ? 'Rp ' . number_format($order->price, 0, ',', '.') : 'Rp 0';
$senderPhone = $order->customer?->phone ?? '083112233445';
@endphp

<div class="shopee-resi-container"
    style="background:#fff; color:#000; font-family:'Helvetica Neue', Arial, sans-serif; width:100%; max-width:390px; margin:0 auto; border:2.5px solid #000; box-sizing:border-box; font-size:11px; line-height:1.25; text-align:left;">

    <!-- 1. HEADER BRAND & LOGO PERUSAHAAN (SHOPEE STYLE) -->
    <div style="display:flex; justify-content:space-between; align-items:stretch; border-bottom:2px solid #000;">
        <!-- Logo Perusahaan Pengirim Paket -->
        <div style="padding:8px 10px; display:flex; align-items:center; gap:8px; border-right:2px solid #000; flex:1;">
            <div
                style="width:38px; height:38px; background:#0A1128; border-radius:6px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="#FF6B00" />
                    <path d="M2 17L12 22L22 17" stroke="#FF6B00" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M2 12L12 17L22 12" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <div style="font-weight:900; font-size:14px; letter-spacing:0.5px; color:#0A1128; line-height:1;">
                    MERIDIAN<span style="color:#FF6B00;">EXPRESS</span>
                </div>
                <div
                    style="font-size:8px; font-weight:700; color:#333; text-transform:uppercase; letter-spacing:0.8px; margin-top:2px;">
                    LOGISTICS & CARGO
                </div>
                <div style="font-size:7.5px; color:#666; margin-top:1px;">
                    DROP POINT: {{ strtoupper($order->origin ?? 'HUB SURABAYA') }}
                </div>
            </div>
        </div>

        <!-- Badges: Layanan & COD Status -->
        <div style="width:130px; display:flex; flex-direction:column; text-align:center;">
            <!-- Service T ype Badge -->
            <div
                style="background:#000; color:#fff; font-weight:900; font-size:13px; padding:6px 4px; text-transform:uppercase; letter-spacing:1px; border-bottom:1.5px solid #000;">
                {{ $order->service_type ?? 'REGULER' }}
            </div>
            <!-- Payment Badge -->
            <div
                style="background:{{ $isCod ? '#ffebee' : '#f5f5f5' }}; color:#000; font-weight:800; font-size:9.5px; padding:5px 4px; flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center;">
                <span>{{ $paymentLabel }}</span>
                @if($isCod)
                <span
                    style="font-size:11px; font-weight:900; color:#d32f2f; margin-top:1px;">{{ $paymentAmount }}</span>
                @else
                <span style="font-size:8px; font-weight:700; color:#2e7d32; margin-top:1px;">LUNAS
                    ({{ $order->payment_method ?? 'QRIS' }})</span>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. ROUTING HUB & KOTA BESAR -->
    <div
        style="display:flex; justify-content:space-between; align-items:center; background:#f0f0f0; padding:4px 10px; border-bottom:2px solid #000; font-size:10px; font-weight:800;">
        <div>
            RUTE: <span style="font-size:11px;">{{ strtoupper($order->origin ?? 'SBY') }}</span> ➔ <span
                style="font-size:12px; font-weight:900;">{{ strtoupper($order->destination ?? 'SUB') }}</span>
        </div>
        <div
            style="background:#000; color:#fff; padding:2px 6px; border-radius:3px; font-size:9px; font-weight:900; letter-spacing:0.5px;">
            {{ $order->vehicle_type === 'pickup' ? 'LUAR KOTA (PICKUP)' : 'DALAM KOTA (SEPEDA)' }}
        </div>
    </div>

    <!-- 3. BARCODE RESI & QR CODE SECTION -->
    <div
        style="padding:8px 10px; border-bottom:2px solid #000; display:flex; align-items:center; justify-content:space-between; gap:10px; background:#fff;">
        <!-- Left: Barcode 1D + Tracking Number text -->
        <div style="flex:1; text-align:center;">
            <div style="display:flex; justify-content:center; margin-bottom:4px;">
                <img src="{{ $order->barcode_url }}" alt="Barcode Resi {{ $order->tracking_number }}"
                    style="max-width:100%; height:44px; object-fit:contain; image-rendering:pixelated;">
            </div>
            <div
                style="font-family:'Courier New', Courier, monospace; font-size:16px; font-weight:900; letter-spacing:2px; color:#000; line-height:1;">
                {{ $order->tracking_number }}
            </div>
            <div style="font-size:8px; color:#555; margin-top:3px; font-weight:700;">
                No. Resi Pengiriman Resmi • ID: {{ $order->kode_barang }}
            </div>
        </div>

        <!-- Right: QR Code Live Tracking -->
        <div style="width:64px; text-align:center; flex-shrink:0; border-left:1px dashed #ccc; padding-left:8px;">
            <img src="{{ $order->qr_code_base64 }}" alt="QR Code Resi"
                style="width:56px; height:56px; display:block; margin:0 auto; border:1px solid #ddd; padding:2px;">
            <div style="font-size:7px; font-weight:700; color:#444; margin-top:2px;">SCAN LACAK</div>
        </div>
    </div>

    <!-- 4. INFORMASI PENERIMA & PENGIRIM (SHOPEE STYLE DUA KOLOM / BLOK BESAR) -->
    <!-- PENERIMA (BLOK UTAMA LEBIH BESAR & JELAS) -->
    <div style="border-bottom:1.5px solid #000; padding:8px 10px; background:#fff;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <span
                style="font-size:9px; font-weight:900; background:#000; color:#fff; padding:1px 6px; border-radius:2px; text-transform:uppercase; letter-spacing:0.5px;">
                KEPADA / PENERIMA:
            </span>
            <span style="font-size:8px; font-weight:700; color:#555;">
                Kota Tujuan: <b style="color:#000;">{{ strtoupper($order->destination) }}</b>
            </span>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:baseline;">
            <div style="font-size:14px; font-weight:900; color:#000; letter-spacing:0.3px;">
                {{ $order->recipient_name }}
            </div>
            <div style="font-size:12px; font-weight:800; font-family:monospace; color:#000;">
                {{ $order->recipient_phone ?? '-' }}
            </div>
        </div>

        <!-- Alamat Lengkap Tujuan -->
        <div style="margin-top:4px; font-size:10.5px; line-height:1.35; color:#111;">
            <div style="font-weight:700;">
                {{ $order->street_address ?? $order->destination }}
            </div>
            @if($order->rt_rw || $order->village || $order->district || $order->postal_code)
            <div style="font-size:9.5px; color:#333; margin-top:2px;">
                @if($order->rt_rw) RT/RW {{ $order->rt_rw }}, @endif
                @if($order->village) Kel. {{ $order->village }}, @endif
                @if($order->district) Kec. {{ $order->district }}, @endif
                {{ $order->destination }}
                @if($order->postal_code) - <b>{{ $order->postal_code }}</b> @endif
            </div>
            @else
            <div style="font-size:9.5px; color:#333; margin-top:1px;">
                Tujuan Pengiriman: {{ $order->full_address }}
            </div>
            @endif
        </div>

        <!-- HIGHLIGHT KECAMATAN & KOTA TUJUAN (KOTAK KHAS RESI SHOPEE) -->
        <div
            style="margin-top:6px; padding:3px 6px; background:#f0f0f0; border:1px solid #000; font-weight:900; font-size:10px; display:inline-block; border-radius:3px;">
            KEC: {{ strtoupper($order->district ?? $order->destination) }} | KOTA: {{ strtoupper($order->destination) }}
        </div>
    </div>

    <!-- PENGIRIM (DARI) -->
    <div style="border-bottom:2px solid #000; padding:6px 10px; background:#fafafa;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2px;">
            <span style="font-size:8.5px; font-weight:800; color:#444; text-transform:uppercase;">
                DARI (PENGIRIM):
            </span>
            <span style="font-size:8.5px; font-weight:700; color:#555;">
                Asal: <b>{{ $order->origin ?? '-' }}</b>
            </span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:baseline;">
            <span style="font-size:11.5px; font-weight:800; color:#000;">
                {{ $order->sender_name ?? $order->customer?->name ?? 'Pelanggan Meridian' }}
            </span>
            <span style="font-size:10.5px; font-weight:700; font-family:monospace; color:#333;">
                {{ $senderPhone }}
            </span>
        </div>
        <div style="font-size:9px; color:#666; margin-top:1px;">
            Cabang Asal: {{ $order->origin ?? 'Pusat Logistik' }} • Akun Pelanggan #{{ $order->customer_id ?? 'GUEST' }}
        </div>
    </div>

    <!-- 5. DETAIL SPESIFIKASI PAKET (BERAT, ONGKIR, TANGGAL, ISI BARANG) -->
    <div style="border-bottom:2px solid #000; background:#fff;">
        <table style="width:100%; border-collapse:collapse; font-size:9.5px;">
            <tr style="border-bottom:1px solid #000;">
                <td style="padding:4px 8px; border-right:1px solid #000; width:25%;">
                    <span style="font-size:7.5px; color:#555; display:block; font-weight:700;">BERAT</span>
                    <span style="font-weight:900; font-size:11px;">{{ number_format($order->weight, 2, ',', '.') }}
                        Kg</span>
                </td>
                <td style="padding:4px 8px; border-right:1px solid #000; width:25%;">
                    <span style="font-size:7.5px; color:#555; display:block; font-weight:700;">ONGKIR</span>
                    <span style="font-weight:900; font-size:11px;">Rp
                        {{ number_format($order->price, 0, ',', '.') }}</span>
                </td>
                <td style="padding:4px 8px; border-right:1px solid #000; width:25%;">
                    <span style="font-size:7.5px; color:#555; display:block; font-weight:700;">BIAYA COD</span>
                    <span style="font-weight:900; font-size:11px;">{{ $isCod ? $paymentAmount : 'Rp 0' }}</span>
                </td>
                <td style="padding:4px 8px; width:25%;">
                    <span style="font-size:7.5px; color:#555; display:block; font-weight:700;">WAKTU ORDER</span>
                    <span
                        style="font-weight:700; font-size:9px;">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding:5px 8px;">
                    <div style="display:flex; justify-content:space-between; align-items:baseline;">
                        <div>
                            <span style="font-size:8px; font-weight:800; color:#555;">ISI BARANG: </span>
                            <span style="font-weight:800; font-size:10px; color:#000;">
                                {{ $order->package_detail ?? 'Paket Ekspedisi & Logistik' }}
                            </span>
                        </div>
                        <div style="font-size:8px; font-weight:700; color:#555;">
                            Est. Sampai: <b style="color:#000;">{{ $order->estimated_arrival }}</b>
                        </div>
                    </div>
                    @if($order->notes)
                    <div style="font-size:8.5px; color:#b71c1c; font-weight:700; margin-top:2px; font-style:italic;">
                        * Catatan: {{ $order->notes }}
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- 6. INSTRUKSI KURIR & KETENTUAN PENGIRIMAN -->
    <div
        style="padding:5px 10px; background:#fff; font-size:8px; color:#444; border-bottom:1.5px dashed #000; line-height:1.3;">
        <div style="font-weight:800; color:#000; text-transform:uppercase; margin-bottom:1px;">
            ⚠️ INSTRUKSI OPERASIONAL KURIR:
        </div>
        <div>
            1. @if($isCod) <b>WAJIB TAGIH COD SEBESAR {{ $paymentAmount }}</b> sebelum menyerahkan paket. @else <b>PAKET
                SUDAH LUNAS (NON-COD)</b>. Kurir TIDAK BOLEH menagih biaya apapun kepada penerima. @endif
        </div>
        <div>
            2. Wajib ambil foto serah terima fisik paket (POD) bersama penerima di lokasi tujuan.
        </div>
    </div>

    <!-- 7. GARIS POTONG / CUT LINE (SHOPEE STYLE LEMBAR SERAH TERIMA) -->
    <div
        style="padding:2px 8px; background:#f9f9f9; text-align:center; font-size:7.5px; color:#777; font-weight:700; border-bottom:1.5px dashed #000; letter-spacing:1px;">
        ✂ - - - - - - - - - - - - - GUNTING DI SINI (BUKTI SERAH TERIMA KURIR) - - - - - - - - - - - - -
    </div>

    <!-- 8. SLIP BUKTI SERAH TERIMA PENERIMA (BAGIAN BAWAH RESI SHOPEE) -->
    <div style="padding:6px 10px; background:#fff; display:flex; justify-content:space-between; align-items:center;">
        <div style="flex:1; font-size:8px; line-height:1.3;">
            <div style="font-weight:900; font-size:9px; color:#000;">
                BUKTI SERAH TERIMA (POD)
            </div>
            <div>No. Resi: <b>{{ $order->tracking_number }}</b></div>
            <div>Penerima: <b>{{ $order->recipient_name }}</b></div>
            <div style="font-size:7.5px; color:#666; margin-top:1px;">
                Paket diterima dalam kondisi baik & segel utuh.
            </div>
        </div>

        <!-- Kolom Tanda Tangan Penerima -->
        <div
            style="width:120px; border:1px solid #000; padding:4px 6px; text-align:center; border-radius:3px; background:#fafafa;">
            <div style="font-size:7.5px; font-weight:800; color:#333; text-transform:uppercase;">
                Tanda Tangan Penerima
            </div>
            <div style="height:32px; margin:2px 0; border-bottom:1px dotted #999;"></div>
            <div style="font-size:7px; color:#777;">
                ( .................................... )
            </div>
            <div style="font-size:6.5px; color:#999; margin-top:1px;">
                Tgl: ..... / ..... / 2026
            </div>
        </div>
    </div>

    <!-- 9. FOOTER RESI RESMI -->
    <div
        style="background:#0A1128; color:#fff; padding:3px 10px; display:flex; justify-content:space-between; align-items:center; font-size:7px; font-weight:700;">
        <span>Meridian Express Logistics System • Cetak Resi Resmi</span>
        <span>Call Center: 0800-1-MERIDIAN</span>
    </div>

</div>