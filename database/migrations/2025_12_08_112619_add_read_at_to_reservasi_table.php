<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Kolom ini dipakai oleh fitur notifikasi (NotifikasiController,
     * AppServiceProvider::boot, dan komponen notification-popup) untuk
     * menandai kapan sebuah reservasi "dibaca" oleh user, tapi kolomnya
     * belum pernah dibuat di migration awal (2025_12_08_112618_reservasi_table).
     * Tanpa ini, semua halaman akan error karena AppServiceProvider
     * menjalankan query `whereNull('read_at')` di setiap request.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('reservasi', 'read_at')) {
            Schema::table('reservasi', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('status');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }
};
