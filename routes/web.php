<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\RuangController;
use Illuminate\Support\Facades\Route;


// =========================================================================
// 1. HALAMAN UTAMA (DASHBOARD)
// =========================================================================
Route::get('/', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('pages.home');


// =========================================================================
// 2. GROUP ROUTE KHUSUS USER LOGIN
// =========================================================================
Route::middleware('auth')->group(function () {

    // --- A. FITUR PROFIL (Bawaan Breeze) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/ruangan', [RuangController::class, 'index'])->name('ruangan.index');



    Route::get('/booking', [ReservasiController::class, 'create'])->name('reservasi.create');

    // 2. Form Review & Input Alasan
    Route::get('/booking/form', [ReservasiController::class, 'tampilkanForm'])->name('reservasi.form');

    // 3. Proses Simpan ke Database (POST)
    Route::post('/booking', [ReservasiController::class, 'store'])->name('reservasi.store');

    // 4. Halaman Status Hasil Pengajuan (Baru)
    Route::get('/booking/status', function () {
        if (!session('status')) {
            return redirect()->route('pages.home'); // Redirect ke dashboard jika diakses langsung tanpa submit
        }
        return view('pages.status-reservasi');
    })->name('reservasi.status');


    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');

    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');

    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/clear', [NotifikasiController::class, 'clearAll'])->name('notifikasi.clear');

    // --- Quick Booking Assistant (AI) ---
    Route::post('/assistant/tanya', [AssistantController::class, 'tanya'])
        ->middleware('throttle:15,1')
        ->name('assistant.tanya');
});

// =========================================================================
// 3. ROUTE AUTH (Breeze)
// =========================================================================
require __DIR__ . '/auth.php';
