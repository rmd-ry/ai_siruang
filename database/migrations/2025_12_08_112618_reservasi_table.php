<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_reservasi_table.php
    public function up()
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_user')->constrained('users')->onDelete('restrict');
            $table->foreignId('id_kelas')->constrained('kelas')->onDelete('restrict');

            $table->time('jam_mulai');   // 09:00:00
            $table->time('jam_selesai'); // 11:00:00

            $table->date('tanggal');
            $table->text('alasan');

            // Status Reservation
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
