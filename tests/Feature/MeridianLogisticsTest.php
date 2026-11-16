<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use App\Imports\ManifestImport;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MeridianLogisticsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test Landing page is accessible publicly and displays smart tariff calculator
     */
    public function test_landing_page_is_accessible_without_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('MERIDIAN');
        $response->assertSee('Kalkulator Tarif Ongkir Cerdas');
    }

    /**
     * Test Customer can track shipment when logged in
     */
    public function test_customer_can_track_shipment_with_login(): void
    {
        $customer = User::where('role', 'customer')->first();
        $order = Order::first();
        $response = $this->actingAs($customer)->get('/lacak?tracking='.$order->tracking_number);
        $response->assertStatus(200);
        $response->assertSee($order->tracking_number);
    }

    /**
     * Test Guest is redirected to login when accessing tracking
     */
    public function test_guest_is_redirected_to_login_when_accessing_tracking(): void
    {
        $response = $this->get('/lacak');
        $response->assertRedirect('/login');
    }

    /**
     * Test Dynamic Tariff Calculation logic via API
     */
    public function test_dynamic_tariff_calculator_logic(): void
    {
        // 3.5 kg * Express (15.000) = 52.500
        $response = $this->postJson('/api/calculate-tariff', [
            'weight' => 3.5,
            'service' => 'Express',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'weight' => 3.5,
            'service' => 'Express',
            'rate_per_kg' => 15000,
            'total_price' => 52500,
        ]);
    }

    /**
     * Test RBAC: Guest is redirected from admin dashboard
     */
    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test RBAC: Courier cannot access admin dashboard (403 forbidden)
     */
    public function test_courier_cannot_access_admin_dashboard(): void
    {
        $courier = User::where('role', 'courier')->first();

        $response = $this->actingAs($courier)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test RBAC: Admin can access dashboard
     */
    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('TOTAL PESANAN');
        $response->assertSee('KURIR AKTIF');
        $response->assertSee('PERINGATAN KETERLAMBATAN');
        $response->assertSee('TINGKAT TEPAT WAKTU');
    }

    /**
     * Test Admin CRUD: Store order with auto resi and dynamic price
     */
    public function test_admin_can_create_new_order_with_dynamic_price(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/orders/store', [
            'sender_name' => 'John Doe',
            'recipient_name' => 'Jane Doe',
            'origin' => 'Sidoarjo',
            'destination' => 'Surabaya',
            'weight' => 4.0,
            'service_type' => 'Reguler', // 4.0 * 9.000 = 36.000
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'sender_name' => 'John Doe',
            'recipient_name' => 'Jane Doe',
            'price' => 36000,
            'status' => 'Verifikasi',
        ]);
    }

    /**
     * Test Image Validation: File exceeding 2MB (2048 KB) must be rejected
     */
    public function test_upload_proof_rejects_files_larger_than_2mb(): void
    {
        Storage::fake('public');

        $courier = User::where('role', 'courier')->first();
        $order = Order::first();
        $order->update(['proof_image' => null]);

        // Create mock image file of 3000 KB (> 2048 KB)
        $oversizedFile = UploadedFile::fake()->create('proof_heavy.jpg', 3000, 'image/jpeg');

        $response = $this->actingAs($courier)->post("/courier/tasks/{$order->id}/upload-proof", [
            'proof_image' => $oversizedFile,
        ]);

        $response->assertSessionHasErrors('proof_image');
        $this->assertNull($order->fresh()->proof_image);
    }

    /**
     * Test Image Validation: Valid file under 2MB is accepted
     */
    public function test_upload_proof_accepts_valid_image_under_2mb(): void
    {
        Storage::fake('public');

        $courier = User::where('role', 'courier')->first();
        $order = Order::first();

        // Create valid mock image of 800 KB (< 2048 KB)
        $validFile = UploadedFile::fake()->create('proof_success.jpg', 800, 'image/jpeg');

        $response = $this->actingAs($courier)->post("/courier/tasks/{$order->id}/upload-proof", [
            'proof_image' => $validFile,
            'notes' => 'Diserahkan ke satpam',
        ]);

        $response->assertRedirect();
        $freshOrder = $order->fresh();
        $this->assertNotNull($freshOrder->proof_image);
        $this->assertEquals('Terkirim', $freshOrder->status);
    }

    /**
     * Test Admin can export PDF report
     */
    public function test_admin_can_export_pdf_report(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/export-pdf');
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /**
     * Test Courier can access Desktop Web Manifest (Gambar 2)
     */
    public function test_courier_can_access_desktop_web_manifest(): void
    {
        $courier = User::where('role', 'courier')->first();

        $response = $this->actingAs($courier)->get('/courier/tasks');
        $response->assertStatus(200);
        $response->assertSee('Manifes pengiriman');
        $response->assertSee('Meridian');
        $response->assertSee('RUTE OPTIMAL');
    }

    /**
     * Test Admin can view and update landing content & animated carousel
     */
    public function test_admin_can_view_and_update_landing_content(): void
    {
        $admin = User::where('role', 'admin')->first();

        $viewResponse = $this->actingAs($admin)->get('/admin/landing-content');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Edit Konten Landing Page');

        $updateResponse = $this->actingAs($admin)->post('/admin/landing-content', [
            'hero_badge' => 'SISTEM LOGISTIK MODERN 2026',
            'hero_title' => 'Pengiriman Cepat Tepat Waktu',
            'hero_subtitle' => 'Jaringan ekspedisi tercanggih dengan armada satelit real-time.',
            'slide_title_0' => 'Armada Truk & Van Modern',
            'slide_image_0' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1200',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');
    }

    /**
     * Test Login Rate Limiting: warnings on 1-3 failures, lock out on 4th
     */
    public function test_login_rate_limiting_tracks_failures_and_locks_at_four(): void
    {
        $testEmail = 'baduser_'.time().'@meridian.com';

        // 1st failed attempt
        $res1 = $this->post('/login', ['email' => $testEmail, 'password' => 'wrongpass']);
        $res1->assertSessionHas('attempt_count', 1);
        $res1->assertSessionHasErrors('email');

        // 2nd failed attempt
        $res2 = $this->post('/login', ['email' => $testEmail, 'password' => 'wrongpass']);
        $res2->assertSessionHas('attempt_count', 2);
        $res2->assertSessionHasErrors('email');

        // 3rd failed attempt
        $res3 = $this->post('/login', ['email' => $testEmail, 'password' => 'wrongpass']);
        $res3->assertSessionHas('attempt_count', 3);
        $res3->assertSessionHasErrors('email');

        // 4th failed attempt: triggers 60s lockout
        $res4 = $this->post('/login', ['email' => $testEmail, 'password' => 'wrongpass']);
        $res4->assertSessionHas('login_locked', true);
        $res4->assertSessionHas('lockout_seconds', 60);

        // 5th attempt while locked: blocked by LoginRateLimitMiddleware
        $res5 = $this->post('/login', ['email' => $testEmail, 'password' => 'wrongpass']);
        $res5->assertRedirect(route('login'));
        $res5->assertSessionHas('login_locked', true);
    }

    /**
     * Test User Registration: can register as customer
     */
    public function test_user_can_register_new_account(): void
    {
        $uniqueEmail = 'reg_customer_'.time().'@meridian.test';

        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => $uniqueEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'role' => 'customer',
        ]);

        $response->assertRedirect('/customer/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => $uniqueEmail,
            'name' => 'Budi Santoso',
            'role' => 'customer',
        ]);
        $this->assertAuthenticated();
    }

    /**
     * Test Google OAuth redirection and demo simulation
     */
    public function test_google_auth_redirect_and_simulation(): void
    {
        $response = $this->get('/auth/google');
        // When client credentials are placeholder/empty, it redirects to callback with simulated user
        $response->assertRedirect();

        $callbackRes = $this->get('/auth/google/callback?simulated=true');
        $callbackRes->assertRedirect();
        $this->assertAuthenticated();
    }

    /**
     * Test Courier can toggle status (Matikan Aplikasi Gojek-Style)
     */
    public function test_courier_can_toggle_online_offline_status(): void
    {
        $courier = User::where('role', 'courier')->first();

        // Toggle to offline
        $res1 = $this->actingAs($courier)->post('/courier/toggle-status');
        $res1->assertRedirect();
        $this->assertEquals('offline', $courier->fresh()->courier_status);

        // Toggle back to online
        $res2 = $this->actingAs($courier)->post('/courier/toggle-status');
        $res2->assertRedirect();
        $this->assertEquals('online', $courier->fresh()->courier_status);
    }

    /**
     * Test Dedicated Tracking Page (/lacak) for authenticated customer
     */
    public function test_dedicated_tracking_page_accessible_and_finds_resi(): void
    {
        $customer = User::where('role', 'customer')->first();
        $order = Order::first();

        $response = $this->actingAs($customer)->get('/lacak?tracking='.$order->tracking_number);
        $response->assertStatus(200);
        $response->assertSee($order->tracking_number);
        $response->assertSee('Live Satellite Tracking System');
    }

    /**
     * Test Admin can create order with detailed address fields
     */
    public function test_admin_can_store_order_with_detailed_address(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/orders/store', [
            'sender_name' => 'CV Maju Jaya',
            'recipient_name' => 'PT Sumber Rejeki',
            'recipient_phone' => '081298765432',
            'origin' => 'Surabaya',
            'destination' => 'Sidoarjo',
            'street_address' => 'Jl. Taman Pinang Indah No. 88',
            'rt_rw' => 'RT 04 / RW 07',
            'village' => 'Kel. Lemahputro',
            'district' => 'Kec. Sidoarjo',
            'postal_code' => '61213',
            'weight' => 5.0,
            'service_type' => 'Express',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'sender_name' => 'CV Maju Jaya',
            'recipient_name' => 'PT Sumber Rejeki',
            'street_address' => 'Jl. Taman Pinang Indah No. 88',
            'rt_rw' => 'RT 04 / RW 07',
            'village' => 'Kel. Lemahputro',
            'district' => 'Kec. Sidoarjo',
            'postal_code' => '61213',
        ]);
    }

    /**
     * Test Admin can upload official PDF report to an order
     */
    public function test_admin_can_upload_pdf_report_and_reject_invalid(): void
    {
        Storage::fake('public');

        $admin = User::where('role', 'admin')->first();
        $order = Order::first();
        $order->update(['proof_image' => null]);

        // 1. Invalid file (not a PDF)
        $invalidFile = UploadedFile::fake()->create('dokumen.txt', 500, 'text/plain');
        $badRes = $this->actingAs($admin)->post("/admin/orders/{$order->id}/upload-pdf", [
            'pdf_file' => $invalidFile,
        ]);
        $badRes->assertSessionHasErrors('pdf_file');

        // 2. Valid PDF file
        $pdfFile = UploadedFile::fake()->create('laporan_manifest_resmi.pdf', 1500, 'application/pdf');
        $goodRes = $this->actingAs($admin)->post("/admin/orders/{$order->id}/upload-pdf", [
            'pdf_file' => $pdfFile,
            'report_notes' => 'Berita Acara Manifest Resmi',
        ]);

        $goodRes->assertRedirect();
        $goodRes->assertSessionHas('success');
        $this->assertNotNull($order->fresh()->pdf_report_path);
    }

    /**
     * Test Lecturer Material PDF Exports: Manifest and Transaksi
     */
    public function test_laporan_pdf_exports_manifest_and_transaksi(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Manifest PDF
        $resManifest = $this->actingAs($admin)->get('/laporan/manifest/pdf');
        $resManifest->assertStatus(200);
        $this->assertEquals('application/pdf', $resManifest->headers->get('Content-Type'));

        // Transaksi PDF
        $resTransaksi = $this->actingAs($admin)->get('/laporan/transaksi/pdf');
        $resTransaksi->assertStatus(200);
        $this->assertEquals('application/pdf', $resTransaksi->headers->get('Content-Type'));
    }

    /**
     * Test Integrated Laporan Pengiriman Page
     */
    public function test_admin_can_access_integrated_laporan_page(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/laporan');
        $response->assertStatus(200);
        $response->assertSee('Laporan Pengiriman');
        $response->assertSee('Export Manifest PDF');
        $response->assertSee('Export Transaksi PDF');
    }

    /**
     * Test Admin can upload local image, GIF, and video file for landing page carousel, while .txt is rejected
     */
    public function test_admin_can_upload_local_image_for_carousel(): void
    {
        Storage::fake('public');

        $admin = User::where('role', 'admin')->first();

        // 1. Upload Gambar biasa (.jpg) -> Berhasil
        $fakeImage = UploadedFile::fake()->create('armada_baru.jpg', 500, 'image/jpeg');
        $response = $this->actingAs($admin)->post('/admin/landing-content', [
            'hero_badge' => 'TEST BADGE',
            'hero_title' => 'TEST TITLE',
            'hero_subtitle' => 'TEST SUBTITLE',
            'slide_title_0' => 'Slide Judul 1',
            'slide_file_0' => $fakeImage,
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 2. Upload GIF (.gif) -> Berhasil
        $fakeGif = UploadedFile::fake()->create('armada_animasi.gif', 1000, 'image/gif');
        $responseGif = $this->actingAs($admin)->post('/admin/landing-content', [
            'hero_badge' => 'TEST BADGE',
            'hero_title' => 'TEST TITLE',
            'hero_subtitle' => 'TEST SUBTITLE',
            'slide_title_0' => 'Slide GIF Animasi',
            'slide_file_0' => $fakeGif,
        ]);
        $responseGif->assertRedirect();
        $responseGif->assertSessionHas('success');

        // 3. Upload Video (.mp4) -> Berhasil
        $fakeVideo = UploadedFile::fake()->create('armada_video.mp4', 2000, 'video/mp4');
        $responseVideo = $this->actingAs($admin)->post('/admin/landing-content', [
            'hero_badge' => 'TEST BADGE',
            'hero_title' => 'TEST TITLE',
            'hero_subtitle' => 'TEST SUBTITLE',
            'slide_title_0' => 'Slide Video Promosi',
            'slide_file_0' => $fakeVideo,
        ]);
        $responseVideo->assertRedirect();
        $responseVideo->assertSessionHas('success');

        // 4. Upload File Dokumen / TXT (.txt) -> Wajib Ditolak
        $fakeTxt = UploadedFile::fake()->create('dokumen_salah.txt', 10, 'text/plain');
        $failTxtResponse = $this->actingAs($admin)->post('/admin/landing-content', [
            'hero_badge' => 'TEST BADGE',
            'hero_title' => 'TEST TITLE',
            'hero_subtitle' => 'TEST SUBTITLE',
            'slide_title_0' => 'Slide File Txt Ditolak',
            'slide_file_0' => $fakeTxt,
        ]);
        $failTxtResponse->assertSessionHasErrors('slide_file_0');

        // 5. File melebihi batas maksimal 20MB -> Ditolak
        $oversizedFile = UploadedFile::fake()->create('huge_banner.mp4', 25000, 'video/mp4');
        $failResponse = $this->actingAs($admin)->post('/admin/landing-content', [
            'hero_badge' => 'TEST BADGE',
            'hero_title' => 'TEST TITLE',
            'hero_subtitle' => 'TEST SUBTITLE',
            'slide_title_0' => 'Slide Judul 1',
            'slide_file_0' => $oversizedFile,
        ]);
        $failResponse->assertSessionHasErrors('slide_file_0');
    }

    /**
     * Test Admin can view users list and add a new courier
     */
    public function test_admin_can_manage_users_and_create_courier(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Akses halaman user management
        $response = $this->actingAs($admin)->get(route('admin.users'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Kurir');

        // 2. Tambah kurir baru
        $postRes = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Kurir Baru Test',
            'email' => 'kurir_baru_test@meridian.id',
            'password' => 'password123',
            'role' => 'courier',
            'phone' => '081234567899',
            'courier_status' => 'active',
        ]);

        $postRes->assertRedirect();
        $postRes->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'kurir_baru_test@meridian.id',
            'role' => 'courier',
        ]);
    }

    /**
     * Test Admin can update and delete user
     */
    public function test_admin_can_update_and_delete_user(): void
    {
        $admin = User::where('role', 'admin')->first();

        $user = User::factory()->create([
            'name' => 'Dummy User',
            'email' => 'dummy_user_temp@meridian.id',
            'role' => 'customer',
        ]);

        // Update
        $updateRes = $this->actingAs($admin)->put(route('admin.users.update', $user->id), [
            'name' => 'Dummy User Updated',
            'email' => 'dummy_user_temp@meridian.id',
            'role' => 'courier',
            'phone' => '0899999999',
        ]);

        $updateRes->assertRedirect();
        $updateRes->assertSessionHas('success');
        $this->assertEquals('Dummy User Updated', $user->fresh()->name);
        $this->assertEquals('courier', $user->fresh()->role);

        // Delete
        $delRes = $this->actingAs($admin)->delete(route('admin.users.destroy', $user->id));
        $delRes->assertRedirect();
        $delRes->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Test Admin new order page renders with Cabang Pengambilan and package detail
     */
    public function test_admin_new_order_renders_cabang_pengambilan_and_reactive_card(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.new-order'));
        $response->assertStatus(200);
        $response->assertSee('CABANG PENGAMBILAN (HUB ASAL)');
        $response->assertSee('DETAIL / DESKRIPSI ISI PAKET');
        $response->assertSee('FOTO PAKET SEBELUM DIKIRIM');
        $response->assertSee('METODE PEMBAYARAN');
        $response->assertSee('QRIS (Lunas)');
        $response->assertSee('Cash (Tunai)');
        $response->assertSee('RINGKASAN PESANAN REAL-TIME');
    }

    /**
     * Test Admin can create order with package detail, payment method, and initial photo (< 2MB)
     */
    public function test_admin_can_create_order_with_package_detail_and_initial_photo(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();

        $photo = UploadedFile::fake()->create('paket_sebelum_kirim.jpg', 800, 'image/jpeg');

        $response = $this->actingAs($admin)->post(route('admin.orders.store'), [
            'tracking_number' => 'MRD-TEST-PKG01',
            'sender_name' => 'Budi Pengirim',
            'recipient_name' => 'Citra Penerima',
            'recipient_phone' => '0812345678',
            'origin' => 'Cabang Hub Waru, Sidoarjo',
            'destination' => 'Surabaya, ID',
            'street_address' => 'Jl. Pemuda No. 45',
            'rt_rw' => 'RT 01 / RW 02',
            'weight' => 5.0,
            'service_type' => 'Express',
            'payment_method' => 'Cash (Tunai)',
            'package_detail' => 'Komponen Sparepart Mesin',
            'initial_photo' => $photo,
        ]);

        $response->assertRedirect('/admin/dashboard');
        $response->assertSessionHas('success');

        $order = Order::where('tracking_number', 'MRD-TEST-PKG01')->first();
        $this->assertNotNull($order);
        $this->assertEquals('Komponen Sparepart Mesin', $order->package_detail);
        $this->assertEquals('Cash (Tunai)', $order->payment_method);
        $this->assertNotNull($order->initial_photo);
        $this->assertTrue(Storage::disk('public')->exists($order->initial_photo));
    }

    /**
     * Test Admin order creation rejects initial photo exceeding 2MB
     */
    public function test_admin_order_creation_rejects_oversized_initial_photo(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();

        $oversizedPhoto = UploadedFile::fake()->create('paket_terlalu_besar.jpg', 3000, 'image/jpeg');

        $response = $this->actingAs($admin)->post(route('admin.orders.store'), [
            'tracking_number' => 'MRD-TEST-PKG02',
            'sender_name' => 'Budi Pengirim',
            'recipient_name' => 'Citra Penerima',
            'origin' => 'Cabang Hub Waru, Sidoarjo',
            'destination' => 'Surabaya, ID',
            'weight' => 5.0,
            'service_type' => 'Express',
            'initial_photo' => $oversizedPhoto,
        ]);

        $response->assertSessionHasErrors('initial_photo');
        $this->assertDatabaseMissing('orders', ['tracking_number' => 'MRD-TEST-PKG02']);
    }

    /**
     * Test login history is recorded for admin, courier, and customer roles
     */
    public function test_login_history_is_recorded_for_all_roles(): void
    {
        $admin = User::where('role', 'admin')->first();
        $courier = User::where('role', 'courier')->first();
        $customer = User::where('role', 'customer')->first();

        // 1. Admin login
        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ])->assertRedirect('/admin/dashboard');

        $this->assertDatabaseHas('login_histories', [
            'user_id' => $admin->id,
            'email' => $admin->email,
            'role' => 'admin',
            'status' => 'Berhasil',
        ]);

        // Logout
        $this->post('/logout');

        // 2. Courier login
        $this->post('/login', [
            'email' => $courier->email,
            'password' => 'password123',
        ])->assertRedirect('/courier/tasks');

        $this->assertDatabaseHas('login_histories', [
            'user_id' => $courier->id,
            'email' => $courier->email,
            'role' => 'courier',
            'status' => 'Berhasil',
        ]);

        // Logout
        $this->post('/logout');

        // 3. Customer login
        $this->post('/login', [
            'email' => $customer->email,
            'password' => 'password123',
        ])->assertRedirect('/customer/dashboard');

        $this->assertDatabaseHas('login_histories', [
            'user_id' => $customer->id,
            'email' => $customer->email,
            'role' => 'customer',
            'status' => 'Berhasil',
        ]);
    }

    /**
     * Test Admin can access and filter Login History view
     */
    public function test_admin_can_access_login_history_view(): void
    {
        $admin = User::where('role', 'admin')->first();
        $customer = User::where('role', 'customer')->first();

        // Customer forbidden
        $this->actingAs($customer)->get(route('admin.login-history'))
            ->assertStatus(403);

        // Admin allowed
        $response = $this->actingAs($admin)->get(route('admin.login-history'));
        $response->assertStatus(200);
        $response->assertSee('Riwayat Login Sistem (3 Role)');
        $response->assertSee('Semua Role (3 Role)');
    }

    /**
     * Test Excel export for laporan (menggunakan Maatwebsite/Laravel Excel .xlsx)
     */
    public function test_admin_can_export_laporan_excel(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Export Excel Barang / Manifest (.xlsx)
        $responseBarang = $this->actingAs($admin)->get(route('laporan.barang.excel'));
        $responseBarang->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $responseBarang->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', $responseBarang->headers->get('content-disposition'));

        // 2. Export Excel Transaksi (.xlsx)
        $responseTransaksi = $this->actingAs($admin)->get(route('laporan.transaksi.excel'));
        $responseTransaksi->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $responseTransaksi->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', $responseTransaksi->headers->get('content-disposition'));

        // 3. Export Excel umum (laporan.excel)
        $response = $this->actingAs($admin)->get(route('laporan.excel'));
        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('content-type'));

        // 4. AdminController export Excel methods
        $adminController = new AdminController;
        $adminExcelBarang = $adminController->exportExcelBarang();
        $this->assertInstanceOf(Response::class, $adminExcelBarang);
        $adminExcelTransaksi = $adminController->exportExcelTransaksi();
        $this->assertInstanceOf(Response::class, $adminExcelTransaksi);
    }

    /**
     * Test Customer creates order with pickup flow and initial checkpoint to warehouse
     */
    public function test_customer_can_create_order_pickup_to_warehouse(): void
    {
        Storage::fake('public');
        $customer = User::where('role', 'customer')->first();

        // Access new order form
        $this->actingAs($customer)->get(route('customer.new-order'))
            ->assertStatus(200)
            ->assertSee('Pesan Pengiriman');

        $photo = UploadedFile::fake()->create('paket_pelanggan.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($customer)->post(route('customer.orders.store'), [
            'tracking_number' => 'MRD-CUST-ORDER01',
            'sender_name' => $customer->name,
            'sender_phone' => '081299998888',
            'recipient_name' => 'Bambang Sudarsono',
            'recipient_phone' => '087711223344',
            'origin' => 'Surabaya, ID',
            'destination' => 'Mojokerto, ID',
            'street_address' => 'Jl. Hayam Wuruk No. 88',
            'rt_rw' => 'RT 04 / RW 01',
            'weight' => 4.5,
            'service_type' => 'Reguler',
            'payment_method' => 'QRIS (Otomatis / Merchant)',
            'pickup_point_type' => 'customer_address',
            'package_detail' => 'Kue Kering & Makanan Kaleng',
            'initial_photo' => $photo,
        ]);

        $response->assertRedirect('/customer/dashboard');
        $response->assertSessionHas('success');

        $order = Order::where('tracking_number', 'MRD-CUST-ORDER01')->first();
        $this->assertNotNull($order);
        $this->assertEquals('QRIS (Otomatis / Merchant)', $order->payment_method);
        $this->assertEquals('Verifikasi', $order->status);

        // Checkpoint flow asserts: courier picks up package to warehouse hub
        $this->assertNotNull($order->checkpoint);
        $this->assertStringContainsString('Gudang', $order->checkpoint);
    }

    /**
     * Test Order vehicle type scope (Pickup for Luar Kota, Sepeda for Di Kecamatan)
     */
    public function test_order_vehicle_type_scope_pickup_vs_sepeda(): void
    {
        // Luar Kota (Surabaya to Mojokerto)
        $orderLuarKota = new Order([
            'origin' => 'Surabaya, ID',
            'destination' => 'Mojokerto, ID',
            'service_type' => 'Reguler',
        ]);
        $this->assertEquals('pickup', $orderLuarKota->vehicle_type);
        $this->assertEquals('fa-solid fa-truck-pickup', $orderLuarKota->vehicle_icon);
        $this->assertEquals('Luar Kota', $orderLuarKota->scope_label);
        $this->assertEquals('Mobil Pickup (Luar Kota)', $orderLuarKota->vehicle_label);

        // Di Kecamatan (Same city: Sidoarjo to Sidoarjo)
        $orderKecamatan = new Order([
            'origin' => 'Sidoarjo, ID',
            'destination' => 'Sidoarjo, ID',
            'service_type' => 'Reguler',
        ]);
        $this->assertEquals('sepeda', $orderKecamatan->vehicle_type);
        $this->assertEquals('fa-solid fa-bicycle', $orderKecamatan->vehicle_icon);
        $this->assertEquals('Di Kecamatan', $orderKecamatan->scope_label);
        $this->assertEquals('Sepeda (Di Kecamatan)', $orderKecamatan->vehicle_label);

        // Kargo service type is always pickup
        $orderKargo = new Order([
            'origin' => 'Sidoarjo, ID',
            'destination' => 'Sidoarjo, ID',
            'service_type' => 'Kargo',
        ]);
        $this->assertEquals('pickup', $orderKargo->vehicle_type);
    }

    /**
     * Test Laporan view contains unified single dropdown export button
     */
    public function test_laporan_page_has_single_dropdown_export_button(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.laporan'));
        $response->assertStatus(200);
        $response->assertSee('Export Laporan');
        $response->assertSee('exportDropdownMenu');
        $response->assertSee('Barang Excel (.xlsx)');
        $response->assertSee('Transaksi Excel (.xlsx)');
        $response->assertSee('Export Manifest PDF');
        $response->assertSee('Export Transaksi PDF');
    }

    /**
     * Test customer cannot track other people's shipments
     */
    public function test_customer_cannot_track_other_customer_resi(): void
    {
        $customer = User::where('role', 'customer')->first();

        $admin = User::where('role', 'admin')->first();

        // Create an order belonging to another person
        $otherOrder = Order::create([
            'tracking_number' => 'MRD-OTHER-999',
            'customer_id' => $admin->id,
            'sender_name' => 'Orang Lain',
            'recipient_name' => 'Penerima Lain',
            'origin' => 'Jakarta, ID',
            'destination' => 'Bandung, ID',
            'weight' => 2.0,
            'service_type' => 'Reguler',
            'price' => 18000,
            'status' => 'Dalam Perjalanan',
        ]);

        $response = $this->actingAs($customer)->get(route('tracking', ['tracking' => 'MRD-OTHER-999']));
        $response->assertStatus(200);
        $response->assertSee('Akses Terbatas');
        $response->assertDontSee('Jakarta, ID → Bandung, ID');
    }

    /**
     * Test Admin can create courier with avatar photo (max 2MB)
     */
    public function test_admin_can_upload_courier_avatar(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();

        $avatar = UploadedFile::fake()->create('foto_kurir.jpg', 600, 'image/jpeg');

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Joko Kurir Baru',
            'email' => 'joko.kurir@meridian.com',
            'password' => 'password123',
            'role' => 'courier',
            'phone' => '081233445566',
            'courier_status' => 'active',
            'avatar' => $avatar,
        ]);

        $response->assertSessionHas('success');

        $courier = User::where('email', 'joko.kurir@meridian.com')->first();
        $this->assertNotNull($courier);
        $this->assertNotNull($courier->avatar);
        $this->assertTrue(Storage::disk('public')->exists($courier->avatar));
        $this->assertStringContainsString('storage/avatars/', $courier->avatar_url);
    }

    /**
     * Test customer can submit rating and review for courier
     */
    public function test_customer_can_rate_courier(): void
    {
        $customer = User::where('role', 'customer')->first();
        $courier = User::where('role', 'courier')->first();

        $order = Order::create([
            'tracking_number' => 'MRD-RATE-01',
            'customer_id' => $customer->id,
            'courier_id' => $courier->id,
            'sender_name' => $customer->name,
            'recipient_name' => 'Budi Client',
            'origin' => 'Surabaya, ID',
            'destination' => 'Sidoarjo, ID',
            'weight' => 3.0,
            'service_type' => 'Reguler',
            'price' => 27000,
            'status' => 'Terkirim',
        ]);

        $response = $this->actingAs($customer)->post(route('customer.orders.rate', $order->id), [
            'rating' => 5,
            'comment' => 'Kurir sangat cepat, ramah, dan paket sampai dengan aman!',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Kurir sangat cepat, ramah, dan paket sampai dengan aman!',
        ]);

        $order->refresh();
        $this->assertNotNull($order->review);
        $this->assertEquals(5, $order->review->rating);
    }

    /**
     * Test order form displays QRIS barcode, dynamic price, and running clock
     */
    public function test_new_order_view_has_qris_barcode_and_live_clock(): void
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->get(route('customer.new-order'));
        $response->assertStatus(200);
        $response->assertSee('qrisDetailContainer');
        $response->assertSee('liveRunningClock');
        $response->assertSee('liveCountdownTimer');
        $response->assertSee('qrisBarcodeImage');
    }

    /**
     * Test estimated arrival is present on new order view, dashboard, and Order model
     */
    public function test_estimated_arrival_feature(): void
    {
        $customer = User::where('role', 'customer')->first();

        // 1. Order form displays estimated arrival section and card row
        $response = $this->actingAs($customer)->get(route('customer.new-order'));
        $response->assertStatus(200);
        $response->assertSee('Estimasi Paket Sampai');
        $response->assertSee('displayEstimatedArrival');
        $response->assertSee('cardEtaDisplay');

        // 2. Order model calculates estimated arrival properly
        $order = Order::first();
        $this->assertNotEmpty($order->estimated_arrival);

        // 3. Customer dashboard displays estimated arrival
        $dashboardResponse = $this->actingAs($customer)->get(route('customer.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Estimasi Paket Sampai');
    }

    /**
     * Test admin sidebar does not have Pelacakan Resi, and customer dashboard restricts admin
     */
    public function test_admin_sidebar_no_tracking_and_customer_dashboard_restricted(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Admin dashboard does not show Pelacakan Resi in sidebar
        $adminDashboard = $this->actingAs($admin)->get(route('admin.dashboard'));
        $adminDashboard->assertStatus(200);
        $adminDashboard->assertDontSee('<span>Pelacakan Resi</span>', false);

        // Admin attempting to visit customer dashboard is forbidden (403)
        $customerDashboard = $this->actingAs($admin)->get(route('customer.dashboard'));
        $customerDashboard->assertStatus(403);
    }

    /**
     * Test kelola pesanan has no edit feature, only barcode and delete with custom modal
     */
    public function test_kelola_pesanan_no_edit_feature_and_has_custom_delete_modal(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.orders'));
        $response->assertStatus(200);

        // No edit manifest/order button in table
        $response->assertDontSee('openEditModal');
        $response->assertDontSee('fa-pen-to-square');

        // Has barcode and delete modal triggers
        $response->assertSee('showBarcodeModal');
        $response->assertSee('modalBarcodeLabel');
        $response->assertSee('fa-barcode');
        $response->assertSee('showQrModal');
        $response->assertSee('openDeleteOrderModal');
        $response->assertSee('modalConfirmDeleteOrder');
    }

    /**
     * Test perbedaan barcode 1D khusus untuk resi paket dan QR code 2D untuk pembayaran QRIS
     */
    public function test_distinction_between_barcode_for_resi_and_qr_code_for_qris_payment(): void
    {
        $order = Order::first();
        $this->assertNotNull($order);

        // 1. Barcode resi: Barcode 1D (Code 128) khusus resi paket
        $barcodeUrl = $order->barcode_url;
        $this->assertNotEmpty($barcodeUrl);
        $this->assertTrue(str_starts_with($barcodeUrl, 'data:image/png;base64,') || str_contains($barcodeUrl, 'code128'));

        // 2. QR Code: QR Code 2D khusus untuk pembayaran QRIS / link verifikasi
        $qrCodeUrl = $order->qr_code_url;
        $this->assertNotEmpty($qrCodeUrl);
        $this->assertStringContainsString('create-qr-code', $qrCodeUrl);

        // 3. Tampilan kelola pesanan admin menggunakan icon barcode dan modal barcode resi 1D
        $admin = User::where('role', 'admin')->first();
        $ordersView = $this->actingAs($admin)->get(route('admin.orders'));
        $ordersView->assertStatus(200);
        $ordersView->assertSee('showBarcodeModal');
        $ordersView->assertSee('modalBarcodeLabel');
        $ordersView->assertSee('fa-barcode');

        // 4. Halaman order baru memiliki detail QR Code QRIS untuk metode pembayaran
        $newOrderView = $this->actingAs($admin)->get(route('admin.new-order'));
        $newOrderView->assertStatus(200);
        $newOrderView->assertSee('qrisDetailContainer');
        $newOrderView->assertSee('QRIS');
        $newOrderView->assertSee('fa-qrcode');
    }

    /**
     * Test kelola akun has reset button and custom delete modal
     */
    public function test_kelola_akun_has_reset_and_custom_delete_modal(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.users'));
        $response->assertStatus(200);

        // Has reset button
        $response->assertSee('resetEditUserForm');
        $response->assertSee('Reset');

        // Has modern delete confirmation modal
        $response->assertSee('openDeleteUserModal');
        $response->assertSee('modalConfirmDeleteUser');
    }

    /**
     * Test removal of lacak resi on public landing navbar and customer dashboard top banner card
     */
    public function test_lacak_resi_removed_from_landing_navbar_and_customer_banner(): void
    {
        // 1. Landing page navbar does not show Lacak Resi navigation link
        $landingResponse = $this->get(route('home'));
        $landingResponse->assertStatus(200);
        $landingResponse->assertDontSee('Lacak Resi eksklusif di Portal Pelanggan');

        // 2. Customer dashboard greeting banner has Kirim Paket Baru but NOT Lacak Resi Mandiri button
        $customer = User::where('role', 'customer')->first();
        $customerResponse = $this->actingAs($customer)->get(route('customer.dashboard'));
        $customerResponse->assertStatus(200);
        $customerResponse->assertSee('Kirim Paket Baru (Order Pengiriman)');
        $customerResponse->assertDontSee('Lacak Resi Mandiri');
    }

    /**
     * Test Google Login works without 403 error across roles and supports custom email
     */
    public function test_google_login_works_properly_with_role_based_redirect(): void
    {
        // 1. When Google user is courier (e.g. keonho ganteng), redirect to courier tasks, not customer dashboard
        $googleUser = User::firstOrCreate(
            ['email' => 'google.user@meridian.com'],
            [
                'name' => 'Kurir Demo Google',
                'password' => bcrypt('password123'),
                'role' => 'courier',
                'courier_status' => 'online',
            ]
        );
        $googleUser->update(['role' => 'courier']);

        $res1 = $this->get(route('auth.google'));
        $res1->assertRedirect(route('courier.tasks'));
        $this->assertAuthenticated();

        // Follow redirect to courier tasks to verify no 403 Forbidden
        $followCourier = $this->get(route('courier.tasks'));
        $followCourier->assertStatus(200);

        // Logout
        $this->post(route('logout'));
        $this->assertGuest();

        // 2. When Google login specifies a customer account
        $res2 = $this->get(route('auth.google', ['email' => 'customer@meridian.com']));
        $res2->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();

        // Follow redirect to customer dashboard
        $followCust = $this->get(route('customer.dashboard'));
        $followCust->assertStatus(200);

        // Logout
        $this->post(route('logout'));

        // 3. When Google login specifies a new email
        $res3 = $this->get(route('auth.google', ['email' => 'pengguna.baru@gmail.com', 'name' => 'Budi Google']));
        $res3->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'pengguna.baru@gmail.com']);

        // Logout
        $this->post(route('logout'));
        $this->assertGuest();

        // 4. Login view shows Google button and account chooser trigger
        $loginRes = $this->get(route('login'));
        $loginRes->assertStatus(200);
        $loginRes->assertSee('Masuk dengan Google');
        $loginRes->assertSee('Pilih / Ganti Akun Google');
        $loginRes->assertSee('modalGoogleChooser');
    }

    /**
     * Test tampilan kelola pesanan memiliki tombol dan modal Import Excel
     */
    public function test_admin_orders_page_contains_import_excel_button_and_modal(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.orders'));
        $response->assertStatus(200);
        $response->assertSee('Import Excel');
        $response->assertSee('modalImportExcel');
        $response->assertSee('name="file_excel"', false);
        $response->assertSee('nomor_resi, nama_pengirim, tujuan, berat, layanan, tarif');
    }

    /**
     * Test admin dapat mengimpor file manifest Excel melalui route admin.import.excel
     */
    public function test_admin_can_import_orders_from_excel_endpoint(): void
    {
        Excel::fake();

        $admin = User::where('role', 'admin')->first();
        $file = UploadedFile::fake()->create('manifest_logistik.xlsx', 100);

        $response = $this->actingAs($admin)->post(route('admin.import.excel'), [
            'file_excel' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        Excel::assertImported('manifest_logistik.xlsx');
    }

    /**
     * Test ManifestImport class memetakan kolom Excel ke model Order dengan benar
     */
    public function test_manifest_import_class_maps_rows_to_orders_model(): void
    {
        $import = new ManifestImport;

        // 1. Baris kosong harus diabaikan (return null)
        $emptyRow = $import->model(['nomor_resi' => '']);
        $this->assertNull($emptyRow);

        // 2. Baris dengan data valid dipetakan ke model Order
        $row = [
            'nomor_resi' => 'MRD-IMPORT-9901',
            'nama_pengirim' => 'PT Maju Terus',
            'tujuan' => 'Bandung',
            'berat' => '15.5',
            'layanan' => 'Kargo',
            'tarif' => '95000',
        ];

        $order = $import->model($row);
        $this->assertNotNull($order);
        $this->assertEquals('MRD-IMPORT-9901', $order->tracking_number);
        $this->assertEquals('PT Maju Terus', $order->sender_name);
        $this->assertEquals('Bandung', $order->destination);
        $this->assertEquals(15.5, $order->weight);
        $this->assertEquals('Kargo', $order->service_type);
        $this->assertEquals(95000, $order->price);
        $this->assertEquals('Verifikasi', $order->status);

        $order->save();
        $this->assertDatabaseHas('orders', [
            'tracking_number' => 'MRD-IMPORT-9901',
            'sender_name' => 'PT Maju Terus',
            'destination' => 'Bandung',
            'status' => 'Verifikasi',
        ]);
    }

    /**
     * Test validasi file import excel menolak berkas non-excel / format terlarang
     */
    public function test_import_excel_validates_file_extension(): void
    {
        $admin = User::where('role', 'admin')->first();
        $invalidFile = UploadedFile::fake()->create('dokumen.txt', 50, 'text/plain');

        $response = $this->actingAs($admin)->post(route('admin.import.excel'), [
            'file_excel' => $invalidFile,
        ]);

        $response->assertSessionHasErrors('file_excel');
    }

    /**
     * Test import Excel dapat memperbarui data pesanan yang diubah dari format file Export Excel
     */
    public function test_import_excel_updates_existing_order_when_modified_in_exported_format(): void
    {
        // 1. Ambil order yang sudah ada di database
        $order = Order::first();
        $this->assertNotNull($order);
        $originalResi = $order->tracking_number;

        $import = new ManifestImport;

        // 2. Simulasikan baris dari file Export Excel (menggunakan header no_resi, pengirim, rute_tujuan, berat_kg, tarif_rp)
        // yang diubah nilainya oleh user di Excel
        $modifiedRow = [
            'id' => $order->id,
            'no_resi' => $originalResi,
            'pengirim' => 'Nama Pengirim Baru dari Excel',
            'penerima' => 'Penerima Baru',
            'rute_asal' => 'Malang',
            'rute_tujuan' => 'Yogyakarta',
            'berat_kg' => '25,50',
            'layanan' => 'Express',
            'tarif_rp' => '382.500',
            'status' => 'Dalam Perjalanan',
        ];

        $updatedOrder = $import->model($modifiedRow);
        $this->assertNotNull($updatedOrder);

        // 3. Verifikasi data di database tabel orders ikut berubah sesuai isi file Excel
        $refreshedOrder = $order->fresh();
        $this->assertEquals('Nama Pengirim Baru dari Excel', $refreshedOrder->sender_name);
        $this->assertEquals('Yogyakarta', $refreshedOrder->destination);
        $this->assertEquals('Malang', $refreshedOrder->origin);
        $this->assertEquals(25.5, (float) $refreshedOrder->weight);
        $this->assertEquals(382500, (float) $refreshedOrder->price);
        $this->assertEquals('Express', $refreshedOrder->service_type);
        $this->assertEquals('Dalam Perjalanan', $refreshedOrder->status);

        // 4. Test menambahkan baris baru di bawah tabel Excel (tanpa resi) otomatis dibuatkan order baru
        $newBottomRow = [
            'pengirim' => 'Pelanggan Baris Baru Bawah',
            'penerima' => 'Penerima Baris Baru',
            'rute_asal' => 'Sidoarjo',
            'rute_tujuan' => 'Denpasar',
            'berat_kg' => '10',
            'layanan' => 'Reguler',
            'tarif_rp' => '90.000',
            'status' => 'Verifikasi',
        ];

        $newOrder = $import->model($newBottomRow);
        $this->assertNotNull($newOrder);
        $newOrder->save();

        $this->assertDatabaseHas('orders', [
            'sender_name' => 'Pelanggan Baris Baru Bawah',
            'destination' => 'Denpasar',
            'service_type' => 'Reguler',
            'status' => 'Verifikasi',
        ]);
        $this->assertTrue(str_starts_with($newOrder->tracking_number, 'ORD-'));
    }
}
