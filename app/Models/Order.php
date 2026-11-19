<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
 * @property int $id
 * @property string $tracking_number
 * @property int|null $customer_id
 * @property int|null $courier_id
 * @property string|null $sender_name
 * @property string|null $recipient_name
 * @property string|null $recipient_phone
 * @property string|null $origin
 * @property string|null $destination
 * @property string|null $street_address
 * @property string|null $rt_rw
 * @property string|null $village
 * @property string|null $district
 * @property string|null $postal_code
 * @property float $weight
 * @property string $service_type
 * @property Carbon|null $pickup_time
 * @property float $price
 * @property string|null $payment_method
 * @property string|null $delay_risk
 * @property int|null $delay_probability
 * @property string $status
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $checkpoint
 * @property string|null $proof_image
 * @property string|null $notes
 * @property string|null $package_detail
 * @property string|null $initial_photo
 * @property string|null $qr_code_path
 * @property string|null $pdf_report_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $kode_barang
 * @property-read string $barcode_url
 * @property-read string $qr_code_url
 * @property-read string $qris_payload
 * @property-read string $full_address
 * @property-read string $status_color
 * @property-read string $risk_color
 * @property-read string $vehicle_type
 * @property-read string $vehicle_icon
 * @property-read string $vehicle_label
 * @property-read string $scope_label
 * @property-read string $estimated_arrival
 * @property-read User|null $customer
 * @property-read User|null $courier
 * @property-read Collection<int, Assignment> $assignments
 * @property-read Collection<int, Review> $reviews
 * @property-read Review|null $review
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'customer_id',
        'courier_id',
        'sender_name',
        'recipient_name',
        'recipient_phone',
        'origin',
        'destination',
        'street_address',
        'rt_rw',
        'village',
        'district',
        'postal_code',
        'weight',
        'service_type',
        'pickup_time',
        'price',
        'payment_method',
        'delay_risk',
        'delay_probability',
        'status',
        'latitude',
        'longitude',
        'checkpoint',
        'proof_image',
        'notes',
        'package_detail',
        'initial_photo',
        'qr_code_path',
        'pdf_report_path',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'price' => 'decimal:2',
            'latitude' => 'float',
            'longitude' => 'float',
            'pickup_time' => 'datetime',
            'delay_probability' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class)->latestOfMany();
    }

    /**
     * Get Proof Image URL safely
     */
    public function getProofImageUrlAttribute(): ?string
    {
        if (! $this->proof_image) {
            return null;
        }

        if (str_starts_with($this->proof_image, 'http://') || str_starts_with($this->proof_image, 'https://')) {
            return $this->proof_image;
        }

        return asset('storage/'.$this->proof_image);
    }

    /**
     * Get Initial Package Photo URL (Foto kondisi barang sebelum dikirim)
     */
    public function getInitialPhotoUrlAttribute(): ?string
    {
        if (! $this->initial_photo) {
            return null;
        }

        if (str_starts_with($this->initial_photo, 'http://') || str_starts_with($this->initial_photo, 'https://')) {
            return $this->initial_photo;
        }

        return asset('storage/'.$this->initial_photo);
    }

    /**
     * Get PDF Report URL safely
     */
    public function getPdfReportUrlAttribute(): ?string
    {
        if (! $this->pdf_report_path) {
            return null;
        }

        if (str_starts_with($this->pdf_report_path, 'http://') || str_starts_with($this->pdf_report_path, 'https://')) {
            return $this->pdf_report_path;
        }

        return asset('storage/'.$this->pdf_report_path);
    }

    /**
     * Get formatted full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street_address,
            $this->rt_rw ? 'RT/RW '.$this->rt_rw : null,
            $this->village ? 'Desa/Kel. '.$this->village : null,
            $this->district ? 'Kec. '.$this->district : null,
            $this->destination,
            $this->postal_code,
        ]);

        return ! empty($parts) ? implode(', ', $parts) : ($this->destination ?? '-');
    }

    /**
     * Accessor: Kode Barang dari kolom ID
     * Format: INV-00001 (5 digit angka ber-padding nol)
     */
    public function getKodeBarangAttribute(): string
    {
        return 'INV-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Method: Barcode 1D Code 39 HTML menggunakan Milon Barcode DNS1D
     * Output berupa string HTML murni (elemen div hitam) tanpa memerlukan file fisik / ext-gd.
     * Digunakan khusus untuk Barcode Identitas Barang / Resi Pengiriman.
     *
     * @param  int  $width  Ketebalan tiap garis vertikal (default: 3)
     * @param  int  $height  Panjang garis vertikal dari atas ke bawah (default: 100)
     */
    public function barcodeHtml(int $width = 10, int $height = 100): string
    {
        return DNS1D::getBarcodeHTML($this->kode_barang, 'C39', $width, $height);
    }

    /**
     * Method: Barcode 1D Code 39 HTML khusus nomor resi pengiriman (tracking_number)
     */
    public function barcodeResiHtml(int $width = 2, int $height = 50): string
    {
        $code = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string) ($this->tracking_number ?: $this->kode_barang)));

        return DNS1D::getBarcodeHTML($code, 'C39', $width, $height);
    }

    /**
     * Accessor: Payload QRIS standar BI untuk verifikasi pembayaran
     */
    public function getQrisPayloadAttribute(): string
    {
        return '00020101021226670016ID.CO.MERIDIAN.WWW0118936009990000000001520454995303360540'.str_pad((string) ($this->id ?? 1), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Method: QR Code 2D HTML menggunakan Milon Barcode DNS2D (Slide 13)
     * Digunakan KHUSUS untuk Metode Pembayaran (QRIS) & Verifikasi Digital.
     *
     * @param  int  $width  Ukuran bujur sangkar (default: 5)
     * @param  int  $height  Ukuran bujur sangkar (default: 5)
     */
    public function qrisHtml(int $width = 5, int $height = 5): string
    {
        return DNS2D::getBarcodeHTML($this->qris_payload, 'QRCODE', $width, $height);
    }

    /**
     * Method: QR Code 2D HTML umum menggunakan Milon Barcode DNS2D
     */
    public function qrCodeHtml(?string $data = null, int $width = 5, int $height = 5): string
    {
        $payload = $data ?: url('/lacak?tracking='.$this->tracking_number);

        return DNS2D::getBarcodeHTML($payload, 'QRCODE', $width, $height);
    }

    /**
     * Get Barcode 1D (Code 39 / Code 128) khusus untuk Nomor Resi Pengiriman Paket
     * Digunakan pada label thermal resi pengiriman, scanner barcode kurir, dan tabel pesanan.
     */
    public function getBarcodeUrlAttribute(): string
    {
        $code = strtoupper(preg_replace('/[^A-Z0-9\-]/i', '', (string) ($this->tracking_number ?: $this->kode_barang)));
        if (empty($code)) {
            $code = 'MRD-00000';
        }

        try {
            if (class_exists(DNS1D::class)) {
                $png = DNS1D::getBarcodePNG($code, 'C39', 2, 45);
                if ($png) {
                    return 'data:image/png;base64,'.$png;
                }
            }
            if (class_exists(BarcodeGeneratorPNG::class)) {
                $generator = new BarcodeGeneratorPNG;
                $barcode = $generator->getBarcode($code, BarcodeGeneratorPNG::TYPE_CODE_128, 2, 45);

                return 'data:image/png;base64,'.base64_encode($barcode);
            }
        } catch (\Throwable) {
            // Fallback ke generator online jika library terkendala
        }

        return 'https://bwipjs-api.metafloor.com/?bcid=code39&text='.urlencode($code).'&scale=2&height=12&includetext';
    }

    /**
     * Get QR Code 2D khusus untuk Metode Pembayaran (QRIS) & Verifikasi Digital
     * Berupa matriks 2 dimensi standar BI untuk scan pembayaran dompet digital / m-banking.
     */
    public function getQrCodeUrlAttribute(): string
    {
        $data = urlencode(url('/lacak?tracking='.$this->tracking_number));

        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$data}";
    }

    /**
     * Get QR Code 2D Base64 Data PNG untuk Cetak Label Thermal Resi
     */
    public function getQrCodeBase64Attribute(): string
    {
        try {
            if (class_exists(DNS2D::class)) {
                $payload = url('/lacak?tracking='.$this->tracking_number);
                $png = DNS2D::getBarcodePNG($payload, 'QRCODE', 4, 4);
                if ($png) {
                    return 'data:image/png;base64,'.$png;
                }
            }
        } catch (\Throwable) {
        }

        return $this->qr_code_url;
    }

    /**
     * Helper for Status Badge styling
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Terkirim' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'Dalam Perjalanan' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Verifikasi', 'Verifikasi Manifest' => 'bg-amber-100 text-amber-800 border-amber-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    /**
     * Helper for Delay Risk Badge styling
     */
    public function getRiskColorAttribute(): string
    {
        return match ($this->delay_risk) {
            'Low' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Medium' => 'bg-amber-50 text-amber-700 border-amber-200',
            'High' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    /**
     * Menentukan jenis armada berdasarkan cakupan pengiriman:
     * - Luar Kota (antar kota atau kargo) = 'pickup' (Mobil Pickup)
     * - Di Kecamatan (sesama kota / lokal) = 'sepeda' (Sepeda)
     */
    public function getVehicleTypeAttribute(): string
    {
        $originStr = (string) ($this->origin ?? '');
        $destStr = (string) ($this->destination ?? '');

        $originCity = strtolower(trim(explode(',', $originStr)[0] ?? $originStr));
        $destCity = strtolower(trim(explode(',', $destStr)[0] ?? $destStr));

        $originClean = trim(str_replace(['cabang hub', 'hub', 'depot', 'pusat'], '', $originCity));
        $destClean = trim(str_replace(['cabang hub', 'hub', 'depot', 'pusat'], '', $destCity));

        if ($this->service_type === 'Kargo' || ($originClean !== '' && $destClean !== '' && ! str_contains($destClean, $originClean) && ! str_contains($originClean, $destClean))) {
            return 'pickup';
        }

        return 'sepeda';
    }

    /**
     * Ikon FontAwesome untuk armada pengiriman
     */
    public function getVehicleIconAttribute(): string
    {
        return $this->vehicle_type === 'pickup' ? 'fa-solid fa-truck-pickup' : 'fa-solid fa-bicycle';
    }

    /**
     * Label armada pengiriman
     */
    public function getVehicleLabelAttribute(): string
    {
        return $this->vehicle_type === 'pickup' ? 'Mobil Pickup (Luar Kota)' : 'Sepeda (Di Kecamatan)';
    }

    /**
     * Label cakupan pengiriman
     */
    public function getScopeLabelAttribute(): string
    {
        return $this->vehicle_type === 'pickup' ? 'Luar Kota' : 'Di Kecamatan';
    }

    /**
     * Perkiraan estimasi waktu paket sampai di tujuan
     */
    public function getEstimatedArrivalAttribute(): string
    {
        if ($this->status === 'Terkirim') {
            $updated = $this->updated_at ? Carbon::parse($this->updated_at) : now();

            return 'Terkirim pada '.$updated->isoFormat('D MMM Y, HH:mm').' WIB';
        }

        $daysToAdd = match ($this->service_type) {
            'Same Day' => 0,
            'Express' => 1,
            'Kargo' => $this->vehicle_type === 'pickup' ? 4 : 2,
            default => 2,
        };

        $baseDate = $this->pickup_time ? Carbon::parse($this->pickup_time) : ($this->created_at ? Carbon::parse($this->created_at) : now());
        $targetDate = $baseDate->copy()->addDays($daysToAdd);

        if ($this->service_type === 'Same Day') {
            return $baseDate->isoFormat('dddd, D MMM Y').' (Hari Yang Sama)';
        }

        $range = match ($this->service_type) {
            'Express' => '1 - 2 Hari',
            'Kargo' => $this->vehicle_type === 'pickup' ? '3 - 5 Hari' : '2 - 3 Hari',
            default => '2 - 3 Hari',
        };

        return $targetDate->isoFormat('dddd, D MMM Y')." (Estimasi {$range})";
    }
}
