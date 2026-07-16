<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as DefaultDashboard;
use App\Filament\Widgets\StatsOverview;

class Dashboard extends DefaultDashboard
{
    protected static ?string $title = 'Dasbor';

    // Gunakan view default filament jika custom view kamu masih bermasalah
    // Atau pastikan path-nya benar
    protected string $view = 'filament.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }
}
