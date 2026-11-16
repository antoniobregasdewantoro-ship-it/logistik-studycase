<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    /**
     * Terhubung ke tabel orders pada database PBL Meridian Logistics
     */
    protected $table = 'orders';

    protected $guarded = [];

    /**
     * Accessor: kode barang dari kolom id (Slide 6 & 7)
     * Menghasilkan format INV-00001 dengan 5 digit padding nol.
     */
    public function getKodeBarangAttribute(): string
    {
        return 'INV-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Method: barcode HTML Code 39 menggunakan Milon Barcode (DNS1D) (Slide 6, 8, 9, 10, 13)
     * Tanpa memerlukan ekstensi GD (output div HTML murni).
     *
     * @param  int  $width  Ketebalan garis (default: 3)
     * @param  int  $height  Tinggi garis dalam px (default: 100)
     */
    public function barcodeHtml(int $width = 3, int $height = 100): string
    {
        return \DNS1D::getBarcodeHTML($this->kode_barang, 'C39', $width, $height);
    }

    /**
     * Method: QR Code 2D HTML menggunakan Milon Barcode (DNS2D) (Slide 13)
     * Digunakan khusus untuk metode pembayaran QRIS & verifikasi digital.
     *
     * @param  int  $width  Ukuran bujur sangkar horizontal (default: 5)
     * @param  int  $height  Ukuran bujur sangkar vertikal (default: 5)
     */
    public function qrisHtml(int $width = 5, int $height = 5): string
    {
        $payload = '00020101021226670016ID.CO.MERIDIAN.WWW0118936009990000000001520454995303360540'.str_pad((string) ($this->id ?? 1), 6, '0', STR_PAD_LEFT);

        return \DNS2D::getBarcodeHTML($payload, 'QRCODE', $width, $height);
    }

    /**
     * Alias nama barang untuk keselarasan dengan modul inventaris & order (Slide 11)
     */
    public function getNamaAttribute(): string
    {
        return $this->package_detail ?? $this->notes ?? $this->sender_name ?? 'Barang Logistik #'.$this->id;
    }
}
