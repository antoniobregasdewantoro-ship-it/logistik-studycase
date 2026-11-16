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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('street_address')->nullable()->after('destination');
            $table->string('rt_rw')->nullable()->after('street_address');
            $table->string('village')->nullable()->after('rt_rw'); // Desa / Kelurahan
            $table->string('district')->nullable()->after('village'); // Kecamatan
            $table->string('postal_code')->nullable()->after('district');
            $table->string('pdf_report_path')->nullable()->after('qr_code_path');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('google_id');
            $table->string('courier_status')->default('online')->after('role'); // online / offline
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'street_address',
                'rt_rw',
                'village',
                'district',
                'postal_code',
                'pdf_report_path',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'avatar',
                'courier_status',
            ]);
        });
    }
};
