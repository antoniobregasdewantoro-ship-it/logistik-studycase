<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@meridian.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081234567890',
            ]
        );

        // 2. Akun Kurir
        $courier = User::firstOrCreate(
            ['email' => 'kurir@meridian.com'],
            [
                'name' => 'Budi Santoso (Kurir)',
                'password' => Hash::make('password123'),
                'role' => 'courier',
                'phone' => '082198765432',
            ]
        );

        $courier2 = User::firstOrCreate(
            ['email' => 'hendra@meridian.com'],
            [
                'name' => 'Hendra Wijaya (Armada Pickup)',
                'password' => Hash::make('password123'),
                'role' => 'courier',
                'phone' => '082233445566',
            ]
        );

        // 3. Akun Customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@meridian.com'],
            [
                'name' => 'Andi Pelanggan',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '083112233445',
            ]
        );

        // 4. Data Cabang (Branch) & Koordinat Peta Indonesia
        $branches = [
            ['name' => 'Cabang Medan', 'city' => 'Medan', 'address' => 'Jl. Gatot Subroto No. 12', 'position_x' => 11, 'position_y' => 24, 'latitude' => 3.5952, 'longitude' => 98.6722],
            ['name' => 'Cabang Batam', 'city' => 'Batam', 'address' => 'Jl. Yos Sudarso No. 45', 'position_x' => 22, 'position_y' => 43, 'latitude' => 1.1301, 'longitude' => 104.0529],
            ['name' => 'Cabang Palembang', 'city' => 'Palembang', 'address' => 'Jl. Jend. Sudirman No. 88', 'position_x' => 22, 'position_y' => 60, 'latitude' => -2.9761, 'longitude' => 104.7754],
            ['name' => 'Cabang Utama Jakarta', 'city' => 'Jakarta', 'address' => 'Jl. HR Rasuna Said No. 10', 'position_x' => 29, 'position_y' => 78, 'latitude' => -6.2088, 'longitude' => 106.8456],
            ['name' => 'Cabang Bandung', 'city' => 'Bandung', 'address' => 'Jl. Asia Afrika No. 5', 'position_x' => 31, 'position_y' => 81, 'latitude' => -6.9175, 'longitude' => 107.6191],
            ['name' => 'Cabang Surabaya', 'city' => 'Surabaya', 'address' => 'Jl. Pemuda No. 18', 'position_x' => 41, 'position_y' => 81, 'latitude' => -7.2575, 'longitude' => 112.7521],
            ['name' => 'Cabang Denpasar', 'city' => 'Denpasar', 'address' => 'Jl. Teuku Umar No. 22', 'position_x' => 47, 'position_y' => 83, 'latitude' => -8.6705, 'longitude' => 115.2126],
            ['name' => 'Cabang Pontianak', 'city' => 'Pontianak', 'address' => 'Jl. Ahmad Yani No. 9', 'position_x' => 33, 'position_y' => 50, 'latitude' => -0.0263, 'longitude' => 109.3425],
            ['name' => 'Cabang Balikpapan', 'city' => 'Balikpapan', 'address' => 'Jl. Sudirman No. 101', 'position_x' => 46, 'position_y' => 50, 'latitude' => -1.2379, 'longitude' => 116.8289],
            ['name' => 'Cabang Makassar', 'city' => 'Makassar', 'address' => 'Jl. Ujung Pandang No. 3', 'position_x' => 54, 'position_y' => 66, 'latitude' => -5.1477, 'longitude' => 119.4327],
            ['name' => 'Cabang Manado', 'city' => 'Manado', 'address' => 'Jl. Sam Ratulangi No. 15', 'position_x' => 61, 'position_y' => 28, 'latitude' => 1.4748, 'longitude' => 124.8428],
            ['name' => 'Cabang Ambon', 'city' => 'Ambon', 'address' => 'Jl. Pattimura No. 7', 'position_x' => 72, 'position_y' => 60, 'latitude' => -3.6547, 'longitude' => 128.1906],
            ['name' => 'Cabang Jayapura', 'city' => 'Jayapura', 'address' => 'Jl. Ahmad Yani No. 50', 'position_x' => 94, 'position_y' => 54, 'latitude' => -2.5916, 'longitude' => 140.6690],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(['name' => $branch['name']], $branch);
        }

        // 5. Data Awal Pesanan (Orders)
        $orders = [
            [
                'tracking_number' => 'MRD-48207',
                'customer_id' => $customer->id,
                'courier_id' => $courier->id,
                'sender_name' => 'Ahmad Fauzi',
                'recipient_name' => 'Kalisogo Logistics',
                'recipient_phone' => '085711223344',
                'origin' => 'Sidoarjo',
                'destination' => 'Surabaya',
                'weight' => 3.0,
                'service_type' => 'Reguler',
                'pickup_time' => now()->subHours(2),
                'price' => 27000,
                'payment_method' => 'QRIS (Lunas)',
                'delay_risk' => 'Low',
                'delay_probability' => 10,
                'status' => 'Verifikasi',
                'latitude' => -7.4478,
                'longitude' => 112.7183,
                'checkpoint' => 'Drop Point Sidoarjo (Menunggu Verifikasi Manifest)',
                'notes' => 'Barang fragile, mohon hati-hati.',
            ],
            [
                'tracking_number' => 'MRD-48208',
                'customer_id' => $customer->id,
                'courier_id' => $courier2->id,
                'sender_name' => 'Siti Aminah',
                'recipient_name' => 'Bintang Store Malang',
                'recipient_phone' => '081299887766',
                'origin' => 'Sidoarjo',
                'destination' => 'Malang',
                'weight' => 15.0,
                'service_type' => 'Express',
                'pickup_time' => now()->subHours(5),
                'price' => 225000,
                'payment_method' => 'Transfer Bank (Lunas)',
                'delay_risk' => 'Medium',
                'delay_probability' => 38,
                'status' => 'Dalam Perjalanan',
                'latitude' => -7.5624,
                'longitude' => 112.6961,
                'checkpoint' => 'Transit Hub Gempol (Menuju Malang)',
                'notes' => 'Pengiriman armada pickup.',
            ],
            [
                'tracking_number' => 'MRD-48209',
                'customer_id' => $customer->id,
                'courier_id' => $courier->id,
                'sender_name' => 'Budi Santoso',
                'recipient_name' => 'Toko Sentosa Mojokerto',
                'recipient_phone' => '087766554433',
                'origin' => 'Surabaya',
                'destination' => 'Mojokerto',
                'weight' => 8.0,
                'service_type' => 'Kargo',
                'pickup_time' => now()->subDay(),
                'price' => 48000,
                'payment_method' => 'QRIS (Lunas)',
                'delay_risk' => 'Low',
                'delay_probability' => 5,
                'status' => 'Terkirim',
                'latitude' => -7.4726,
                'longitude' => 112.4381,
                'checkpoint' => 'Paket Diterima di Tujuan (Mojokerto)',
                'proof_image' => null,
                'notes' => 'Telah diterima oleh Satpam kantor.',
            ],
            [
                'tracking_number' => 'MRD-48210',
                'customer_id' => $customer->id,
                'courier_id' => $courier2->id,
                'sender_name' => 'PT Nusantara Makmur',
                'recipient_name' => 'Klinik Sehat Bandung',
                'recipient_phone' => '089988776655',
                'origin' => 'Jakarta',
                'destination' => 'Bandung',
                'weight' => 22.0,
                'service_type' => 'Express',
                'pickup_time' => now()->subHours(8),
                'price' => 330000,
                'payment_method' => 'Invoice Tempo (Lunas)',
                'delay_risk' => 'High',
                'delay_probability' => 74,
                'status' => 'Dalam Perjalanan',
                'latitude' => -6.5950,
                'longitude' => 106.8166,
                'checkpoint' => 'Tol Cipularang KM 88 (Cuaca Hujan Lebat)',
                'notes' => 'Peringatan potensi keterlambatan cuaca buruk.',
            ],
        ];

        foreach ($orders as $order) {
            Order::updateOrCreate(['tracking_number' => $order['tracking_number']], $order);
        }
    }
}
