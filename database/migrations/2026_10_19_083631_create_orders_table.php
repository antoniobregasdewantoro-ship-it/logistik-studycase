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
        $table->string('tracking_number')->unique(); // contoh: ORD-48213
        $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null');
        
        // Data Rute & Kargo
        $table->string('origin'); // contoh: Rotterdam, NL
        $table->string('destination'); // contoh: Chicago, US
        $table->decimal('weight', 8, 2); // dalam Kg
        $table->dateTime('pickup_time')->nullable();
        
        // Output Machine Learning (Harga & Risiko)
        $table->decimal('price', 12, 2); // Dynamic Price dari ML
        $table->enum('delay_risk', ['Low', 'Medium', 'High'])->default('Low'); // Prediksi Risiko
        $table->integer('delay_probability')->default(0); // contoh: 46%
        
        // Status Pengiriman
        $table->enum('status', ['Pending', 'In Transit', 'Out for Delivery', 'Delivered'])->default('Pending');
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