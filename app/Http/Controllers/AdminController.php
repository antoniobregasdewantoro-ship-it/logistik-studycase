<?php

namespace App\Http\Controllers;

use App\Exports\BarangExport;
use App\Exports\TransaksiExport;
use App\Imports\ManifestImport;
use App\Models\Branch;
use App\Models\LandingContent;
use App\Models\LoginHistory;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminController extends Controller
{
    /**
     * Kode ini untuk menghitung tarif ongkir dinamis: (Berat Paket x Harga Layanan Dasar).
     * Layanan: Reguler (Rp 9.000/kg), Express (Rp 15.000/kg), Kargo (Rp 6.000/kg), Same Day (Rp 20.000/kg).
     */
    public function calculateTariff(float $weight, string $service = 'Reguler'): float
    {
        $rates = [
            'Reguler' => 9000,
            'Express' => 15000,
            'Kargo' => 6000,
            'Same Day' => 20000,
        ];

        $ratePerKg = $rates[$service] ?? 9000;

        return $weight * $ratePerKg;
    }

    /**
     * Kode ini untuk mengenerate nomor resi pelacakan unik otomatis (format ORD-XXXXX).
     */
    public function generateTrackingNumber(): string
    {
        do {
            $number = 'ORD-'.rand(10000, 99999);
        } while (Order::where('tracking_number', $number)->exists());

        return $number;
    }

    /**
     * Kode ini untuk mendapatkan koordinat GPS (Latitude & Longitude) default kota hub di Indonesia.
     */
    public function getCoordinatesByCity(string $cityName): array
    {
        $city = strtolower(trim($cityName));

        $coords = [
            'jakarta' => ['lat' => -6.2088, 'lng' => 106.8456],
            'surabaya' => ['lat' => -7.2575, 'lng' => 112.7521],
            'sidoarjo' => ['lat' => -7.4478, 'lng' => 112.7183],
            'malang' => ['lat' => -7.9666, 'lng' => 112.6326],
            'mojokerto' => ['lat' => -7.4726, 'lng' => 112.4381],
            'bandung' => ['lat' => -6.9175, 'lng' => 107.6191],
            'semarang' => ['lat' => -6.9667, 'lng' => 110.4167],
            'yogyakarta' => ['lat' => -7.7956, 'lng' => 110.3695],
            'medan' => ['lat' => 3.5952, 'lng' => 98.6722],
            'batam' => ['lat' => 1.1301, 'lng' => 104.0529],
            'palembang' => ['lat' => -2.9761, 'lng' => 104.7754],
            'denpasar' => ['lat' => -8.6705, 'lng' => 115.2126],
            'makassar' => ['lat' => -5.1477, 'lng' => 119.4327],
            'balikpapan' => ['lat' => -1.2379, 'lng' => 116.8289],
        ];

        foreach ($coords as $key => $val) {
            if (str_contains($city, $key)) {
                return $val;
            }
        }

        return ['lat' => -7.2575, 'lng' => 112.7521];
    }

    /**
     * Kode ini untuk menampilkan dashboard admin: metrik ringkasan armada (4 bento card), daftar pesanan aktif, dan filter risiko.
     */
    public function dashboard(Request $request): View
    {
        $ordersQuery = Order::with(['customer', 'courier'])->latest();

        if ($request->filled('risk') && in_array($request->risk, ['Low', 'Medium', 'High'])) {
            $ordersQuery->where('delay_risk', $request->risk);
        }

        $orders = $ordersQuery->get();
        $branches = Branch::all();
        $couriers = User::whereIn('role', ['courier', 'kurir'])->get();

        // Metrik statistik armada bento card
        $totalOrders = Order::count();
        $activeCouriers = $couriers->count() ?: 24;
        $delayAlerts = Order::where('delay_risk', 'High')->orWhere('delay_probability', '>', 50)->count();

        $onTimeCount = Order::where('delay_risk', '!=', 'High')->count();
        $onTimeRate = $totalOrders > 0 ? round(($onTimeCount / $totalOrders) * 100, 1) : 94.2;

        return view('admin.dashboard', compact(
            'orders',
            'branches',
            'couriers',
            'totalOrders',
            'activeCouriers',
            'delayAlerts',
            'onTimeRate'
        ));
    }

    /**
     * Kode ini untuk menampilkan halaman formulir pesan pengiriman baru dengan data cabang pengambilan asal dan kurir armada.
     */
    public function newOrderView(): View
    {
        $branches = Branch::all();
        $couriers = User::whereIn('role', ['courier', 'kurir'])->get();

        return view('customer.new-order', compact('branches', 'couriers'));
    }

    /**
     * Kode ini untuk menampilkan tabel daftar kelola manifest pesanan dengan fitur pencarian teks, filter status, dan filter risiko.
     */
    public function orders(Request $request): View
    {
        $query = Order::with(['customer', 'courier'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('risk')) {
            $query->where('delay_risk', $request->risk);
        }

        $orders = $query->paginate(15)->withQueryString();
        $couriers = User::whereIn('role', ['courier', 'kurir'])->get();
        $branches = Branch::all();

        return view('admin.orders', compact('orders', 'couriers', 'branches'));
    }

    /**
     * Kode ini untuk menampilkan halaman CMS pengeditan konten landing page (hero banner & showcase armada publik).
     */
    public function landingContentView(): View
    {
        $hero = LandingContent::firstOrCreate(
            ['section_key' => 'hero'],
            [
                'badge_text' => 'SISTEM LOGISTIK & PELACAKAN REAL-TIME TERINTEGRASI',
                'title' => 'Kirim Paket Cepat, Aman, dan Tepat Waktu.',
                'subtitle' => 'Layanan ekspedisi terpercaya ke seluruh pelosok Nusantara. Lacak keberadaan paket Anda secara live melalui peta atau cek kalkulasi estimasi tarif otomatis.',
            ]
        );

        $carousel = LandingContent::firstOrCreate(
            ['section_key' => 'carousel'],
            [
                'title' => 'Armada & Jaringan Pengiriman Modern',
                'subtitle' => 'Teknologi otomasi logistik terdepan menghubungkan pulau-pulau di Indonesia.',
                'extra_data' => [
                    [
                        'tag' => 'ARMADA CEPAT',
                        'title' => 'Armada Pickup & Kargo Antar Kota',
                        'desc' => 'Layanan Same-Day dan Next-Day dengan armada van dan sepeda motor berinsulasi tinggi.',
                        'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'tag' => 'LIVE TRACKING',
                        'title' => 'Pemantauan Satelit & OpenStreetMap',
                        'desc' => 'GPS real-time terhubung langsung ke ponsel kurir dan command center drop point.',
                        'image' => 'https://images.unsplash.com/photo-1519003722824-194d4455a60c?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'tag' => 'VERIFIKASI DIGITAL',
                        'title' => 'Bukti Foto Serah Terima & Barcode QR',
                        'desc' => 'Penerima dapat langsung mengecek foto bukti pengiriman asli secara transparan.',
                        'image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],
            ]
        );

        return view('admin.landing-content', compact('hero', 'carousel'));
    }

    /**
     * Menyimpan perubahan teks banner landing page dan upload media armada (Gambar, GIF, atau Video).
     * File non-media seperti .txt atau dokumen lainnya akan ditolak otomatis.
     */
    public function updateLandingContent(Request $request): RedirectResponse
    {
        // Validasi input: hanya mengizinkan Gambar (JPG, PNG, WEBP), GIF, dan Video (MP4, WEBM, MOV). File .txt dan format lain ditolak
        $request->validate([
            'hero_badge' => 'required|string|max:100',
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string',
            'slide_title_0' => 'required|string',
            'slide_file_0' => 'nullable|file|max:20480|extensions:jpeg,png,jpg,webp,gif,mp4,webm,ogg,mov',
            'slide_file_1' => 'nullable|file|max:20480|extensions:jpeg,png,jpg,webp,gif,mp4,webm,ogg,mov',
            'slide_file_2' => 'nullable|file|max:20480|extensions:jpeg,png,jpg,webp,gif,mp4,webm,ogg,mov',
        ], [
            'slide_file_0.uploaded' => 'File slide 1 gagal terunggah ke server! Silakan restart terminal (php artisan serve) Anda agar pengaturan batas upload baru aktif.',
            'slide_file_1.uploaded' => 'File slide 2 gagal terunggah ke server! Silakan restart terminal (php artisan serve) Anda agar pengaturan batas upload baru aktif.',
            'slide_file_2.uploaded' => 'File slide 3 gagal terunggah ke server! Silakan restart terminal (php artisan serve) Anda agar pengaturan batas upload baru aktif.',
            'slide_file_0.max' => 'Ukuran file slide 1 maksimal 20MB.',
            'slide_file_1.max' => 'Ukuran file slide 2 maksimal 20MB.',
            'slide_file_2.max' => 'Ukuran file slide 3 maksimal 20MB.',
            'slide_file_0.extensions' => 'Format file slide 1 tidak didukung! Hanya diperbolehkan upload Gambar (JPG, PNG, WEBP), GIF animasi, atau Video (MP4, WEBM). Format seperti .txt atau dokumen tidak diizinkan.',
            'slide_file_1.extensions' => 'Format file slide 2 tidak didukung! Hanya diperbolehkan upload Gambar (JPG, PNG, WEBP), GIF animasi, atau Video (MP4, WEBM). Format seperti .txt atau dokumen tidak diizinkan.',
            'slide_file_2.extensions' => 'Format file slide 3 tidak didukung! Hanya diperbolehkan upload Gambar (JPG, PNG, WEBP), GIF animasi, atau Video (MP4, WEBM). Format seperti .txt atau dokumen tidak diizinkan.',
        ]);

        // Simpan Data Hero Banner
        LandingContent::updateOrCreate(
            ['section_key' => 'hero'],
            [
                'badge_text' => $request->hero_badge,
                'title' => $request->hero_title,
                'subtitle' => $request->hero_subtitle,
            ]
        );

        $existingCarousel = LandingContent::where('section_key', 'carousel')->first();
        $existingSlides = $existingCarousel?->extra_data ?? [];

        // Susun Slides Showcase Armada Pengiriman
        $slides = [];
        for ($i = 0; $i < 3; $i++) {
            if ($request->filled("slide_title_{$i}")) {
                $imageUrl = $existingSlides[$i]['image'] ?? 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80';

                // Cek jika ada upload file gambar/gif/video baru dari folder lokal
                if ($request->hasFile("slide_file_{$i}")) {
                    /** @var UploadedFile $file */
                    $file = $request->file("slide_file_{$i}");
                    $fileName = 'slide_'.$i.'_'.time().'_'.rand(100, 999).'.'.$file->getClientOriginalExtension();
                    $path = $file->storeAs('landing', $fileName, 'public');
                    $imageUrl = asset('storage/'.$path);
                }

                $slides[] = [
                    'tag' => $request->input("slide_tag_{$i}", 'LOGISTIK'),
                    'title' => $request->input("slide_title_{$i}"),
                    'desc' => $request->input("slide_desc_{$i}", ''),
                    'image' => $imageUrl,
                ];
            }
        }

        LandingContent::updateOrCreate(
            ['section_key' => 'carousel'],
            [
                'title' => $request->input('carousel_title', 'Armada Pengiriman Modern'),
                'subtitle' => $request->input('carousel_subtitle', ''),
                'extra_data' => $slides,
            ]
        );

        return redirect()->back()->with('success', 'Konten Landing Page & Gambar Armada berhasil diperbarui!');
    }

    /**
     * Kode ini untuk menampilkan halaman laporan pengiriman terintegrasi: total omset, tonase kargo, paket terkirim, dan tombol cetak PDF.
     */
    public function laporanView(Request $request): View
    {
        $query = Order::with(['customer', 'courier'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service')) {
            $query->where('service_type', $request->service);
        }

        $orders = $query->paginate(15)->withQueryString();

        $totalRevenue = Order::sum('price');
        $totalOrders = Order::count();
        $deliveredOrders = Order::where('status', 'Terkirim')->count();
        $totalWeight = Order::sum('weight');

        return view('admin.laporan', compact(
            'orders',
            'totalRevenue',
            'totalOrders',
            'deliveredOrders',
            'totalWeight'
        ));
    }

    /**
     * Kode ini untuk menampilkan halaman manajemen kelola kurir & akun pengguna sistem beserta bento ringkasan statistik akun.
     */
    public function usersView(Request $request): View
    {
        $query = User::latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $totalUsers = User::count();
        $totalCouriers = User::whereIn('role', ['courier', 'kurir'])->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $activeCouriers = User::whereIn('role', ['courier', 'kurir'])->where('courier_status', 'active')->count();

        return view('admin.users', compact(
            'users',
            'totalUsers',
            'totalCouriers',
            'totalCustomers',
            'activeCouriers'
        ));
    }

    /**
     * Kode ini untuk menambahkan akun kurir armada baru atau pengguna baru dengan kata sandi terenkripsi (Hash::make) dan upload foto avatar (maks 2MB).
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|string|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:courier,customer,admin',
            'phone' => 'nullable|string|max:30',
            'courier_status' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'email.unique' => 'Email tersebut sudah terdaftar di sistem.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'avatar.max' => 'Ukuran foto avatar/kurir maksimal 2MB (2048 KB).',
            'avatar.image' => 'File harus berupa gambar (JPG, JPEG, PNG, WEBP).',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            /** @var UploadedFile $avatarFile */
            $avatarFile = $request->file('avatar');
            $avatarPath = $avatarFile->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'avatar' => $avatarPath,
            'courier_status' => $validated['role'] === 'courier' ? ($validated['courier_status'] ?? 'active') : null,
        ]);

        $roleText = $user->role === 'courier' ? 'Kurir' : ($user->role === 'admin' ? 'Administrator' : 'Pelanggan');

        return redirect()->back()->with('success', "Akun {$roleText} '{$user->name}' berhasil ditambahkan ke sistem!");
    }

    /**
     * Kode ini untuk memperbarui profil akun pengguna, alamat email, kata sandi, role hak akses, status operasional kurir, dan foto avatar.
     */
    public function updateUser(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => "required|string|email|max:150|unique:users,email,{$id}",
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:courier,customer,admin',
            'phone' => 'nullable|string|max:30',
            'courier_status' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'avatar.max' => 'Ukuran foto avatar/kurir maksimal 2MB (2048 KB).',
            'avatar.image' => 'File harus berupa gambar (JPG, JPEG, PNG, WEBP).',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'courier_status' => $validated['role'] === 'courier' ? ($validated['courier_status'] ?? 'active') : null,
        ];

        if ($request->hasFile('avatar')) {
            /** @var UploadedFile $avatarFile */
            $avatarFile = $request->file('avatar');
            $updateData['avatar'] = $avatarFile->store('avatars', 'public');
        }

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', "Data akun '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Kode ini untuk pelanggan memberikan penilaian (rating 1-5 bintang & ulasan) kepada kurir pengantar paket
     */
    public function rateCourier(Request $request, int $id): RedirectResponse
    {
        $order = Order::with('courier')->findOrFail($id);

        $currentUser = auth()->user();
        $currentName = $currentUser ? $currentUser->name : '';

        if ($order->customer_id !== auth()->id() && strtolower(trim($order->sender_name)) !== strtolower(trim($currentName))) {
            return redirect()->back()->with('error', 'Anda hanya dapat memberikan rating pada paket milik Anda sendiri.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Review::updateOrCreate(
            [
                'order_id' => $order->id,
                'customer_id' => auth()->id(),
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        $courierName = $order->courier?->name ?? 'Kurir Armada';

        return redirect()->back()->with('success', "Penilaian bintang {$validated['rating']} untuk kurir {$courierName} berhasil dikirim! Terima kasih atas ulasan Anda.");
    }

    /**
     * Kode ini untuk menghapus akun pengguna dari sistem (dilengkapi proteksi anti self-delete bagi akun yang sedang login).
     */
    public function destroyUser(int $id): RedirectResponse
    {
        if (auth()->id() === $id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "Akun '{$name}' berhasil dihapus dari sistem.");
    }

    /**
     * Kode ini untuk membuat pesanan pengiriman baru, memvalidasi input, menghitung tarif, dan menyimpan foto kondisi awal paket di cabang (maks 2MB).
     */
    public function storeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tracking_number' => 'nullable|string|unique:orders,tracking_number',
            'sender_name' => 'required|string|max:150',
            'recipient_name' => 'required|string|max:150',
            'recipient_phone' => 'nullable|string|max:30',
            'origin' => 'required|string|max:100',
            'destination' => 'required|string|max:100',
            'street_address' => 'nullable|string|max:255',
            'rt_rw' => 'nullable|string|max:50',
            'village' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'weight' => 'required|numeric|min:0.1',
            'service_type' => 'required|string',
            'payment_method' => 'nullable|string',
            'courier_id' => 'nullable|exists:users,id',
            'pickup_time' => 'nullable',
            'package_detail' => 'nullable|string|max:255',
            'initial_photo' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:2048',
            'notes' => 'nullable|string|max:500',
        ], [
            'initial_photo.image' => 'File foto paket harus berupa gambar (JPG, PNG, WEBP).',
            'initial_photo.mimes' => 'Format foto paket harus JPG, JPEG, PNG, atau WEBP.',
            'initial_photo.max' => 'Ukuran foto paket sebelum dikirim maksimal 2MB (2048 KB).',
        ]);

        $resi = ! empty($validated['tracking_number'])
            ? strtoupper(trim($validated['tracking_number']))
            : $this->generateTrackingNumber();

        $price = $this->calculateTariff((float) $validated['weight'], $validated['service_type']);
        $coords = $this->getCoordinatesByCity($validated['origin']);

        $courierId = $validated['courier_id'] ?? null;
        if (! $courierId) {
            $defaultCourier = User::whereIn('role', ['courier', 'kurir'])->first();
            $courierId = $defaultCourier?->id;
        }

        $delayRisk = 'Low';
        $delayProb = rand(8, 20);
        if ($validated['weight'] > 20 || $validated['service_type'] === 'Kargo') {
            $delayRisk = 'Medium';
            $delayProb = rand(30, 48);
        }

        $initialPhotoPath = null;
        if ($request->hasFile('initial_photo')) {
            /** @var UploadedFile $file */
            $file = $request->file('initial_photo');
            $fileName = 'pkg_before_'.$resi.'_'.time().'.'.$file->getClientOriginalExtension();
            $initialPhotoPath = $file->storeAs('packages', $fileName, 'public');
        }

        Order::create([
            'tracking_number' => $resi,
            'customer_id' => auth()->id(),
            'courier_id' => $courierId,
            'sender_name' => $validated['sender_name'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'] ?? null,
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'street_address' => $validated['street_address'] ?? null,
            'rt_rw' => $validated['rt_rw'] ?? null,
            'village' => $validated['village'] ?? null,
            'district' => $validated['district'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'weight' => $validated['weight'],
            'service_type' => $validated['service_type'],
            'pickup_time' => now(),
            'price' => $price,
            'payment_method' => $validated['payment_method'] ?? 'QRIS (Lunas)',
            'delay_risk' => $delayRisk,
            'delay_probability' => $delayProb,
            'status' => 'Verifikasi',
            'latitude' => $coords['lat'],
            'longitude' => $coords['lng'],
            'checkpoint' => 'Kurir ditugaskan menjemput paket di lokasi pengirim untuk dibawa ke Gudang (Hub '.$validated['origin'].') sebelum dikirim ke '.$validated['destination'].'.',
            'package_detail' => $validated['package_detail'] ?? null,
            'initial_photo' => $initialPhotoPath,
            'notes' => $validated['notes'] ?? null,
        ]);

        $currentUser = auth()->user();
        if (auth()->check() && $currentUser && $currentUser->role === 'customer') {
            return redirect()->route('customer.dashboard')->with(
                'success',
                "Pesanan {$resi} berhasil dipesan! Kurir armada akan segera menjemput paket di alamat Anda untuk dibawa ke gudang cabang dan dikirim ke tujuan."
            );
        }

        return redirect()->route('admin.dashboard')->with(
            'success',
            "Pesanan {$resi} berhasil dibuat! Estimasi Tarif: Rp ".number_format($price, 0, ',', '.')
        );
    }

    /**
     * Kode ini untuk memperbarui data manifest pesanan, alamat penerima, status pengiriman, koordinat peta GPS, dan foto kondisi paket.
     */
    public function updateOrder(Request $request, int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'sender_name' => 'required|string|max:150',
            'recipient_name' => 'required|string|max:150',
            'recipient_phone' => 'nullable|string|max:30',
            'origin' => 'required|string|max:100',
            'destination' => 'required|string|max:100',
            'street_address' => 'nullable|string|max:255',
            'rt_rw' => 'nullable|string|max:50',
            'village' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'weight' => 'required|numeric|min:0.1',
            'service_type' => 'required|string',
            'courier_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'checkpoint' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'delay_risk' => 'nullable|string',
            'package_detail' => 'nullable|string|max:255',
            'initial_photo' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $price = $this->calculateTariff((float) $validated['weight'], $validated['service_type']);

        $updateData = [
            'sender_name' => $validated['sender_name'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'] ?? $order->recipient_phone,
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'street_address' => $validated['street_address'] ?? $order->street_address,
            'rt_rw' => $validated['rt_rw'] ?? $order->rt_rw,
            'village' => $validated['village'] ?? $order->village,
            'district' => $validated['district'] ?? $order->district,
            'postal_code' => $validated['postal_code'] ?? $order->postal_code,
            'weight' => $validated['weight'],
            'service_type' => $validated['service_type'],
            'price' => $price,
            'courier_id' => $validated['courier_id'] ?? $order->courier_id,
            'status' => $validated['status'],
            'checkpoint' => $validated['checkpoint'] ?? $order->checkpoint,
            'latitude' => $validated['latitude'] ?? $order->latitude,
            'longitude' => $validated['longitude'] ?? $order->longitude,
            'delay_risk' => $validated['delay_risk'] ?? $order->delay_risk,
            'package_detail' => $validated['package_detail'] ?? $order->package_detail,
            'notes' => $validated['notes'] ?? $order->notes,
        ];

        if ($request->hasFile('initial_photo')) {
            /** @var UploadedFile $file */
            $file = $request->file('initial_photo');
            $fileName = 'pkg_before_'.$order->tracking_number.'_'.time().'.'.$file->getClientOriginalExtension();
            $updateData['initial_photo'] = $file->storeAs('packages', $fileName, 'public');
        }

        $order->update($updateData);

        return redirect()->back()->with('success', "Pesanan {$order->tracking_number} berhasil diperbarui.");
    }

    /**
     * Kode ini untuk memverifikasi manifest pengiriman oleh admin dan menugaskan kurir armada (mengubah status menjadi 'Dalam Perjalanan').
     */
    public function verifyOrder(int $id): RedirectResponse
    {
        $order = Order::with('courier')->findOrFail($id);
        $courierName = $order->courier?->name ?? 'Kurir Armada Meridian';

        $order->update([
            'status' => 'Dalam Perjalanan',
            'checkpoint' => "Manifest diverifikasi oleh Admin. Sedang diantar oleh {$courierName} menuju {$order->destination}.",
        ]);

        return redirect()->back()->with('success', "Manifest {$order->tracking_number} berhasil diverifikasi & ditugaskan.");
    }

    /**
     * Kode ini untuk menghapus data manifest pesanan dari sistem database.
     */
    public function destroyOrder(int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        $resi = $order->tracking_number;
        $order->delete();

        return redirect()->back()->with('success', "Data pesanan {$resi} berhasil dihapus.");
    }

    /**
     * Kode ini untuk mengunggah foto bukti serah terima paket pengiriman (POD) dengan validasi gambar ketat maksimal 2MB.
     */
    public function uploadProof(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'proof_image.required' => 'Wajib memilih file foto bukti serah terima.',
            'proof_image.image' => 'File harus berupa gambar.',
            'proof_image.mimes' => 'Format file yang diizinkan hanya JPG, JPEG, atau PNG.',
            'proof_image.max' => 'Ukuran file foto maksimal adalah 2MB (2048 KB).',
        ]);

        $order = Order::findOrFail($id);

        if ($request->hasFile('proof_image')) {
            /** @var UploadedFile $file */
            $file = $request->file('proof_image');
            $fileName = 'proof_'.$order->tracking_number.'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('proofs', $fileName, 'public');

            $order->update([
                'proof_image' => $path,
                'status' => 'Terkirim',
                'checkpoint' => 'Paket telah terkirim ke penerima dan foto serah terima telah diverifikasi.',
            ]);
        }

        return redirect()->back()->with('success', 'Bukti foto berhasil diunggah dan pesanan ditandai Terkirim!');
    }

    /**
     * Kode ini untuk mengekstrak dan mengunduh laporan PDF resmi ringkasan armada & pesanan dalam format A4 landscape.
     */
    public function exportPdf(Request $request)
    {
        $orders = Order::with(['customer', 'courier'])->latest()->get();
        $branches = Branch::all();
        $couriers = User::whereIn('role', ['courier', 'kurir'])->get();

        $stats = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('price'),
            'delivered' => $orders->where('status', 'Terkirim')->count(),
            'in_transit' => $orders->where('status', 'Dalam Perjalanan')->count(),
            'verifying' => $orders->where('status', 'Verifikasi')->count(),
            'high_risk' => $orders->where('delay_risk', 'High')->count(),
            'active_couriers' => $couriers->count(),
            'total_branches' => $branches->count(),
            'generated_at' => now()->translatedFormat('d F Y, H:i').' WIB',
        ];

        $pdf = Pdf::loadView('admin.report-pdf', compact('orders', 'stats', 'couriers', 'branches'))
            ->setPaper('a4', 'landscape');

        $fileName = 'Meridian_Laporan_Armada_Pesanan_'.now()->format('Ymd_His').'.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Kode ini untuk mengekspor data barang / manifest ke file Excel (.xlsx)
     */
    public function exportExcelBarang(): BinaryFileResponse
    {
        return Excel::download(new BarangExport, 'laporan-barang-'.date('Y-m-d').'.xlsx');
    }

    /**
     * Kode ini untuk mengekspor data transaksi & omset ke file Excel (.xlsx)
     */
    public function exportExcelTransaksi(): BinaryFileResponse
    {
        return Excel::download(new TransaksiExport, 'laporan-transaksi-'.date('Y-m-d').'.xlsx');
    }

    /**
     * Kode ini untuk export Excel umum (mendukung unduhan data logistik).
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        return $this->exportExcelBarang();
    }

    /**
     * Kode ini untuk mengimpor data manifest pesanan secara massal dari file Excel (.xlsx, .xls, .csv).
     */
    public function importExcel(Request $request): RedirectResponse
    {
        // Validasi file: wajib diunggah, format Excel/CSV, maksimal 5MB (5120 KB)
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'file_excel.required' => 'Pilih berkas file Excel terlebih dahulu.',
            'file_excel.file' => 'Berkas yang diunggah harus berupa file yang valid.',
            'file_excel.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'file_excel.max' => 'Ukuran file Excel maksimal 5MB.',
        ]);

        try {
            // Eksekusi import dengan Excel::import menggunakan ManifestImport
            Excel::import(new ManifestImport, $request->file('file_excel'));

            return back()->with('success', 'Data manifest pengiriman berhasil diimpor dari file Excel!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengimpor file Excel: '.$e->getMessage());
        }
    }

    /**
     * Kode ini untuk menampilkan halaman riwayat login untuk 3 role (Admin, Kurir, Pelanggan) dengan filter dan statistik
     */
    public function loginHistory(Request $request): View
    {
        $query = LoginHistory::with('user')->latest('login_at');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('device', 'like', "%{$search}%");
            });
        }

        $histories = $query->paginate(20)->withQueryString();

        $totalLogins = LoginHistory::count();
        $adminLogins = LoginHistory::where('role', 'admin')->count();
        $courierLogins = LoginHistory::whereIn('role', ['courier', 'kurir'])->count();
        $customerLogins = LoginHistory::where('role', 'customer')->count();

        return view('admin.login-history', compact(
            'histories',
            'totalLogins',
            'adminLogins',
            'courierLogins',
            'customerLogins'
        ));
    }
}
