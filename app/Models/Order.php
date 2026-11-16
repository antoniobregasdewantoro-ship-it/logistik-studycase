<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
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
     * Get Barcode 1D (Code 128) khusus untuk Nomor Resi Pengiriman Paket
     * Digunakan pada label thermal resi pengiriman, scanner barcode kurir, dan tabel pesanan.
     */
    public function getBarcodeUrlAttribute(): string
    {
        $code = trim((string) ($this->tracking_number ?: 'MRD-00000'));

        try {
            if (class_exists(BarcodeGeneratorPNG::class)) {
                $generator = new BarcodeGeneratorPNG;
                $barcode = $generator->getBarcode($code, BarcodeGeneratorPNG::TYPE_CODE_128, 2, 45);

                return 'data:image/png;base64,'.base64_encode($barcode);
            }
        } catch (\Throwable) {
            // Fallback ke generator online jika library terkendala
        }

        return 'https://bwipjs-api.metafloor.com/?bcid=code128&text='.urlencode($code).'&scale=2&height=12&includetext';
    }

    /**
     * Get QR Code 2D khusus untuk Metode Pembayaran (QRIS) & Verifikasi Digital
     * Berupa matriks 2 dimensi standar BI untuk scan pembayaran dompet digital / m-banking.
     */
    public function getQrCodeUrlAttribute(): string
    {
        $data = urlencode(url('/?tracking='.$this->tracking_number));

        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$data}";
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
        $originCity = strtolower(trim(explode(',', $this->origin)[0] ?? $this->origin));
        $destCity = strtolower(trim(explode(',', $this->destination)[0] ?? $this->destination));

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
            return 'Terkirim pada '.$this->updated_at->isoFormat('D MMM Y, HH:mm').' WIB';
        }

        $daysToAdd = match ($this->service_type) {
            'Same Day' => 0,
            'Express' => 1,
            'Kargo' => $this->vehicle_type === 'pickup' ? 4 : 2,
            default => 2,
        };

        $baseDate = $this->pickup_time ? Carbon::parse($this->pickup_time) : ($this->created_at ?? now());
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
