<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@meridian.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '08111111111'
        ]);

        // 2. Akun Kurir
        User::create([
            'name' => 'Budi Kurir',
            'email' => 'kurir@meridian.com',
            'password' => Hash::make('password123'),
            'role' => 'courier',
            'phone' => '08222222222'
        ]);

        // 3. Akun Customer
        User::create([
            'name' => 'Andi Pelanggan',
            'email' => 'customer@meridian.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '08333333333'
        ]);

        // 4. Data Cabang (Branch) & Koordinat Peta Indonesia
        $branches = [
            ['name' => 'Cabang Medan', 'city' => 'Medan', 'address' => 'Jl. Gatot Subroto No. 12', 'position_x' => 11, 'position_y' => 24],
            ['name' => 'Cabang Batam', 'city' => 'Batam', 'address' => 'Jl. Yos Sudarso No. 45', 'position_x' => 22, 'position_y' => 43],
            ['name' => 'Cabang Palembang', 'city' => 'Palembang', 'address' => 'Jl. Jend. Sudirman No. 88', 'position_x' => 22, 'position_y' => 60],
            ['name' => 'Cabang Utama Jakarta', 'city' => 'Jakarta', 'address' => 'Jl. HR Rasuna Said No. 10', 'position_x' => 29, 'position_y' => 78],
            ['name' => 'Cabang Bandung', 'city' => 'Bandung', 'address' => 'Jl. Asia Afrika No. 5', 'position_x' => 31, 'position_y' => 81],
            ['name' => 'Cabang Surabaya', 'city' => 'Surabaya', 'address' => 'Jl. Pemuda No. 18', 'position_x' => 41, 'position_y' => 81],
            ['name' => 'Cabang Denpasar', 'city' => 'Denpasar', 'address' => 'Jl. Teuku Umar No. 22', 'position_x' => 47, 'position_y' => 83],
            ['name' => 'Cabang Pontianak', 'city' => 'Pontianak', 'address' => 'Jl. Ahmad Yani No. 9', 'position_x' => 33, 'position_y' => 50],
            ['name' => 'Cabang Balikpapan', 'city' => 'Balikpapan', 'address' => 'Jl. Sudirman No. 101', 'position_x' => 46, 'position_y' => 50],
            ['name' => 'Cabang Makassar', 'city' => 'Makassar', 'address' => 'Jl. Ujung Pandang No. 3', 'position_x' => 54, 'position_y' => 66],
            ['name' => 'Cabang Manado', 'city' => 'Manado', 'address' => 'Jl. Sam Ratulangi No. 15', 'position_x' => 61, 'position_y' => 28],
            ['name' => 'Cabang Ambon', 'city' => 'Ambon', 'address' => 'Jl. Pattimura No. 7', 'position_x' => 72, 'position_y' => 60],
            ['name' => 'Cabang Jayapura', 'city' => 'Jayapura', 'address' => 'Jl. Ahmad Yani No. 50', 'position_x' => 94, 'position_y' => 54],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}