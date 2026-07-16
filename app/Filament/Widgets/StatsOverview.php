<?php

namespace App\Filament\Widgets;

use App\Models\Kelas;
use App\Models\Reservasi;
use App\StatusReservasi;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // 1. Hitung Reservasi Pending
        $pendingCount = Reservasi::where('status', StatusReservasi::PENDING)->count();

        // 2. Total Reservasi Hari Ini
        $todayCount = Reservasi::whereDate('tanggal', Carbon::today())->count();

        // 3. Jumlah Kelas yang Terpakai/Penuh Hari Ini
        // Menghitung kelas unik yang memiliki reservasi disetujui pada jam ini/hari ini
        $kelasPenuhCount = Reservasi::whereDate('tanggal', Carbon::today())
            ->where('status', StatusReservasi::DITERIMA)
            ->distinct('id_kelas')
            ->count();

        return [
            Stat::make('Reservasi Pending', $pendingCount)
                ->description('Perlu konfirmasi admin')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Reservasi Hari Ini', $todayCount)
                ->description('Total semua status')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Kelas Tidak Tersedia', $kelasPenuhCount)
                ->description('Kelas yang sedang digunakan')
                ->descriptionIcon('heroicon-m-no-symbol')
                ->color('danger'),
        ];
    }
}
