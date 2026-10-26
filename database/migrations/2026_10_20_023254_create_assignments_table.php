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
    Schema::create('assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
        $table->foreignId('courier_id')->constrained('users')->onDelete('cascade');
        $table->enum('status', ['assigned', 'accepted', 'completed', 'failed'])->default('assigned');
        $table->text('notes')->nullable(); // Alasan jika paket gagal dikirim
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};