<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Hub Surabaya
            $table->string('city'); // Contoh: Surabaya
            $table->text('address'); // Alamat lengkap gudang
            $table->string('phone')->nullable();

            // Tambahan untuk koordinat posisi di gambar peta (persentase %) & GPS Leaflet
            $table->decimal('position_x', 5, 2)->nullable(); // Jarak dari kiri (%)
            $table->decimal('position_y', 5, 2)->nullable(); // Jarak dari atas (%)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
