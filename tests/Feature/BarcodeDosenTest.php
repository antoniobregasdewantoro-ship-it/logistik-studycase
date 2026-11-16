<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BarcodeDosenTest extends TestCase
{
    use DatabaseTransactions;

    public function test_order_and_barang_implements_dosen_barcode_and_qris_guidelines(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = Order::create([
            'customer_id' => $user->id,
            'tracking_number' => 'MRD-12345',
            'package_detail' => 'Laptop HP Pavilion',
            'sender_name' => $user->name,
            'recipient_name' => 'Budi Penerima',
            'origin' => 'Jakarta, ID',
            'destination' => 'Surabaya, ID',
            'weight' => 2.5,
            'service_type' => 'Express',
            'price' => 50000,
            'status' => 'Verifikasi',
        ]);

        // 1. Uji Accessor Kode Barang (INV-00001) dari kolom ID (Slide 6 & 7)
        $this->assertEquals('INV-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT), $order->kode_barang);

        // 2. Uji Method barcodeHtml(3, 100) menghasilkan raw HTML div Code 39 (Slide 6, 8, 10)
        $barcodeHtml = $order->barcodeHtml(3, 100);
        $this->assertStringContainsString('<div style="font-size:0;position:relative;', $barcodeHtml);
        $this->assertStringContainsString('background-color:black', $barcodeHtml);

        // 3. Uji Model Barang (sesuai Slide 6)
        $barang = Barang::find($order->id);
        $this->assertNotNull($barang);
        $this->assertEquals($order->kode_barang, $barang->kode_barang);
        $this->assertEquals('Laptop HP Pavilion', $barang->nama);
        $this->assertStringContainsString('background-color:black', $barang->barcodeHtml(3, 100));

        // 4. Uji Penugasan 2: Pergantian ukuran Barcode (width & height)
        $barcodeKecil = $order->barcodeHtml(2, 50);
        $this->assertStringContainsString('height:50px;', $barcodeKecil);

        // 5. Uji Penugasan 3: Format QR Code 2D untuk pembayaran QRIS (Slide 13)
        $qrisHtml = $order->qrisHtml(5, 5);
        $this->assertStringContainsString('background-color:black', $qrisHtml);

        // 6. Uji Barcode URL dan QR URL
        $this->assertStringStartsWith('data:image/png;base64,', $order->barcode_url);
        $this->assertNotEmpty($order->qr_code_url);

        // 7. Uji Rute Detail Barang /barang/{id} (Slide 11 & 12)
        $detailResponse = $this->get(route('barang.show', $order->id));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Detail Barang');
        $detailResponse->assertSee('Barcode Barang');
        $detailResponse->assertSee($order->kode_barang);
        $detailResponse->assertSee('Laptop HP Pavilion');
        $detailResponse->assertSee('Cetak Barcode');
        $detailResponse->assertDontSee('Scan QRIS');

        // 8. Uji QRIS pembayaran hanya ada di halaman customer
        $customerNewOrder = $this->actingAs($user)->get(route('customer.new-order'));
        $customerNewOrder->assertStatus(200);
        $customerNewOrder->assertSee('Scan QRIS');
    }
}
