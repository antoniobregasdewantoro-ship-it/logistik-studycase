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
            $table->string('name'); // Contoh: Hub Jakarta Pusat
            $table->string('city'); // Contoh: Jakarta
            $table->text('address'); // Alamat lengkap gudang
            $table->string('phone')->nullable();
            
            // Tambahan untuk kordinat posisi di gambar peta (dalam persentase %)
            $table->decimal('position_x', 5, 2)->nullable(); // Jarak dari kiri (left)
            $table->decimal('position_y', 5, 2)->nullable(); // Jarak dari atas (top)
            
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