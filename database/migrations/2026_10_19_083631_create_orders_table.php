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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique(); // Contoh: MRD-48207
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null');

            // Data Pengirim & Penerima
            $table->string('sender_name')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();

            // Data Rute & Kargo
            $table->string('origin'); // Contoh: Sidoarjo
            $table->string('destination'); // Contoh: Surabaya
            $table->decimal('weight', 8, 2); // dalam Kg
            $table->string('service_type')->default('Reguler'); // Reguler, Express, Kargo, Same Day
            $table->dateTime('pickup_time')->nullable();

            // Tarif & Pembayaran
            $table->decimal('price', 12, 2); // Dynamic Price
            $table->string('payment_method')->default('QRIS (Lunas)');
            $table->enum('delay_risk', ['Low', 'Medium', 'High'])->default('Low'); // Prediksi Risiko
            $table->integer('delay_probability')->default(15); // Contoh: 15%

            // Status Pengiriman & Checkpoint Live Tracking
            $table->string('status')->default('Verifikasi'); // Verifikasi, Dalam Perjalanan, Terkirim
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('checkpoint')->nullable(); // Drop Point Sidoarjo (Menunggu Verifikasi)
            $table->string('proof_image')->nullable(); // Bukti Foto Pengiriman (< 2MB)
            $table->text('notes')->nullable();
            $table->text('qr_code_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
