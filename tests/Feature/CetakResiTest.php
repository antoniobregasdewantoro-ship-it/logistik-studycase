<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class CetakResiTest extends TestCase
{
    public function test_print_resi_page_can_be_rendered_with_shopee_style_and_company_logo(): void
    {
        $order = Order::first();
        $this->assertNotNull($order, 'Database must have at least one order');

        $response = $this->get(route('resi.cetak', $order->tracking_number));

        $response->assertStatus(200);
        // Memastikan No Resi ditampilkan
        $response->assertSee($order->tracking_number);
        // Memastikan Logo & Identitas Perusahaan Pengirim Paket ada
        $response->assertSee('MERIDIAN');
        $response->assertSee('EXPRESS');
        $response->assertSee('LOGISTICS & CARGO', false);
        // Memastikan Keterangan Pengirim & Penerima serta Alamat Tujuan ada
        $response->assertSee(e($order->recipient_name));
        $response->assertSee(e($order->sender_name ?? 'Pelanggan'));
        $response->assertSee(e($order->destination));
        // Memastikan Format Khas Resi Shopee ada (Bukti Serah Terima, Garis Gunting, dsb)
        $response->assertSee('GUNTING DI SINI');
        $response->assertSee('BUKTI SERAH TERIMA');
        $response->assertSee('Tanda Tangan Penerima');
    }

    public function test_print_resi_by_order_id_route(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);

        $response = $this->get(route('orders.print-resi', $order->id));

        $response->assertStatus(200);
        $response->assertSee($order->tracking_number);
    }

    public function test_download_resi_pdf_route(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);

        $response = $this->get(route('resi.pdf', $order->tracking_number));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type') ?? '');
    }

    public function test_tracking_page_shows_cetak_resi_button_and_preview_modal(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);

        // Pelacakan resi butuh login
        $user = $order->customer ?? User::where('role', 'customer')->first() ?? User::where('role', 'admin')->first();
        $response = $this->actingAs($user)->get(route('tracking', ['tracking' => $order->tracking_number]));

        $response->assertStatus(200);
        $response->assertSee('Cetak Resi');
        $response->assertSee(route('resi.cetak', $order->tracking_number));
        $response->assertSee('modalResiPreview');
    }

    public function test_admin_orders_page_shows_cetak_resi_button(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);

        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get(route('admin.orders'));

        $response->assertStatus(200);
        $response->assertSee(route('resi.cetak', $order->tracking_number));
        $response->assertSee('Cetak Resi');
        // Memastikan tombol 'Cetak Cepat' dan teks 'Cetak Resi Shopee' sudah ditiadakan
        $response->assertDontSee('Cetak Cepat');
        $response->assertDontSee('Cetak Resi Shopee');
    }

    public function test_customer_dashboard_shows_cetak_resi_button(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);

        $customer = $order->customer ?? User::where('role', 'customer')->first();
        $this->assertNotNull($customer);

        // Pastikan order milik customer ini
        $order->update(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Cetak Resi');
        $response->assertSee(route('resi.cetak', $order->tracking_number));
    }
}
